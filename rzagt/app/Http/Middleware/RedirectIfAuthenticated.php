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
            if (session('2fact') != 1) {
                if ($request->getPathInfo() != '/autentication/2fact') {
                    return redirect()->route('autenticacion.2fact')->with('msj', 'Debe Validar su identidad');
                }
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
                        if (Auth::user()->ID != 1) {
                            if ($check == 0) {
                                if (session('menu') == 1) {
                                    session(['menu' == 0 ]);
                                }
                                return redirect()->route('tienda-index')->with('msj', 'Por favor realice una compra primero');
                            }
                            $idmayor = $compras->max('post_id');
                            $detalleCompra = $funcionesIndex->getShoppingDetails($idmayor);
                            if (!$detalleCompra->null) {
                                if (session('menu') == 1) {
                                    session(['menu' => 0]);
                                }
                                return redirect()->route('tienda-index')->with('msj', 'Su Compra esta siendo procesada, por favor espere que este validada para usar las funciones de nuestro sistema');
                            }
                        }
                        // $comisionesController = new ComisionesController;
                        // $comisionesController->registePackageToRentabilizar(Auth::user()->ID);
                        // $checkRentabilidad = DB::table('log_rentabilidad')->where([
                        //     ['iduser', '=', Auth::user()->ID],
                        //     ['progreso', '=', 100],
                        // ])->orderBy('id', 'desc')->first();
                        // if ($checkRentabilidad != null) {
                        //     $checkRentabilidad1 = DB::table('log_rentabilidad')->where([
                        //         ['iduser', '=', Auth::user()->ID],
                        //         ['progreso', '<', 100],
                        //     ])->first();
                        //     if ($checkRentabilidad1 == null) {
                        //         // if ($checkRentabilidad->limite > $checkRentabilidad->retirado) {
                        //         //     $walletController = new WalletController;
                        //         //     $walletController->retiroCulminacionRentabilidad(Auth::user()->ID, $checkRentabilidad->id);
                        //         // }
                        //         return redirect()->route('tienda-index')->with('msj', 'Por favor compre otro paquete');
                        //     }else{
                        //         // if ($checkRentabilidad->limite > $checkRentabilidad->retirado) {
                        //         //     // $walletController = new WalletController;
                        //         //     // $walletController->retiroCulminacionRentabilidad(Auth::user()->ID, $checkRentabilidad->id);
                        //         // }
                        //     }
                        // }
                    }
                }
            }
        }

        return $next($request);
    }
}
