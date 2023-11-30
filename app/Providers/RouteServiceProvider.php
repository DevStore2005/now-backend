<?php

namespace App\Providers;

use App\Utils\MyAppEnv;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * The path to the "home" route for your application.
     *
     * @var string
     */
    public const HOME = '/dashboard';

    /**
     * @var string $_environment
     * @var array <string> $_domains
     */
    private $_environment, $_domains;

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        $this->_environment = App::environment();
        if ($this->_environment ==  MyAppEnv::STAGING) {
            $this->_domains = [
                'admin'         =>  'staging-admin.farenow.com',
//                'restaurant'    =>  'staging-restaurant.farenow.com',
//                'grocer'        =>  'staging-grocer.farenow.com',
                'api'           =>  'staging-api.farenow.com',
            ];
        } elseif ($this->_environment ==  MyAppEnv::PRODUCTION) {
            $this->_domains = [
                'admin'         =>   'admin.farenow.com',
//                'restaurant'    =>   'restaurant.farenow.com',
//                'grocer'        =>   'grocer.farenow.com',
                'api'           =>   'api.farenow.com',
            ];
        } else {
            $this->_domains = [
                'admin'         =>   config('common.domain.admin'),
//                'restaurant'    =>   config('common.domain.restaurant'),
//                'grocer'        =>   config('common.domain.grocer'),
                'api'           =>   config('common.domain.api'),
            ];
        }
        parent::boot();
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $this->mapApiRoutes();

        $this->mapWebRoutes();

        $this->mapUserRoutes();

        $this->mapProviderRoutes();

//        $this->mapResturantRoutes();
//
//        $this->mapGroceryRoutes();

//        $this->mapWebRestaurantRoutes();
//
//        $this->mapWebGrocerRoutes();
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
        Route::domain($this->_domains['admin'])
            ->middleware('web')
            ->namespace($this->namespace)
            ->group(base_path('routes/web/web.php'));
    }

    protected function mapWebRestaurantRoutes()
    {
        Route::domain($this->_domains['restaurant'])
            ->middleware('web')
            ->namespace($this->namespace)
            ->group(base_path('routes/web/restaurant.php'));
    }

    protected function mapWebGrocerRoutes()
    {
        Route::domain($this->_domains['grocer'])
            ->middleware('web')
            ->namespace($this->namespace)
            ->group(base_path('routes/web/grocery.php'));
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        Route::domain($this->_domains['api'])
        ->prefix('api')
            ->middleware('api')
            ->namespace($this->namespace)
            ->group(base_path('routes/api/api.php'));
    }

    protected function mapUserRoutes()
    {
        Route::domain($this->_domains['api'])
        ->prefix('api/user')
            ->middleware(['api','cors', 'json_response'])
            ->namespace($this->namespace)
            ->group(base_path('routes/api/user.php'));
    }

    protected function mapProviderRoutes()
    {
        Route::domain($this->_domains['api'])
        ->prefix('api/provider')
            ->middleware(['api','cors', 'json_response'])
            ->namespace($this->namespace)
            ->group(base_path('routes/api/provider.php'));
    }
    protected function mapResturantRoutes()
    {
        Route::domain($this->_domains['api'])
        ->prefix('api/restaurant')
            ->middleware(['api', 'cors', 'json_response'])
            ->namespace($this->namespace)
            ->group(base_path('routes/api/restaurant.php'));
    }
    protected function mapGroceryRoutes()
    {
        Route::domain($this->_domains['api'])
        ->prefix('api/grocery')
            ->middleware(['api', 'cors', 'json_response'])
            ->namespace($this->namespace)
            ->group(base_path('routes/api/grocery.php'));
    }
}
