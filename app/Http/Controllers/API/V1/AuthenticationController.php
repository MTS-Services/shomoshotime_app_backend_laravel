<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\V1\Auth\LoginRequest;
use App\Http\Requests\API\V1\Auth\OTPRequest;
use App\Http\Requests\API\V1\Auth\RegistrationRequest;
use App\Http\Requests\API\V1\Auth\PasswordRequest;
use App\Models\User;
use App\Models\UserDevice;
use App\Services\AuthenticationService;
use App\Services\FirebaseNotificationService;
use App\Services\PhoneTokenService;
use App\Services\SMS\DezSmsService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Throwable;
use Illuminate\Support\Str;

class AuthenticationController extends Controller
{
    protected AuthenticationService $authService;
    protected PhoneTokenService $phoneTokenService;
    protected FirebaseNotificationService $firebaseNotificationService;
    protected DezSmsService $dezSmsService;

    public function __construct(AuthenticationService $authService, PhoneTokenService $phoneTokenService, FirebaseNotificationService $firebaseNotificationService, DezSmsService $dezSmsService)
    {
        $this->authService = $authService;
        $this->phoneTokenService = $phoneTokenService;
        $this->firebaseNotificationService = $firebaseNotificationService;
        $this->dezSmsService = $dezSmsService;
    }

