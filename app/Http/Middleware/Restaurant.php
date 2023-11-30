<?php

namespace App\Http\Middleware;

use Closure;
use App\Utils\UserType;
use Illuminate\Support\Facades\Auth;

class Restaurant
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
        if(Auth::check() && Auth::user()->role == UserType::RESTAURANT_OWNER){
            return $next($request);
        } else{
            return redirect()->route('restaurant.login');
        }
    }

    // {
    //     if ($request->user()) {
    //         if ($request->user()->role == UserType::RESTAURANT_OWNER) {

    //             return $next($request);
    //         } else {

    //             return redirect()->route('restaurant.index');
    //         }
    //     } else {
    //         return redirect('login');
    //     }
    // }
}
