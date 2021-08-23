<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrdenNewController extends Controller
{

    public function __construct()
    {
        view()->share('title', 'Ordenes de Red');
    }

    public function index()
    {
        $ordenes = [];
        return view('contabilidad.ordenes.index', compact('ordenes'));
    }

    /**
     * Obtenienes las ordenes filtradas por un rango de fecha
     *
     * @param Request $request
     * @return void
     */
    public function ordenFilterDate(Request $request)
    {
        $validate = $request->validate([
            'fecha1' => ['required', 'date'],
            'fecha2' => ['required', 'date'],
        ]);

        try {
            if ($validate) {
                $parametros = [
                    'fecha1' => date('Y-m-d', strtotime($request->fecha1)),
                    'fecha2' => date('Y-m-d', strtotime($request->fecha2)),
                ];
                $ordenes = $this->queryOrden($parametros, 'fecha');
                // dd($ordenes);
                return view('contabilidad.ordenes.index', compact('ordenes'));
            }
        } catch (\Throwable $th) {
            // dd($th);
            return redirect()->back()->with('msj2', 'ocurrio un error');
        }
    }

    /**
     * Obtenienes las ordenes filtradas por el tipo de orden
     *
     * @param Request $request
     * @return void
     */
    public function ordenFilterType(Request $request)
    {
        $validate = $request->validate([
            'orden' => ['required', 'string'],
        ]);

        try {
            if ($validate) {
                $parametros = $request->orden;
                $ordenes = $this->queryOrden($parametros, 'activacion');
                // dd($ordenes);
                return view('contabilidad.ordenes.index', compact('ordenes'));
            }
        } catch (\Throwable $th) {
            // dd($th);
            return redirect()->back()->with('msj2', 'ocurrio un error');
        }
    }

    /**
     * Obtenienes las ordenes filtradas por el estado de la compra
     *
     * @param Request $request
     * @return void
     */
    public function ordenFilterStatus(Request $request)
    {
        $validate = $request->validate([
            'status' => ['required', 'string'],
        ]);

        try {
            if ($validate) {
                $arregloEstado = [
                    'Completado' => 'wc-completed',
                    'En Espera' => 'wc-on-hold'
                ];
                $parametros = $arregloEstado[$request->status];
                $ordenes = $this->queryOrden($parametros, 'estado');
                // dd($ordenes);
                return view('contabilidad.ordenes.index', compact('ordenes'));
            }
        } catch (\Throwable $th) {
            // dd($th);
            return redirect()->back()->with('msj2', 'ocurrio un error');
        }
    }

     /**
     * Obtenienes las ordenes filtradas de un usuario
     *
     * @param Request $request
     * @return void
     */
    public function ordenFilterUser(Request $request)
    {
        $validate = $request->validate([
            'iduser' => ['required', 'numeric'],
        ]);

        try {
            if ($validate) {
                $parametros = $request->iduser;
                if (Auth::user()->ID != 1) {
                    $parametros = Auth::user()->ID;
                }
                $ordenes = $this->queryOrden($parametros, 'idusuario');
                // dd($ordenes);
                return view('contabilidad.ordenes.index', compact('ordenes'));
            }
        } catch (\Throwable $th) {
            // dd($th);
            return redirect()->back()->with('msj2', 'ocurrio un error');
        }
    }

    /**
     * Undocumented function
     *
     * @param mixed $parametros
     * @param string $tipo
     * @return object
     */
    public function queryOrden($parametros, string $tipo):object
    {
        $query = collect();

        if ($tipo == 'fecha') {
            $query = DB::table('wp_posts')
                        ->select('ID as idorden', 'post_date', 'post_status', 'to_ping as tipo_activacion')
                        ->whereBetween('post_date', [$parametros['fecha1'], $parametros['fecha2']])
                        ->orderBy('ID', 'desc')
                        ->paginate(100);
        }

        if ($tipo == 'activacion') {
            $query = DB::table('wp_posts')
                        ->select('ID as idorden', 'post_date', 'post_status', 'to_ping as tipo_activacion')
                        ->where('to_ping', $parametros)
                        ->orderBy('ID', 'desc')
                        ->paginate(100);
        }

        if ($tipo == 'estado') {
            $query = DB::table('wp_posts')
                        ->select('ID as idorden', 'post_date', 'post_status', 'to_ping as tipo_activacion')
                        ->where('post_status', $parametros)
                        ->orderBy('ID', 'desc')
                        ->paginate(100);
        }

        if ($tipo == 'idusuario') {
            $idordens = $this->getOrdensIduser($parametros);
            $query = DB::table('wp_posts')
                        ->select('ID as idorden', 'post_date', 'post_status', 'to_ping as tipo_activacion')
                        ->whereIn('ID', $idordens)
                        ->orderBy('ID', 'desc')
                        ->paginate(100);
        }
        if (!empty($query)) {
            $query = $this->detailOrden($query);
        }

        return $query;
    }

    /**
     * Permite obtener la informacion faltante de las ordenes
     *
     * @param object $ordenes
     * @return object
     */
    public function detailOrden($ordenes):object
    {
        $arregloEstado = [
            'wc-completed' => 'Completado',
            'wc-pending' => 'Pendiente de Pago',
            'wc-processing' => 'Procesando',
            'wc-on-hold' => 'En Espera',
            'wc-cancelled' => 'Cancelado',
            'wc-refunded' => 'Reembolsado',
            'wc-failed' => 'Fallido',
        ];
        foreach ($ordenes as $orden) {
            //Obtiene el estado de forma entendible
            $orden->estado = 'No Disponible';
            if (array_key_exists($orden->post_status, $arregloEstado)) {
                $orden->estado = $arregloEstado[$orden->post_status];
            }
            //Formatea la fecha
            $orden->fecha = date('Y-m-d', strtotime($orden->post_date));
            // informacion Usuario
            $orden->iduser = $this->getIdUserOrden($orden->idorden);
            $user = User::find($orden->iduser)->only('display_name', 'user_email');
            $orden->email = 'No Disponible';
            $orden->fullname = 'No Disponible';
            if (!empty($user)) {
                $orden->email = $user['user_email'];
                $orden->fullname = $user['display_name'];
            }
            // Informacion Orden
            $orden->price = $this->getPriceOrden($orden->idorden);
            $orden->concepto = $this->getConceptoOrden($orden->idorden);
        }
        return $ordenes;
    }

    /**
     * Permite obtener las ordenes de un determinado usuario
     *
     * @param integer $iduser
     * @return array
     */
    private function getOrdensIduser(int $iduser): array
    {
        $query = DB::table('wp_postmeta')->select('post_id as idpost')
                                ->where([
                                    ['meta_key', '=', '_customer_user'],
                                    ['meta_value', '=', $iduser]
                                ])->get();
        $idordens = [];
        if (!empty($query)) {
            foreach ($query as $orden) {
                $idordens [] = $orden->idpost;
            }
        }

        return $idordens;
    }

    /**
     * Permite el iduser usuario de una compra en especifico
     *
     * @param integer $idorden
     * @return integer
     */
    private function getIdUserOrden(int $idorden):int
    {
         $query = DB::table('wp_postmeta')->select('meta_value as iduser')
                                ->where([
                                    ['post_id', '=', $idorden],
                                    ['meta_key', '=', '_customer_user']
                                ])->first();
        $iduser = 0;
        if (!empty($query)) {
            $iduser = $query->iduser;
        }

        return $iduser;
    }

    /**
     * Permite el precio de una compra en especifico
     *
     * @param integer $idorden
     * @return float
     */
    private function getPriceOrden(int $idorden):float
    {
         $query = DB::table('wp_postmeta')->select('meta_value as price')
                                ->where([
                                    ['post_id', '=', $idorden],
                                    ['meta_key', '=', '_order_total']
                                ])->first();
        $price = 0;
        if (!empty($query)) {
            $price = $query->price;
        }

        return $price;
    }

    /**
     * Permite obtener el concepto de la orden
     *
     * @param integer $idorden
     * @return string
     */
    private function getConceptoOrden(int $idorden): string
    {
        $itemsOrden = DB::table('wp_woocommerce_order_items')
                        ->select('order_item_name')
                        ->where('order_id', '=', $idorden)
                        ->where('order_item_type', '=', 'line_item')
                        ->get();

        $items = "";
        foreach ($itemsOrden as $item){
            $items = $items." ".$item->order_item_name;
        }
        return $items;
    }
}
