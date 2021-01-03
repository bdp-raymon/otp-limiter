<?php

namespace BdpRaymon\OtpLimiter;

use Illuminate\Container\Container;
use Illuminate\Support\ServiceProvider;


class OtpLimiterServiceProvider extends ServiceProvider
{

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/config.php' => config_path('otp-limiter.php'),
            ], 'config');
        }
    }

    public function register(): void
    {
        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'otp-limiter');

        // Register the main class to use with the facade
        $this->app->bind('otp-limiter', function (Container $app) {
            return new OtpLimiter(
                $app->make('cache'),
                $app->make('config')
            );
        });
    }
}
