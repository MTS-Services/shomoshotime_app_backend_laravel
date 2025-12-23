<?php

namespace App\Providers;

use Carbon\CarbonInterval;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Passport::tokensExpireIn(CarbonInterval::days(15));
        Passport::refreshTokensExpireIn(CarbonInterval::days(30));
        Passport::personalAccessTokensExpireIn(CarbonInterval::months(6));
        Passport::tokensCan([
            'user:read' => 'Read user information',
            'user:write' => 'Modify user information',
        ]);
        Passport::defaultScopes([
            'user:read',
        ]);
       
        Blade::componentNamespace('App\\View\\Components\\Frontend', 'frontend');
        Blade::componentNamespace('App\\View\\Components\\Admin', 'admin');
        
    }
}
