<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        // En producción, fuerza https para todas las URLs generadas (asset(), route(), etc.)
        if (app()->environment('production')) {
            URL::forceScheme('https');
        }
    }
}
