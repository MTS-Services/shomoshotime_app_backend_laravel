<?php

namespace App\Providers;

use App\Services\SMS\DezSmsService;
use Illuminate\Support\ServiceProvider;

class DezSmsServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(DezSmsService::class, function ($app) {
            return new DezSmsService();
        });

        // Optionally, if you want to bind it to a simpler name for direct resolution
        $this->app->alias(DezSmsService::class, 'dezsms');
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
