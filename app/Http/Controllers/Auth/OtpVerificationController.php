<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\PhoneTokenService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class OtpVerificationController extends Controller
{
    protected PhoneTokenService $phoneTokenService;

    public function __construct(PhoneTokenService $phoneTokenService)
    {
        $this->phoneTokenService = $phoneTokenService;
    }

    /**
     * Handle the request to send an OTP for password reset.
     * This method initiates the OTP generation and sending process.
     *
     * @param Request $request
     * @return RedirectResponse
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'phone' => ['required', 'string', 'regex:/^\+965[569]\d{7}$/'],
        ]);

        $phone = $request->phone;

        try {
            $this->phoneTokenService->generateAndStoreOtp($phone);
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }

        return redirect()->route('password.otp.verify.form', ['phone' => $phone])
                         ->with('success', 'OTP sent successfully.');
    }

    public function verify(Request $request)
    {
        $request->validate([
            'phone' => ['required', 'string', 'regex:/^\+965[569]\d{7}$/'],
            'otp'   => ['required', 'array', 'size:4'],
        ]);

        $phone = $request->phone;
        $otpArray = $request->otp;
        
        $otp = implode('', $otpArray);

        if (!is_numeric($otp)) {
            throw ValidationException::withMessages([
                'otp' => 'OTP must contain only numbers.',
            ]);
        }

        if (strlen($otp) !== 4) {
            throw ValidationException::withMessages([
                'otp' => 'OTP must be exactly 4 digits.',
            ]);
        }

        $this->phoneTokenService->verifyOtp($phone, $otp);

        $user = User::where('phone', $phone)->firstOrFail();
        $longResetToken = $this->phoneTokenService->createToken($user);

        return redirect()->route('password.reset', [
            'token' => $longResetToken, 
            'phone' => $phone
        ])->with('success', 'OTP verified successfully. Please set your new password.');
    }

    public function resend(Request $request)
    {
        $request->validate([
            'phone' => ['required', 'string', 'regex:/^\+965[569]\d{7}$/'],
        ]);

        $phone = $request->phone;

        try {
            $this->phoneTokenService->generateAndStoreOtp($phone);
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }
        
        return redirect()->route('password.otp.verify.form', ['phone' => $phone])->with('success', 'A new OTP has been sent to your phone.');
    }

    public function showOtpForm(Request $request)
    {
        $phone = $request->phone ?? session('phone');
        
        if (!$phone) {
            return redirect()->route('password.request')->withErrors([
                'phone' => 'Phone number is required.'
            ]);
        }

        return view('frontend.auth.verify-otp', [
            'phone' => $phone
        ]);
    }
}