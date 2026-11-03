<?php

namespace Modules\HR\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class HRServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
        $this->loadViewsFrom(__DIR__ . '/../Resources/views', 'hr');

        $this->registerRoutes();
    }

    /**
     * Register module routes
     */
    protected function registerRoutes(): void
    {
        Route::middleware(['web', 'auth'])
            ->prefix('hr')
            ->name('hr.')
            ->group(__DIR__ . '/../Routes/web.php');
    }
}
