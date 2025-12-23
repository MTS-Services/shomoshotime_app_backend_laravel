<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class EmailTokenService
{
    protected $table = 'password_reset_tokens';

    protected $longTokenExpiryMinutes = 60; // Token for password reset valid for 60 minutes

    protected $otpExpiryMinutes = 2; // OTP valid for 2 minutes

    protected $otpResendCooldownSeconds = 60; // Cooldown for OTP resend in seconds

    public function generateAndStoreOtp(string $email): string
    {
        $existing = DB::table($this->table)
            ->where('email', $email)
            ->orderByDesc('created_at')
            ->first();

        if ($existing) {
            $secondsSinceLastOtp = Carbon::parse($existing->created_at)->diffInSeconds(now());

            if ($secondsSinceLastOtp < $this->otpResendCooldownSeconds) {
                $waitTime = $this->otpResendCooldownSeconds - $secondsSinceLastOtp;
                throw ValidationException::withMessages([
                    'email' => "Please wait {$waitTime} seconds before requesting another OTP.",
                ]);
            }
        }

        $otp = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);

        DB::table($this->table)->updateOrInsert(
            ['email' => $email],
            ['token' => $otp, 'created_at' => now()]
        );

        Log::info("Password reset OTP for {$email}: {$otp}");

        return $otp;
    }

    public function verifyOtp(string $email, string $otp): bool
    {
        $record = DB::table($this->table)
            ->where('email', $email)
            ->where('token', $otp)
            ->first();

        if (!$record) {
            throw ValidationException::withMessages([
                'otp' => 'Invalid OTP or email.',
            ]);
        }

        $expiresAt = Carbon::parse($record->created_at)->addMinutes($this->otpExpiryMinutes);

        if (now()->gt($expiresAt)) {
            throw ValidationException::withMessages([
                'otp' => 'OTP has expired. Please request a new one.',
            ]);
        }

        return true;
    }

    public function createToken(User $user): string
    {
        $this->deleteExistingToken($user);

        $token = Str::random(60);

        DB::table($this->table)->insert([
            'email' => $user->email,
            'token' => $token,
            'created_at' => Carbon::now(),
        ]);

        return $token;
    }

    public function verifyToken(User $user, string $token): bool
    {
        $record = DB::table($this->table)
            ->where('email', $user->email)
            ->where('token', $token)
            ->first();

        if (!$record) {
            return false;
        }

        if (Carbon::parse($record->created_at)->addMinutes($this->longTokenExpiryMinutes)->isPast()) {
            $this->deleteExistingToken($user);
            return false;
        }

        return true;
    }

    public function deleteToken(User $user): int
    {
        return $this->deleteExistingToken($user);
    }

    protected function deleteExistingToken(User $user): int
    {
        return DB::table($this->table)->where('email', $user->email)->delete();
    }
}
