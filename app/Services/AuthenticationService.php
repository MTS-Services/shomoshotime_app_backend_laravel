<?php

namespace App\Services;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Lang;

class AuthenticationService
{

    protected $otpExpiresAfter = 5; // in minutes
    protected $maxAttempts = 500;
    protected $blockMinutes = 30;

    /**
     * Generates a new OTP for the user and "sends" it (logs it for simulation).
     * In a real application, this would send an SMS via a third-party service.
     *
     * @param User $user The user for whom to generate the OTP.
     * @return User The updated user model.
     */
    public function generateOtp(User $user): User
    {
        $otp = rand(1000, 9999);
        $expiresAt = Carbon::now()->addMinutes($this->otpExpiresAfter);

        $user->update([
            'otp' => $otp,
            'otp_expires_at' => $expiresAt,
            'otp_sent_at' => Carbon::now()
        ]);


        $user->refresh();

        Log::info("OTP for user id: {$user->id}, Name: {$user->name}, Phone Number: ({$user->phone}) is: {$user->otp}");

        return $user;
    }

    /**
     * Verifies the provided OTP against the user's stored OTP and expiration.
     *
     * @param User $user The user attempting to verify.
     * @param string $otp The OTP provided by the user.
     * @return bool True if OTP is valid and verified, false otherwise.
     */
    public function verifyOtp(User $user, string $otp): bool
    {
        $record = User::where('id', $user->id)
            ->where('otp', $otp)
            ->where('otp_expires_at', '>=', Carbon::now())
            ->first();

        if (!$record) {
            throw new \Exception(__('otp.invalid'));
        }

        $record->update(['otp' => null, 'otp_expires_at' => null, 'otp_sent_at' => null]);

        return true;
    }

    /**
     * Checks if the user's phone number is verified.
     *
     * @param User $user The user to check.
     * @return bool True if phone_verified_at is not null, false otherwise.
     */
    public function isVerified(User $user): bool
    {
        return $user->phone_verified_at !== null;
    }

    /**
     * Resends an OTP to the user, with rate limiting.
     *
     * @param User $user The user to resend OTP to.
     * @return array An array indicating if blocked and a message.
     */
    public function resendOtp(User $user)
    {
        $key = "otp_resend_attempts:{$user->id}";
        $blockedKey = "otp_blocked:{$user->id}";

        if (Cache::has($blockedKey)) {
            throw new \Exception(__('otp.resend_limit_exceeded', ['block_minutes' => $this->blockMinutes]));
        }

        $attempts = Cache::get($key, 0) + 1;
        Cache::put($key, $attempts, Carbon::now()->addMinutes($this->blockMinutes));

        if ($attempts > $this->maxAttempts) {
            Cache::put($blockedKey, true, Carbon::now()->addMinutes($this->blockMinutes));
            Cache::forget($key);
            throw new \Exception(__('otp.resend_limit_exceeded', ['block_minutes' => $this->blockMinutes]));
        }
        $this->generateOtp($user);
    }


    public function verifyPhone(string $phone, $expectedUserType = null): User
    {
        $userQuery = User::where('phone', $phone);

        if ($expectedUserType !== null) {
            if ($expectedUserType === User::NOT_ADMIN || $expectedUserType === User::ADMIN) {
                $userQuery->where('is_admin', (bool)$expectedUserType);
            } else if ($expectedUserType === User::USER_TYPE_INDIVIDUAL) {
                $userQuery->where('user_type', User::USER_TYPE_INDIVIDUAL);
            } else if ($expectedUserType === User::USER_TYPE_AGENT) {
                $userQuery->where('user_type', User::USER_TYPE_AGENT);
            } else {
                throw new \Exception(__('user.invalid_type'));
            }
        }

        $user = $userQuery->first();

        if (!$user) {
            $message = __('user.not_found');
            if ($expectedUserType === User::ADMIN) {
                $message = __('user.admin_not_found');
            }
            throw new ModelNotFoundException($message);
        }

        return $user;
    }

    public function verifyPassword(User $user, string $password): void
    {
        if (!Hash::check($password, $user->password)) {
            throw new \Exception(__('password.invalid'));
        }
    }

    public function resetPassword(User $user, string $password): void
    {
        $user->update(['password' => bcrypt($password)]);
    }
}
