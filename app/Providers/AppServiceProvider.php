<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    protected $namespace = 'App\Http\Controllers';

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
        //
    }

    public function map()
    {
        $this->mapApiRoutes();

        // You can also have your web routes here
        $this->mapWebRoutes();
    }

    protected function mapApiRoutes()
    {
        Route::prefix('api') // Adds the "api" prefix to all routes in api.php
            ->middleware('api') // Adds the "api" middleware group to these routes
            ->namespace($this->namespace) // Use the default namespace (App\Http\Controllers)
            ->group(base_path('routes/api.php')); // Load routes from the api.php file
    }

    protected function mapWebRoutes()
    {
        Route::middleware('web')
            ->namespace($this->namespace)
            ->group(base_path('routes/web.php'));
    }
}
