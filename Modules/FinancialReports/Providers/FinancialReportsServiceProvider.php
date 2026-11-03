<?php

namespace Modules\FinancialReports\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Modules\FinancialReports\Services\FinancialCalculationService;

class FinancialReportsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(FinancialCalculationService::class, function () {
            return new FinancialCalculationService();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
        $this->loadViewsFrom(__DIR__ . '/../Resources/views', 'reports');
        $this->loadViewsFrom(__DIR__ . '/../Resources/views', 'financial-reports');

        $this->registerRoutes();
    }

    /**
     * Register module routes
     */
    protected function registerRoutes(): void
    {
        Route::middleware(['web', 'auth'])
            ->prefix('reports')
            ->name('reports.')
            ->group(__DIR__ . '/../Routes/web.php');
    }
}

