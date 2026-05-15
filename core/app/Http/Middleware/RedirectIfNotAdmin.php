<?php

namespace App\Http\Middleware;

use Closure;
class RedirectIfNotAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = 'admin')
    {
        if (!auth()->guard($guard)->check()) {
            return to_route('admin.login');
        }

        return $next($request);
    }
}
