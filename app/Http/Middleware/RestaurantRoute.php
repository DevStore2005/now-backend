<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Config;

class RestaurantRoute
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // Config::set('subdomain', 'restaurant');
        return $next($request);
    }
}
