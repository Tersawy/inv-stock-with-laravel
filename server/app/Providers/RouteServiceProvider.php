<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * The path to the "home" route for your application.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        //

        parent::boot();
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $this->mapAppRoutes();

        $this->mapWebRoutes();

        //
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        Route::middleware('web')
            ->namespace($this->namespace)
            ->group(base_path('routes/web.php'));
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapAppRoutes()
    {
        $route = Route::prefix('api/v1')->middleware('api');

        // Auth routes
        $route->group(base_path('routes/api/auth.php'));
        // Warehouse routes
        $route->group(base_path('routes/api/warehouse.php'));
        // Category routes
        $route->group(base_path('routes/api/category.php'));
        // Brand routes
        $route->group(base_path('routes/api/brand.php'));
        // MainUnit routes
        $route->group(base_path('routes/api/mainUnit.php'));
        // SubUnit routes
        $route->group(base_path('routes/api/subUnit.php'));
        // Product routes
        $route->group(base_path('routes/api/product.php'));
        // Supplier routes
        $route->group(base_path('routes/api/supplier.php'));
    }
}
