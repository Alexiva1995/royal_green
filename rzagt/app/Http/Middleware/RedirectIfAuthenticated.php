<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\IndexController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\ComisionesController;

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
            if ($request->getPathInfo() == '/login'){
                return redirect('admin');
            }
            if (empty(Auth::user()->verificar_correo)) {
                if (session('2fact') != 1) {
                    if ($request->getPathInfo() != '/autentication/2fact') {
                        return redirect()->route('autenticacion.2fact')->with('msj', 'Debe Validar su identidad');
                    }
                }
            }else{
                session(['2fact' => 1]);
            }
            if (session('2fact') == 1) {
                $funcionesIndex = new IndexController;
                if ($request->getPathInfo() != '/tienda/savecompra') {
                    if ($request->getPathInfo() != '/tienda') {
                        $compras = $funcionesIndex->getShopping(Auth::user()->ID);
                        $check = count($compras);
                        if (!session()->has('menu')) {
                            session(['menu' => 1]);
                        }
                        if (Auth::user()->ID != 1 || Auth::user()->ID != 614) {
                            if ($check == 0) {
                                if (session('menu') == 1) {
                                    session(['menu' => 0]);
                                }
                                return redirect()->route('tienda-index')->with('msj', 'Por favor realice una compra primero');
                            }

                            if (Auth::user()->status == 0) {
                                if (session('menu') == 1) {
                                    session(['menu' => 0]);
                                }
                                return redirect()->route('tienda-index')->with('msj', 'Su Compra esta siendo procesada, por favor espere que este validada para usar las funciones de nuestro sistema');
                            }
                        }
                    }
                }
            }
        }

        return $next($request);
    }
}
