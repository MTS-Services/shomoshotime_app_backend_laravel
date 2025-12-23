<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AuthenticationService;
use App\Services\PhoneTokenService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Throwable;

class PasswordResetLinkController extends Controller
{

    public function create(): View
    {
        return view('frontend.auth.forgot-password');
    }

    /**
     * Handle incoming password reset request via phone.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'phone' => ['required', 'string', 'regex:/^\+965[569]\d{7}$/'],
        ]);

        $otp = rand(1000, 9999);
        $phone = $request->phone;

        DB::table('password_reset_tokens')->where('phone', $phone)->delete();
        DB::table('password_reset_tokens')->insert([
            'phone' => $phone,
            'token' => $otp,
            'created_at' => now(),
        ]);
        // DB::table('password_reset_tokens')->updateOrInsert(
        //     ['phone' => $phone],  
        //     ['token' => $otp, 'created_at' => now()] 
        // );

        Log::info("Password reset OTP for {$phone}: $otp");

        return redirect()->route('password.otp.verify.form', ['phone' => $phone])->with('success', 'OTP sent successfully.');
    }
}
