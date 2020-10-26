<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
            if ($request->getPathInfo() != '/mioficina/admin/user/update') {
                if ($request->getPathInfo() != '/mioficina/admin/user/edit') {
                    $check = DB::table('user_campo')->where([
                        ['ID', '=', Auth::user()->ID],
                        ['firstname', '!=', ''],
                        ['lastname', '!=', ''],
                        ['direccion', '!=', ''],
                        ['paypal', '!=', '']
                    ])->first();
                    if ($check == null) {
                        return redirect()->route('admin.user.edit')->with('msj4', 'Por favor llene su informacion personal, para poder hacer uso del sistema, nombre, apellido, direcion y su direccion en la billetera');
                    }
                }
            }
        }

        return $next($request);
    }
}
