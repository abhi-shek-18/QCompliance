<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $guard
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $guard = null)
    {
        // Check if the user is already authenticated
        if (Auth::guard($guard)->check()) {
            //Redirect them to a specific page (like the home page) if authenticated
            return redirect()->route('dashboard'); //Or wherever you want the user to be redirected after login
        }

        return $next($request);
    }
}
