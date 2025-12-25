<?php

namespace App\Http\Middleware;

use App\Services\AuthenticationService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Symfony\Component\HttpFoundation\Response;

class EmailVerified
{
    protected AuthenticationService $authService;

    public function __construct(AuthenticationService $authService)
    {
        $this->authService = $authService;
    }

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if (!$user) {
            if ($request->expectsJson()) {
                return sendResponse(false, __('auth.unauthenticated'), null, Response::HTTP_UNAUTHORIZED);
            }
            if ($request->is('api/*')) {
                return sendResponse(false, __('auth.unauthenticated'), null, Response::HTTP_UNAUTHORIZED);
            }

            session()->flash('error', __('auth.unauthenticated'));
            return Redirect::guest(route('login'));
        }


        if (!$this->authService->isVerified($user)) {
            $this->authService->generateOtp($user);

            $message = "Your email is not verified. A verification code has been sent to your email ending in ***" . substr($user->email, -2) . ".";
            if ($request->expectsJson()) {
                return response()->json(['message' => $message, 'status' => 'Not verified'], Response::HTTP_UNAUTHORIZED);
            }
            if ($request->is('api/*')) {
                return sendResponse(false, $message, null, Response::HTTP_UNAUTHORIZED);
            }
            session()->flash('error', $message);
            return Redirect::route('verification.notice');
        }

        return $next($request);
    }
}
