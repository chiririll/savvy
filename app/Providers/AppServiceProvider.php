<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

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
        $forwardedProto = strtolower(trim(explode(',', (string) request()->header('X-Forwarded-Proto'))[0]));

        if ($forwardedProto === 'https') {
            URL::forceScheme('https');
        }
    }
}
