<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        // Strict mode di non-production: cegah lazy loading, mass assignment diam-diam, dll
        Model::shouldBeStrict(! app()->isProduction());

        // Aturan password default (9.3)
        Password::defaults(function () {
            $rule = Password::min(10)->mixedCase()->numbers();

            // uncompromised() butuh internet; hanya di production
            return app()->isProduction()
                ? $rule->uncompromised()
                : $rule;
        });

        // Paksa HTTPS di production
        if (app()->isProduction()) {
            URL::forceScheme('https');
        }
    }
}
