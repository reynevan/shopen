<?php

namespace Shopen\Providers;

use Illuminate\Support\ServiceProvider;
use Shopen\Services\BreadcrumbsService;

class BreadcrumbsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton('breadcrumbs', function ($app) {
            return new BreadcrumbsService($app->make('request'));
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $breadcrumbsFile = base_path('routes/breadcrumbs.php');

        if (file_exists($breadcrumbsFile)) {
            require $breadcrumbsFile;
        }

        $breadcrumbsFile = __DIR__ . '/../routes/breadcrumbs.php';

        if (file_exists($breadcrumbsFile)) {
            require $breadcrumbsFile;
        }
    }
}