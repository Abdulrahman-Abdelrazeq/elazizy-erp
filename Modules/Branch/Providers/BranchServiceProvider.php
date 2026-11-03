<?php

namespace Modules\Branch\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class BranchServiceProvider extends ServiceProvider
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
        $this->loadViewsFrom(__DIR__ . '/../Resources/views', 'branch');

        $this->registerRoutes();
    }

    /**
     * Register module routes
     */
    protected function registerRoutes(): void
    {
        Route::middleware(['web', 'auth'])
            ->prefix('branches')
            ->name('branches.')
            ->group(__DIR__ . '/../Routes/web.php');
    }
}
