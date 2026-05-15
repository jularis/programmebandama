<?php

namespace App\Http\Middleware;

use Closure;


class CheckStatus
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
        if (auth()->check()) {
            $user = auth()->user();
            if ($user->status) {
                return $next($request);
            } else {
                 return to_route('home');
            }
        }
        abort(403);
    }
}
