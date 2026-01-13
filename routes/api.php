<?php

use App\Http\Controllers\API\V1\AuthenticationController;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Route;

Broadcast::routes(['middleware' => ['auth:api']]);

// Public routes
Route::controller(AuthenticationController::class)->name('v1.auth.')->prefix('v1/auth')->group(function () {
    Route::post('/register', 'register')->name('register');
    Route::post('/login', 'login')->name('login');

    Route::post('/forgot-password', 'forgotPassword')->name('forgot-password');
    Route::post('/reset-password', 'ResetPassword')->name('reset-password');

    Route::post('/forgot-verify-otp', 'forgotVerifyOTP')->name('forgot-verify-otp');
    Route::post('/forgot-resend-otp', 'forgotResendOTP')->name('forgot-resend-otp');


});

// Protected routes
Route::controller(AuthenticationController::class)->name('v1.auth.')->prefix('v1/auth')->middleware('auth:api')->group(function () {
    Route::delete('/logout', 'logout')->name('logout');
    Route::post('/verify-otp', 'verifyOTP')->name('verify-otp');
    Route::post('/resend-otp', 'resendOTP')->name('resend-otp')->middleware('auth:api');
    Route::post('/change-password', 'changePassword')->name('change-password');
});
