<?php

namespace App\Http\Middleware;

//use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Support\Facades\Auth;
use Closure;

class Authenticate 
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    
        public function handle($request, Closure $next, $guard = null)
    {
        if (Auth::guard($guard)->guest()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response('authorized.', 401);
            } else {
                return response('Unauthorized.', 401);
            }
        }

        return $next($request);
    }
       /* if(!$request->expectsJson()){
        return response('Unauthorized.', 401);

        }*/
}
