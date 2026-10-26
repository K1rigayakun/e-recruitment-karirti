<?php

namespace App\Providers;

use App\Models\Document;
use App\Models\JobPosting;
use App\Models\PortfolioProject;
use App\Policies\DocumentPolicy;
use App\Policies\JobPostingPolicy;
use App\Policies\PortfolioProjectPolicy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends AuthServiceProvider
{
    protected $policies = [
        Document::class => DocumentPolicy::class,
        JobPosting::class => JobPostingPolicy::class,
        PortfolioProject::class => PortfolioProjectPolicy::class,
    ];

    public function register(): void {}

    public function boot(): void
    {
        $this->registerPolicies();

        Paginator::defaultView('vendor.pagination.karirti');

        Model::shouldBeStrict(! app()->isProduction());

        Password::defaults(function () {
            $rule = Password::min(10)->mixedCase()->numbers();

            return app()->isProduction() ? $rule->uncompromised() : $rule;
        });

        if (app()->isProduction()) {
            URL::forceScheme('https');
        }
    }
}
