<?php

namespace App\Http\Controllers;

use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;


class AdminNewController extends Controller
{
    public function index()
    {
        view()->share('title', 'Resumen');
        return view('dashboard.index');
    }

    /**
     * Permite llevar los datos a la web
     *
     * @return void
     */
    public function dataGraficos()
    {
        $data = [
            'user' => $this->dataGraphiUser(),
            'compras' => $this->dataGraphiCompras()
        ];
        return json_encode($data);
    }

    /**
     * Permite obtener la cantidad de usuarios activos e inactivos de los ultimos 7 dias
     *
     * @return void
     */
    public function dataGraphiUser()
    {
        $fecha = Carbon::now();
        $data = User::whereDate('created_at', '>=', $fecha->copy()->subDays(7))
                                ->selectRaw('COUNT(ID) as total, status')
                                ->groupBy('status')->get();

        if ($data) {
            $data = [
                ['total' => 0],
                ['total' => 0]
            ];
        }
        return $data;
    }

    public function dataGraphiCompras()
    {
        $fecha = Carbon::now();
        $ordenes = DB::table('wp_posts')->whereDate('post_date', '>=', $fecha->copy()->subDays(7))
                ->selectRaw('ID, DAY(post_date) as dia')
                ->where([
                    ['post_type', '=', 'shop_order'],
                    ['post_status', '=', 'wc-completed']
                ])->get();
        foreach ($ordenes as $orden) {
            $orden->total = $this->totalCompra($orden->ID);
        }
        $dias = [];
        $totales = [];
        foreach ($ordenes->groupBy('dia') as $ordens) {
            $total = $ordens->sum('total');
            $dias [] = $ordens[0]->dia;
            $totales [] = $total;
        }

        $data = [
            $dias, $totales
        ];
        return $data;
    }

    /**
     * Permite obtener el total de las compras
     *
     * @param integer $idcompra
     * @return void
     */
    private function totalCompra($idcompra) 
    {
        $total = DB::table('wp_postmeta')
                    ->select('meta_value')
                    ->where([
                        ['post_id', '=', $idcompra],
                        ['meta_key', '=', '_order_total'],
                    ])->first();
        return $total->meta_value;
    }
}
