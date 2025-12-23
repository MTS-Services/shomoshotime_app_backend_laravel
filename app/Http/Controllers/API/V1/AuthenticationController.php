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
use App\Services\EmailTokenService;
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
    protected EmailTokenService $emailTokenService;
    protected FirebaseNotificationService $firebaseNotificationService;

    public function __construct(AuthenticationService $authService, EmailTokenService $emailTokenService, FirebaseNotificationService $firebaseNotificationService)
    {
        $this->authService = $authService;
        $this->emailTokenService = $emailTokenService;
        $this->firebaseNotificationService = $firebaseNotificationService;
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
                'password' => Hash::make($request->password),
                'last_login_at' => Carbon::now(),
                'fcm_token' => $request->fcm_token,
            ]);

            // Store the initial device information for the new user.
            UserDevice::create([
                'user_id' => $user->id,
                'device_token' => $request->device_token ?? Str::uuid()->toString(), // Generate a unique token for the device
                'user_agent' => $request->header('User-Agent'),
                'ip_address' => $request->ip(),
                'last_login_at' => Carbon::now(),
            ]);

            $tokenResult = $user->createToken('Personal Access Token');
            $token = $tokenResult->accessToken;
            $tokenModel = $tokenResult->token;
            $expiresInSeconds = Carbon::now()->diffInSeconds($tokenModel->expires_at);

            $this->authService->generateOtp($user);


            $message = "User registered successfully. Please verify your email. A one-time password (OTP) has been sent to your email ending in ***" . substr($user->email, -2) . ".";

            $data = [
                'message' => $message,
                'otp' => $user->otp,
                'token' => $token,
                'token_type' => 'Bearer',
                'expires_in' => $expiresInSeconds,
                'expires_at' => $tokenModel->expires_at->toDateTimeString(),
            ];

            if ($user->fcm_token != null) {
                $this->firebaseNotificationService->sendToDevice($user->fcm_token, 'Registration Successful', 'You have successfully registered.');
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
            $user = User::where('email', $request->email)->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                throw ValidationException::withMessages([
                    'email' => ['The provided email or password is incorrect.'],
                ]);
            }

            if ($user->status == User::STATUS_SUSPENDED) {
                throw ValidationException::withMessages([
                    'email' => ['Your account has been suspended. Please contact support.'],
                ]);
            }

            if ($user->is_admin == User::ADMIN) {
                throw ValidationException::withMessages([
                    'email' => ['You are not allowed to login as admin.'],
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
            }

            $message = $verified
                ? "User logged in successfully."
                : "User logged in successfully. Please verify your email. A one-time password (OTP) has been sent to your email ending in ***" . substr($user->email, -2) . ".";

            $data = [
                'message' => $message,
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

            return sendResponse(true, 'Logged in successfully', $data, Response::HTTP_OK);
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
                return sendResponse(true, __('Logged out successfully'), null, Response::HTTP_OK);
            }
            return sendResponse(false, __('Unauthenticated'), null, Response::HTTP_UNAUTHORIZED);
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
            $user->update(['email_verified_at' => Carbon::now()]);

            if ($user->fcm_token != null) {
                $this->firebaseNotificationService->sendToDevice($user->fcm_token, 'OTP Verified', 'Your OTP has been verified successfully.');
            }

            return sendResponse(true, __('OTP verified'), null, Response::HTTP_OK);
        } catch (Throwable $error) {
            Log::error($error);
            return sendResponse(false, $error->getMessage(), null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Resend a new OTP to the user's email.
     */
    public function resendOTP(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $this->authService->resendOtp($user);
            $message = 'A new one-time password (OTP) has been sent to your email ending in ***' . substr($user->email, -2) . '.';
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

            return sendResponse(true, __('Password changed successfully'), null, Response::HTTP_OK);
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
            $user = $this->authService->verifyEmail($request->email);
            $this->authService->generateOtp($user);
            $token = $this->emailTokenService->createToken($user);
            $message = "An OTP has been sent to your email ending in ***" . substr($user->email, -2) . ".";
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
            $user = $this->authService->verifyEmail($request->email);
            $verify = $this->emailTokenService->verifyToken($user, $request->token);
            if (!$verify) {
                throw new \Exception("Invalid token");
            }
            $this->authService->resendOtp($user);
            $message = "A new one-time password (OTP) has been sent to your email ending in ***" . substr($user->email, -2) . ".";
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
            $user = $this->authService->verifyEmail($request->email);
            $verify = $this->emailTokenService->verifyToken($user, $request->token);
            if (!$verify) {
                throw new \Exception("Invalid token");
            }
            return sendResponse(true, "OTP verified", ['token' => $request->token], Response::HTTP_OK);
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
            $user = $this->authService->verifyEmail($request->email);
            $this->emailTokenService->verifyToken($user, $request->token);
            $this->authService->resetPassword($user, $request->password);
            $this->emailTokenService->deleteToken($user);

            if ($user->fcm_token != null) {
                $this->firebaseNotificationService->sendToDevice($user->fcm_token, 'Password Reset', 'Your password has been reset successfully.');
            }

            return sendResponse(true, "Password reset successfully", null, Response::HTTP_OK);
        } catch (Throwable $error) {
            Log::error($error);
            return sendResponse(false, $error->getMessage(), null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
