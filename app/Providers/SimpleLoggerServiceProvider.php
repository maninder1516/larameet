<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class SimpleLoggerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(SimpleLoggerService::class, function ($app) {
            return new SimpleLoggerService();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
        \Log::debug('SimpleLoggerServiceProvider booted.');
    }
}
