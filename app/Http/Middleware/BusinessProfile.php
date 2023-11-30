<?php

namespace App\Http\Middleware;

use Closure;
use App\Utils\UserType;

class BusinessProfile
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
        if($request->user()) 
         {   
            if($request->user()->role == UserType::GROCERY_OWNER || $request->user()->role == UserType::RESTAURANT_OWNER){

            return $next($request);
            }
            else{
             
            return redirect('/restaurant/index');
            }
       }
       else{
            return redirect('/restaurant/login');

       }
    }
}
