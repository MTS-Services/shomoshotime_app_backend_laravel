<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Jenssegers\Agent\Agent;
use Symfony\Component\HttpFoundation\Response;
use Torann\GeoIP\Facades\GeoIP;

class Authenticate extends Middleware
{
    protected function redirectTo(Request $request)
    {
        if ($request->ajax()) {
            sendResponse(false, __('auth.unauthorized'), null, 401);
        }

        if ($request->is('api/*')) {
            abort(sendResponse(false, __('auth.unauthorized'), null, 401));
        }

        if (!$request->expectsJson()) {
            return route('login');
        }
    }
}
