<?php

namespace App\Http\Controllers;

use App\Models\Wallet;
use Illuminate\Http\Request;
use App\Models\OrdenPurchases;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;

class ReporteController extends Controller
{
    //

    /**
     * lleva a la vista de informen de pedidos
     *
     * @return void
     */
    public function indexPedidos()
    {
        $ordenes = OrdenPurchases::orderBy('id', 'desc')->get();
    
        foreach ($ordenes as $orden) {
            $orden->name = $orden->getOrdenUser->fullname;
            // $orden->grupo = $orden->getGroupOrden->name;
            $orden->paquete = $orden->getPackageOrden->name;
        }

        return view('reports.perdido', compact('ordenes'));
    }

    /**
     * Lleva a la vista de informa de comisiones
     *
     * @return void
     */
    public function indexComision()
    {
        $wallets = Wallet::where([
            ['tipo_transaction', '=', 0],
            ['status', '!=', '3']
        ])->get();

        foreach ($wallets as $wallet) {
            $wallet->name = $wallet->getWalletUser->fullname;
            $wallet->referido = $wallet->getWalletReferred->fullname;
        }

        return view('reports.comision', compact('wallets'));
    }


    /**
     * lleva a la vista de informe de beneficio-royal
     *
     * @return void
     */
    public function indexBeneficio()
    {
        try {
            $beneficios = Wallet::all();
            $comision = Wallet::where('tipo_transaction', 0)->sum('monto');
            $retiro = Wallet::where('tipo_transaction', 1)->sum('monto');
            // dd($comision);

            return view('reports.beneficio', compact('beneficios', 'comision', 'retiro'));
        } catch (\Throwable $th) {
            Log::error('ReporteController - indexBeneficio -> Error: '.$th);
            abort(403, "Ocurrio un error, contacte con el administrador");
        }

    }


    public function graphisDashboard()
    {
        
    }
}
