<?php

namespace App\Providers;

use App\Utils\MyAppEnv;
use App\Utils\UserType;
use Laravel\Cashier\Cashier;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        Cashier::ignoreMigrations();
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->environment('local')) {
            $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);
            $this->app->register(TelescopeServiceProvider::class);
        }

        if (config('app.env') == MyAppEnv::LOCAL) {
            DB::listen(function ($query) {
                Log::info('local query', [
                    'sql' => $query->sql,
                    'bindings' => $query->bindings,
                    'time' => $query->time
                ]);
            });
        }
        $role = auth()->check() && auth()->user()->role;
        Blade::if('restaurant', function () use ($role) {
            if ($role == UserType::RESTAURANT_OWNER) return 1;
            else return 0;
        });

        Blade::if('grocer', function () use ($role) {
            if ($role == UserType::GROCERY_OWNER) return 1;
            else return 0;
        });
    }
}
