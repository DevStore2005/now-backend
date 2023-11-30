<?php

namespace App\Http\Middleware;

use Closure;
use App\Utils\UserType;
use Illuminate\Support\Facades\Auth;

class Grocer
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if(Auth::check() && Auth::user()->role == UserType::GROCERY_OWNER){
            return $next($request);
        } else{
            return redirect()->route('grocer.login');
        }
    }
    // {
    //     if ($request->user()) {
    //         if ($request->user()->role == UserType::GROCERY_OWNER) {

    //             return $next($request);
    //         } else {

    //             return redirect()->route('grocer.index');
    //         }
    //     } else {
    //         return redirect('login');
    //     }
    // }
}