    /**
     * Handle user registration and store initial device information.
     */
    public function register(RegistrationRequest $request): JsonResponse
    {
        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'last_login_at' => Carbon::now(),
                'fcm_token' => $request->fcm_token,
            ]);

            // Store the initial device information for the new user.
            UserDevice::create([
                'user_id' => $user->id,
                'device_token' => Str::uuid()->toString(), // Generate a unique token for the device
                'user_agent' => $request->header('User-Agent'),
                'ip_address' => $request->ip(),
                'last_login_at' => Carbon::now(),
            ]);

            $tokenResult = $user->createToken('Personal Access Token');
            $token = $tokenResult->accessToken;
            $tokenModel = $tokenResult->token;
            $expiresInSeconds = Carbon::now()->diffInSeconds($tokenModel->expires_at);

            $this->authService->generateOtp($user);

            $this->dezSmsService->sendSms($user->phone, 'Your OTP is' . $user->otp . '.');

            $message = str_replace(
                '{phone_ending}',
                substr($user->phone, -2),
                __('auth.otp_sent')
            );

            $data = [
                'phone_verified' => $message,
                'otp' => $user->otp,
                'token' => $token,
                'token_type' => 'Bearer',
                'expires_in' => $expiresInSeconds,
                'expires_at' => $tokenModel->expires_at->toDateTimeString(),
            ];

            if ($user->fcm_token != null) {
                $this->firebaseNotificationService->sendToDevice($user->fcm_token,  'Registration Successful', 'You have successfully registered.');
            }

            return sendResponse(true, __('registration.success'), $data, Response::HTTP_CREATED);
        } catch (Throwable $error) {
            Log::error($error);
            return sendResponse(false, $error->getMessage(), null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Handle user login and manage device information.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $user = User::where('phone', $request->phone)->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                throw ValidationException::withMessages([
                    'phone' => [__('auth.invalid_credentials')],
                ]);
            }

            if ($user->status == User::STATUS_SUSPENDED) {
                throw ValidationException::withMessages([
                    'phone' => [__('auth.account_suspended')],
                ]);
            }

            if ($user->is_admin == User::ADMIN) {
                throw ValidationException::withMessages([
                    'phone' => [__('auth.invalid_credentials')],
                ]);
            }

            // Single update for user data
            $user->update([
                'last_login_at' => Carbon::now(),
                'fcm_token' => $request->fcm_token,
            ]);

            UserDevice::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'device_token' => Str::uuid()->toString(),
                    'user_agent' => $request->header('User-Agent'),
                    'ip_address' => $request->ip(),
                    'last_login_at' => Carbon::now(),
                ]
            );

            // This will create a new token and invalidate the old one.
            $tokenResult = $user->createToken('API Token');
            $token = $tokenResult->accessToken;
            $tokenModel = $tokenResult->token;
            $expiresInSeconds = Carbon::now()->diffInSeconds($tokenModel->expires_at);

            $verified = $this->authService->isVerified($user);
            if (!$verified) {
                $this->authService->generateOtp($user);
                $this->dezSmsService->sendSms($user->phone, 'Your OTP is' . $user->otp . '.');
            }

            $phoneVerifiedMessage = $verified
                ? __('auth.phone_verified')
                : str_replace(
                    '{phone_ending}',
                    substr($user->phone, -2),
                    __('auth.phone_unverified')
                );

            $data = [
                'phone_verified' => $phoneVerifiedMessage,
                'otp' => $verified ? null : $user->otp,
                'token' => $token,
                'token_type' => 'Bearer',
                'expires_in' => $expiresInSeconds,
                'expires_at' => $tokenModel->expires_at->toDateTimeString(),
            ];

            // Send notification only once
            if ($user->fcm_token != null) {
                $this->firebaseNotificationService->sendToDevice(
                    $user->fcm_token,
                    'Login Successful',
                    'You have logged in successfully.'
                );
            }

            return sendResponse(true, __('login.success'), $data, Response::HTTP_OK);
        } catch (Throwable $error) {
            Log::error($error);
            return sendResponse(false, $error->getMessage(), null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Log the user out of the application.
     */
    public function logout(Request $request): JsonResponse
    {
        try {
            if ($request->user() && $request->user()->token()) {
                // Revoke the current access token.
                $request->user()->token()->revoke();
                // Optionally delete the user device record to enforce a new login on next attempt
                $request->user()->userDevice()->delete();
                return sendResponse(true, __('logout.success'), null, Response::HTTP_OK);
            }
            return sendResponse(false, __('auth.unauthenticated'), null, Response::HTTP_UNAUTHORIZED);
        } catch (Throwable $error) {
            Log::error($error);
            return sendResponse(false, $error->getMessage(), null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Verify the OTP provided by the user.
     */
    public function verifyOTP(OTPRequest $request): JsonResponse
    {
        try {
            $user = $request->user();
            $this->authService->verifyOtp($user, $request->otp);
            $user->update(['phone_verified_at' => Carbon::now()]);

            if ($user->fcm_token != null) {
                $this->firebaseNotificationService->sendToDevice($user->fcm_token, 'OTP Verified', 'Your OTP has been verified successfully.');
            }

            return sendResponse(true, __('otp.verified'), null, Response::HTTP_OK);
        } catch (Throwable $error) {
            Log::error($error);
            return sendResponse(false, $error->getMessage(), null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Resend a new OTP to the user's phone.
     */
    public function resendOTP(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $this->authService->resendOtp($user);
            $this->dezSmsService->sendSms($user->phone, 'Your OTP is' . $user->otp . '.');
            $message = __('otp.resent', ['phone_ending' => substr($user->phone, -2)]);
            $otp = $user->otp;

            return sendResponse(true, $message, $otp, Response::HTTP_OK);
        } catch (Throwable $error) {
            Log::error($error);
            return sendResponse(false, $error->getMessage(), null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Handle password change for an authenticated user.
     */
    public function changePassword(PasswordRequest $request): JsonResponse
    {
        try {
            $user = request()->user();
            $this->authService->verifyPassword($user, $request->old_password);
            $this->authService->resetPassword($user, $request->password);

            if ($user->fcm_token != null) {
                $this->firebaseNotificationService->sendToDevice($user->fcm_token, 'Password Changed', 'Your password has been changed successfully.');
            }

            return sendResponse(true, __('password.changed'), null, Response::HTTP_OK);
        } catch (Throwable $error) {
            Log::error($error);
            return sendResponse(false, $error->getMessage(), null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Handle the forgot password process by sending an OTP.
     */
    public function forgotPassword(Request $request): JsonResponse
    {
        try {
            $user = $this->authService->verifyPhone($request->phone, User::NOT_ADMIN);
            $this->authService->generateOtp($user);
            $this->dezSmsService->sendSms($user->phone, 'Your OTP is' . $user->otp . '.');
            $token = $this->phoneTokenService->createToken($user);
            $message = __('password.reset_otp_sent', ['phone_ending' => substr($user->phone, -2)]);
            return sendResponse(true, $message, ['token' => $token, 'otp' => $user->otp], Response::HTTP_OK);
        } catch (Throwable $error) {
            Log::error($error);
            return sendResponse(false, $error->getMessage(), null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Resend an OTP for a forgotten password request.
     */
    public function forgotResendOTP(Request $request): JsonResponse
    {
        try {
            $user = $this->authService->verifyPhone($request->phone, User::NOT_ADMIN);
            $verify = $this->phoneTokenService->verifyToken($user, $request->token);
            if (!$verify) {
                throw new \Exception(__('auth.invalid_token'));
            }
            $this->authService->resendOtp($user);
            $this->dezSmsService->sendSms($user->phone, 'Your OTP is' . $user->otp . '.');
            $message = __('otp.resent', ['phone_ending' => substr($user->phone, -2)]);
            return sendResponse(true, $message, ['token' => $request->token, 'otp' => $user->otp], Response::HTTP_OK);
        } catch (Throwable $error) {
            Log::error($error);
            return sendResponse(false, $error->getMessage(), null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Verify the OTP for a forgotten password request.
     */
    public function forgotVerifyOTP(OTPRequest $request): JsonResponse
    {
        try {
            $user = $this->authService->verifyPhone($request->phone, User::NOT_ADMIN);
            $verify = $this->phoneTokenService->verifyToken($user, $request->token);
            if (!$verify) {
                throw new \Exception(__('auth.invalid_token'));
            }
            return sendResponse(true, __('otp.verified'), ['token' => $request->token], Response::HTTP_OK);
        } catch (Throwable $error) {
            Log::error($error);
            return sendResponse(false, $error->getMessage(), null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Reset the user's password using the provided token.
     */
    public function ResetPassword(PasswordRequest $request): JsonResponse
    {
        try {
            $user = $this->authService->verifyPhone($request->phone, User::NOT_ADMIN);
            $this->phoneTokenService->verifyToken($user, $request->token);
            $this->authService->resetPassword($user, $request->password);
            $this->phoneTokenService->deleteToken($user);

            if ($user->fcm_token != null) {
                $this->firebaseNotificationService->sendToDevice($user->fcm_token, 'Password Reset', 'Your password has been reset successfully.');
            }

            return sendResponse(true, __('password.reset_success'), null, Response::HTTP_OK);
        } catch (Throwable $error) {
            Log::error($error);
            return sendResponse(false, $error->getMessage(), null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
