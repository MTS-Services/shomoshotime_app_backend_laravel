<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class NewPasswordController extends Controller

{
    /**
     * Display the password reset view.
     */
    public function create(Request $request)
    {
        return view('frontend.auth.reset-password', [
            'token' => $request->token, // This should be the 4-digit OTP
            'phone' => $request->phone,
        ]);
    }

    /**
     * Handle an incoming new password request.
     */
    public function store(Request $request)
    {
     
        $request->validate([
            'token'    => ['required', 'string', 'size:60'],
            'phone'    => ['required', 'string', 'regex:/^\+965[569]\d{7}$/'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $otp = $request->token;
        $phone = $request->phone;

        $record = DB::table('password_reset_tokens')
            ->where('phone', $phone)
            ->where('token', $otp)
            ->first();

        if (!$record) {
            throw ValidationException::withMessages([
                'token' => 'This OTP is invalid or has expired.',
            ]);
        }

        $user = User::where('phone', $phone)->first();

        if (!$user) {
            throw ValidationException::withMessages([
                'phone' => 'We couldn\'t find a user with that phone number.',
            ]);
        }

        $user->update([
            'password' => Hash::make($request->password)
        ]);

        DB::table('password_reset_tokens')
            ->where('phone', $phone)
            ->delete();

        return redirect()->route('login')
            ->with('success', 'Your password has been reset successfully!');
    }
}
