<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class UserMidelware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::guard('api')->check()) {
            return sendResponse(false, 'Unauthorized', null, Response::HTTP_UNAUTHORIZED);
        }
        if ($request->user()->is_admin == 1) {
            return sendResponse(false, 'User access required', null, Response::HTTP_UNAUTHORIZED);
        }
        return $next($request);
    }
}
