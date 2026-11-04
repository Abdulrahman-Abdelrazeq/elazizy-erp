<?php

namespace Modules\Quality\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class QualityServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../Config/config.php', 'quality');
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
        $this->loadViewsFrom(__DIR__ . '/../Resources/views', 'quality');

        $this->registerRoutes();
    }

    /**
     * Register module routes
     */
    protected function registerRoutes(): void
    {
        Route::middleware(['web', 'auth'])
            ->prefix('quality')
            ->name('quality.')
            ->group(__DIR__ . '/../Routes/web.php');
    }
}
