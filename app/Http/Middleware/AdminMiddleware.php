<?php

namespace fuma\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use fuma\User;

class AdminMiddleware
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
        $user = User::all()->count();
        if (!($user == 1)) {
            if (!Auth::user()->hasPermissionTo('Administer roles & permissions')) //If user does //not have this permission
        {
                abort('401');
            }
        }

        return $next($request);
    }
}