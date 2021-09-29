<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class checkEmail
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
            if (empty(Auth::user()->email_verified_at)) {
                Auth::logout();
                return redirect()->route('login')->with('msj-info', 'Correo Electronico no confirmado, Revise su correo Electronico - ');
            }   
        return $next($request);
    }
}
