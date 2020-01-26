<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if (Auth::guard($guard)->check()) {
            $user = Auth::user();
            if($user->usertype_id == "2" || $user->usertype_id == "5"|| $user->usertype_id == "1"){
                return redirect('/seguimiento');
            }else if($user->usertype_id == "3" || $user->usertype_id == "4"|| $user->usertype_id == "1"){
                return redirect('/bolsa');
            }
            
        }

        return $next($request);
    }
}
