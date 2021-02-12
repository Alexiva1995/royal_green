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
            $funcionesIndex = new IndexController;
            if ($request->getPathInfo() != '/tienda/savecompra') {
                if ($request->getPathInfo() != '/tienda') {
                    $check = count($funcionesIndex->getShopping(Auth::user()->ID));
                    if ($check == 0) {
                        return redirect()->route('tienda-index')->with('msj', 'Por favor realice una compra primero');
                    }
                    $comisionesController = new ComisionesController;
                    $comisionesController->registePackageToRentabilizar(Auth::user()->ID);
                    $checkRentabilidad = DB::table('log_rentabilidad')->where([
                        ['iduser', '=', Auth::user()->ID],
                        ['progreso', '=', 100],
                    ])->orderBy('id', 'desc')->first();
                    if ($checkRentabilidad != null) {
                        $checkRentabilidad1 = DB::table('log_rentabilidad')->where([
                            ['iduser', '=', Auth::user()->ID],
                            ['progreso', '<', 100],
                        ])->first();
                        if ($checkRentabilidad1 == null) {
                            if ($checkRentabilidad->limite > $checkRentabilidad->retirado) {
                                $walletController = new WalletController;
                                $walletController->retiroCulminacionRentabilidad(Auth::user()->ID, $checkRentabilidad->id);
                            }
                            return redirect()->route('tienda-index')->with('msj', 'Por favor compre otro paquete');
                        }else{
                            if ($checkRentabilidad->limite > $checkRentabilidad->retirado) {
                                $walletController = new WalletController;
                                $walletController->retiroCulminacionRentabilidad(Auth::user()->ID, $checkRentabilidad->id);
                            }
                        }
                    }
                }
            }
        }

        return $next($request);
    }
}
