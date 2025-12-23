<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class PhoneTokenService
{
    protected $table = 'password_reset_tokens';

    protected $longTokenExpiryMinutes = 60; // Token for password reset valid for 60 minutes

    protected $otpExpiryMinutes = 2; // OTP valid for 2 minutes

    protected $otpResendCooldownSeconds = 60; // Cooldown for OTP resend in seconds

    public function generateAndStoreOtp(string $phone): string
    {
        $existing = DB::table($this->table)
            ->where('phone', $phone)
            ->orderByDesc('created_at')
            ->first();

        if ($existing) {
            $secondsSinceLastOtp = Carbon::parse($existing->created_at)->diffInSeconds(now());

            if ($secondsSinceLastOtp < $this->otpResendCooldownSeconds) {
                $waitTime = $this->otpResendCooldownSeconds - $secondsSinceLastOtp;
                throw ValidationException::withMessages([
                    'phone' => "Please wait {$waitTime} seconds before requesting another OTP.",
                ]);
            }
        }

        $otp = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);

        DB::table($this->table)->updateOrInsert(
            ['phone' => $phone],
            ['token' => $otp, 'created_at' => now()]
        );

        Log::info("Password reset OTP for {$phone}: {$otp}");

        return $otp;
    }

    public function verifyOtp(string $phone, string $otp): bool
    {
        $record = DB::table($this->table)
            ->where('phone', $phone)
            ->where('token', $otp)
            ->first();

        if (!$record) {
            throw ValidationException::withMessages([
                'otp' => 'Invalid OTP or phone number.',
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
            'phone' => $user->phone,
            'token' => $token,
            'created_at' => Carbon::now(),
        ]);

        return $token;
    }

    public function verifyToken(User $user, string $token): bool
    {
        $record = DB::table($this->table)
            ->where('phone', $user->phone)
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
        return DB::table($this->table)->where('phone', $user->phone)->delete();
    }
}