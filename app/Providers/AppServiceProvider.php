<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
    }

    public function boot(): void
    {
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        // Directive Blade pour formater les montants
        Blade::directive('montant', function ($expression) {
            // Extraire les arguments pour sécuriser la devise null
            return "<?php
                \$_args = [$expression];
                \$_montant = \$_args[0] ?? 0;
                \$_devise  = \$_args[1] ?? 'USD';
                echo App\Helpers\DeviseHelper::formater(\$_montant, \$_devise ?? 'USD');
            ?>";
        });
    }
}
