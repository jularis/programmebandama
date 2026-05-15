<?php

namespace App\Http\Middleware;

use Closure;


class RedirectIfAuthenticated
{
    public function handle($request, Closure $next, $guard = null)
    {

        if (auth()->guard($guard)->check()) {
            $user =auth()->user();
            if($user){
                return redirect()->route('manager.dashboard');
            }
            // elseif($user->user_type == "staff"){
            //     return redirect()->route('staff.dashboard');
            // }
        }
        return $next($request);

    }
}
