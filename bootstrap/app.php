<?php

use App\Http\Middleware\Authenticate;
use Illuminate\Foundation\Application;
use App\Http\Middleware\SetLocaleMiddleware as MultiLangSet;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Middleware\HandleCors;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware(['auth:api', 'email-verified', 'admin'])
                ->prefix('api/v1/admin')
                ->name('api.v1.admin.')
                ->group(base_path('routes/api/v1/admin.php'));
            Route::middleware(['auth:api', 'email-verified', 'user'])
                ->prefix('api/v1/user')
                ->name('api.v1.user.')
                ->group(base_path('routes/api/v1/user.php'));

            Route::prefix('api/v1')
                ->name('api.v1.')
                ->group(base_path('routes/api/v1/public.php'));
        }
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->api(prepend: [
            HandleCors::class,
        ]);
        $middleware->trustProxies(at: '*', headers: Request::HEADER_X_FORWARDED_FOR | Request::HEADER_X_FORWARDED_PROTO);
        $middleware->alias([
            'auth' => Authenticate::class,
            'email-verified' => App\Http\Middleware\EmailVerified::class,
            'admin' => App\Http\Middleware\AdminMidelware::class,
            'user' => App\Http\Middleware\UserMidelware::class,

        ]);
        $middleware->web(MultiLangSet::class);
        $middleware->api(MultiLangSet::class);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
