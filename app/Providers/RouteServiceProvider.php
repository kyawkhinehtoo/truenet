<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        $this->configureRoutes();

        parent::boot();
    }

    /**
     * Configure the routes for the application.
     *
     * @return void
     */
    protected function configureRoutes()
    {
        // Web routes
        Route::middleware('web')
            ->namespace('App\Http\Controllers') // <-- Add the namespace prefix
            ->group(base_path('routes/web.php'));

        // API routes
        Route::middleware('api')
            ->namespace('App\Http\Controllers') // <-- Add the namespace prefix
            ->group(base_path('routes/api.php'));
    }
}
