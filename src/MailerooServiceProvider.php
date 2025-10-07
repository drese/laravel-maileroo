<?php

namespace Drese\LaravelMaileroo;

use Illuminate\Mail\MailManager;
use Illuminate\Support\ServiceProvider;
use Maileroo\MailerooClient;

class MailerooServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/maileroo.php', 'maileroo'
        );

        // Register Maileroo SDK Client
        $this->app->singleton(MailerooClient::class, function ($app) {
            return new MailerooClient(
                config('maileroo.api_key'),
                config('maileroo.timeout', 30)
            );
        });

        // Register Form Service
        $this->app->singleton(MailerooFormService::class, function ($app) {
            return new MailerooFormService($app->make(MailerooClient::class));
        });
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/maileroo.php' => config_path('maileroo.php'),
            ], 'maileroo-config');
        }

        // Register custom mail transport
        $this->app->make(MailManager::class)->extend('maileroo', function ($config) {
            return new MailerooTransport(
                $this->app->make(MailerooClient::class)
            );
        });
    }
}