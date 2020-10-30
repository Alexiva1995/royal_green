<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\IndexController;

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
            $funcionesIndex = new IndexController;
            if ($request->getPathInfo() != '/mioficina/tienda/savecompra') {
                if ($request->getPathInfo() != '/mioficina/tienda') {
                    $check = count($funcionesIndex->getShopping(Auth::user()->ID));
                    if ($check == 0) {
                        return redirect()->route('tienda-index')->with('msj', 'Por favor realice una compra primero');
                    }
                }
            }
        }

        return $next($request);
    }
}
