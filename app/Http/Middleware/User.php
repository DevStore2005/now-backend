<?php

namespace App\Http\Middleware;

use Closure;
use App\Utils\UserType;
use App\Utils\HttpStatusCode;

class User
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
        if ($request->user()->role == UserType::USER) {
            return $next($request);
        } else {
            return response()->json(['error' => true, 'message' =>  'User ' . HttpStatusCode::$statusTexts[HttpStatusCode::UNAUTHORIZED]], HttpStatusCode::UNAUTHORIZED);
        }
    }
}
