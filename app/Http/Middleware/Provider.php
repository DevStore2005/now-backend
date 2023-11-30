<?php

namespace App\Http\Middleware;

use Closure;
use App\User;
use App\Utils\UserType;
use App\Utils\HttpStatusCode;

class Provider
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
        if ($request->user()->role == UserType::PROVIDER) {
            return $next($request);
        } else {
            return response()->json(['error' => true, 'message' => 'Provider ' . HttpStatusCode::$statusTexts[HttpStatusCode::UNAUTHORIZED]], HttpStatusCode::UNAUTHORIZED);
        }
    }
}
