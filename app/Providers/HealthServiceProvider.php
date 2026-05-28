<?php

namespace App\Providers;

use App\Services\Health\Checks\DatabaseConnectivityCheck;
use App\Services\Health\Checks\MigrationsCheck;
use App\Services\HealthService;
use Illuminate\Support\ServiceProvider;

class HealthServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->tag([
            DatabaseConnectivityCheck::class,
            MigrationsCheck::class,
        ], 'health.checks');

        $this->app->bind(
            HealthService::class,
            fn ($app) => new HealthService($app->tagged('health.checks')),
        );
    }
}
