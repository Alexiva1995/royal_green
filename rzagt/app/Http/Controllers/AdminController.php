<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use App\User;
use App\Wallet;
use App\Settings;
use App\Notification;


use App\Http\Controllers\IndexController;
use App\Http\Controllers\ComisionesController;
use App\Http\Controllers\RangoController;
use App\Http\Controllers\ActivacionController;


class AdminController extends Controller
{

    public $indexControl;
    public $rangoControl;
    public $activaControl;
    public $comiControl;
	function __construct()
	{
        // TITLE
        $this->indexControl = new IndexController;
        $this->rangoControl = new RangoController;
        $this->activaControl = new ActivacionController;
        $this->comiControl = new ComisionesController;
	}



    public function index()
    {
        if (Auth::user()->ID == 614) {
        }
        if (Auth::user()->ID == 1) {
            return redirect()->route('new_admin');
        }else{
            return redirect()->route('new_dashboard');
        }
    }

    public function eliminarRentabilidadMala()
    {
        $users = Wallet::where([
            ['descripcion', '=', 'Utilidad (5%)'],
            ['iduser', '<', 7500]
        ])->take(250)->select('id')->get();
        foreach ($users as $user ) {
            dump('Usuario -> '.$user->id);
            $this->comiControl->eliminarRegistros($user->id);
            dump('Procesado');
        }
    }

     /**
     * Permite pagar la rentabilidad a los usuarios que no lo hicieron el 11 de agosto
     *
     * @return void
     */
    public function pagarRentabilidad()
    {
        $userFaltantes = DB::table('log_rentabilidad_pay')
                            ->whereDate('fecha_pago', '=', '20210811')
                            ->where([
                                ['iduser', '>', 6900],
                                ['iduser', '<', 7500],
                                ['porcentaje', '=', 0.51]
                            ])->get();
        foreach ($userFaltantes as $userF) {
            $checkCobro = DB::table('log_rentabilidad_pay')
            ->whereDate('fecha_pago', '=', '20210811')
            ->where([
                ['iduser', '=', $userF->iduser],
                ['porcentaje', '=', 0.5]
            ])->first();
            dump('Usuario -> '.$userF->iduser);
            if ($checkCobro == null) {
                $orden = DB::table('log_rentabilidad')->where('id', $userF->id_log_renta)->first();
                if ($this->comiControl->filtrarUserRentabilidad($orden->iduser)) {
                    $user = User::find($orden->iduser);
                    if (!empty($user)) {
                        $tmp = json_decode($orden->detalles_producto);
                        $producto = [
                            'idproducto' => $orden->idproducto,
                            'nombre' => $tmp->nombre,
                            'precio' => $orden->precio
                        ];
                            $this->comiControl->saveRentabilidad($orden->idcompra, $orden->iduser, $producto, 0.5, $orden->nivel_minimo_cobro);
                    }
                    dump('Procesado');
                }
            }
        }
    }

    public function getDataDashboard($iduser)
    {

        // obtiene la informacion de la ultima rentabilidad agregada
        $paquete = DB::table('log_rentabilidad')->where('iduser', $iduser)->orderBy('id', 'desc')->first();
        if (!empty($paquete)) {
            $paquete->img = asset('assets/paquetes/rg'.$paquete->precio.'.png');
            $paquete->detalles_producto = json_decode($paquete->detalles_producto);
        }

        // obtiene la informacion del bono de bienvenida
        $bienvenida = $this->indexControl->bonoBienvenida($iduser);

        $data = [
            'paquetes' => $paquete,
            'rangospoints' => $this->rangoControl->getPointRango($iduser),
            'bienvenida' => $bienvenida
        ];

        return $data;
    }

    /**
     * Permite ver la vista del dashboard de los usuarios
     *
     * @return void
     */
    public function subdashboard()
    {
        try {
            $iduser = 1;
            if (request()->iduser != null) {
                $iduser = request()->iduser;
            }
            
            $data = $this->getDataDashboard($iduser);
            $principal = 0;
            view()->share('title', 'Resumen de Usuarios');
            return view('dashboard.subdashboard', compact('data', 'iduser', 'principal'));
        } catch (\Throwable $th) {
            // dd($th);
            return redirect()->back()->with('msj', 'ID de usuario Incorrecto');
        }
    }


    /**
     * Permite obtener mas informacion de un usuario
     *
     * @param integer $iduser
     * @return object
     */
    public function masInfo($iduser):object
    {
        $masinfo = DB::table('user_campo')->where('ID', $iduser)->first();
        return $masinfo;
    }

    public function direct_records(){
        // TITLE
        view()->share('title', 'Usuarios Directos');


        $referidosDirectos = User::where('referred_id', '=', Auth::user()->ID)
                                ->orderBy('created_at', 'DESC')
                                ->get();

        return view('dashboard.directRecords')->with(compact('referidosDirectos'));
    }

    
    public function buscardirectos(){
        // TITLE
        view()->share('title', 'Usuarios Directos');

        $primero = new Carbon($_POST["fecha1"]);
        $segundo = new Carbon($_POST["fecha2"]);
        $referidosDirectos =User::whereDate("created_at",">=",$primero)
             ->whereDate("created_at","<=",$segundo)
             ->where('referred_id', '=', Auth::user()->ID)
             ->orderBy('created_at', 'DESC')
             ->get();

        return view('dashboard.buscardirectos')->with(compact('referidosDirectos'));
    }

    

    public function buscarnetwork(){

        // TITLE

        
        $allReferido = $this->indexControl->getChidrens2(Auth::user()->ID, [], 1, 'referred_id', 0);
        view()->share('title', 'Usuarios en Red');

        return view('dashboard.networkRecords')->with(compact('allReferido'));

    }

    /**
     * Permite obtener la informacion de un usuario en especifico
     *
     * @param Request $request
     * @return void
     */
    public function buscarnetworknivel(Request $request)
    {
        // TITLE
        view()->share('title', 'Usuarios en Red');

        $user = User::find($request->iduser);
        $allReferido[] = $user;
        
        return view('dashboard.networkRecords')->with(compact('allReferido'));
    }



    public function network_records(){

        // TITLE

        
        $allReferido = $this->indexControl->getChidrens2(Auth::user()->ID, [], 1, 'referred_id', 0);
        view()->share('title', 'Usuarios en Red');

        return view('dashboard.networkRecords')->with(compact('allReferido'));

    }
  

    public function personal_orders(){
          // TITLE
          view()->share('title', 'Ordenes Personales');
        $settings = Settings::first();
        $ordenes = DB::table($settings->prefijo_wp.'postmeta')
                    ->select('post_id')
                    ->where('meta_key', '=', '_customer_user')
                    ->where('meta_value', '=', Auth::user()->ID)
                    ->orderBy('post_id', 'DESC')
                    ->get();

        return view('dashboard.personalOrders')->with(compact('ordenes'));
    }

    

    

     public function buscarpersonalorder(){

          // TITLE

          view()->share('title', 'Ordenes Personales');

        $settings = Settings::first();

        $primero = new Carbon($_POST['fecha1']);

        $segundo = new Carbon($_POST['fecha2']);

        

        $ordenes = DB::table($settings->prefijo_wp.'postmeta')

                    ->select('post_id')

                    ->where('meta_key', '=', '_customer_user')

                    ->where('meta_value', '=', Auth::user()->ID)

                    ->orderBy('post_id', 'DESC')

                    ->get();

        return view('dashboard.buscarpersonalorder')->with(compact('ordenes','primero','segundo'));



    }

    /**
     * Genera la Informacion de las ordenes de la red
     *
     * @param integer $order_id
     * @param array $array_datos
     * @param integer $level
     * @param string $nombre
     * @param array $fecha
     * @param string $correo
     * @return void
     */
    public function getDetailsOrder($order_id, $array_datos, $level, $nombre, $fecha, $correo){
        $settings = Settings::first();
        $fechaOrden = DB::table($settings->prefijo_wp.'posts')
                        ->select('post_date')
                        ->where('ID', '=', $order_id)
                        ->first();

        $type_activacion = DB::table($settings->prefijo_wp.'posts')
                        ->select('to_ping')
                        ->where('ID', '=', $order_id)
                        ->first();
		$nombreCompleto = $nombre;
        
        $itemsOrden = DB::table($settings->prefijo_wp.'woocommerce_order_items')
                        ->select('order_item_name')
                        ->where('order_id', '=', $order_id)
                        ->where('order_item_type', '=', 'line_item')
                        ->get();
        $estadoOrden = DB::table($settings->prefijo_wp.'posts')
                        ->select('post_status')
                        ->where('ID', '=', $order_id)
                        ->first();
       

        $items = "";
        foreach ($itemsOrden as $item){
            $items = $items." ".$item->order_item_name;
        }

        $estadoEntendible = $this->statusOrdenWP($estadoOrden->post_status);

        if ($estadoEntendible != 'No Disponible') {
            if (!empty($fecha)) {
                $fechaCompra = new Carbon($fechaOrden->post_date);
                if ($fechaCompra->format('ymd') >= $fecha['primero']->format('ymd') && $fechaCompra->format('ymd') <= $fecha['segundo']->format('ymd')) {
                    array_push($array_datos, array(
                        'idorden' =>$order_id, 
                        'nombreusuario' => $nombreCompleto, 
                        'correouser' => $correo,
                        'fechacompra' => $fechaOrden->post_date, 
                        'producto' => $items, 
                        'total' => $this->indexControl->getShoppingTotal($order_id), 
                        'nivel' => $level, 
                        'activacion' => $type_activacion->to_ping,
                        'estado' => $estadoEntendible) 
                    );
                }
            } else {
                array_push($array_datos, array(
                    'idorden' =>$order_id, 
                    'nombreusuario' => $nombreCompleto, 
                    'correouser' => $correo,
                    'fechacompra' => $fechaOrden->post_date, 
                    'producto' => $items, 
                    'total' => $this->indexControl->getShoppingTotal($order_id), 
                    'nivel' => $level, 
                    'activacion' => $type_activacion->to_ping,
                    'estado' => $estadoEntendible)
                );
            }
        }
        return($array_datos);
    }

    /**
     * Permite saber el estado de las transaciones de wp
     *
     * @param string $estado
     * @return string
     */
    private function statusOrdenWP($estado): string
    {
        $estadoEntendible = 'No Disponible';
            if ($estado != null) {
                switch ($estado) {
                    case 'wc-completed':
                        $estadoEntendible = 'Completado';
                        break;
                    case 'wc-pending':
                        $estadoEntendible = 'Pendiente de Pago';
                        break;
                    case 'wc-processing':
                        $estadoEntendible = 'Procesando';
                        break;
                    case 'wc-on-hold':
                        $estadoEntendible = 'En Espera';
                        break;
                    case 'wc-cancelled':
                        $estadoEntendible = 'Cancelado';
                        break;
                    case 'wc-refunded':
                        $estadoEntendible = 'Reembolsado';
                        break;
                    case 'wc-failed':
                        $estadoEntendible = 'Fallido';
                        break;
                }
            }
        return $estadoEntendible;
    }

    public function network_orders(){

        view()->share('title', 'Ordenes de Red');
        $fecha = Carbon::now();
        $compras = [];
        if (Auth::user()->ID == 1) {
            $ordenes = DB::table('wp_posts')
            ->select('*')
            ->whereDate('post_date', '>', $fecha->subMonth(1))
            ->where([
                ['post_type', '=', 'shop_order'],
            ])->paginate(100);
            $compras = $this->pucharseAdmin(null, $ordenes);
        }else{
            $ordenes = null;
            $compras = $this->pucharseUser(Auth::user()->ID, []);
        }

        return view('dashboard.networkOrders')->with(compact('compras', 'ordenes'));
    }

    /**
     * Permite procesar las compra que vera el admin
     *
     * @param array $filtro
     * @return array
     */
    public function pucharseAdmin($filtro, $ordenes): array
    {
        $settings = Settings::first();
        $compras = [];
        foreach ($ordenes as $orden) {
            $estadoEntendible = $this->statusOrdenWP($orden->post_status);
            $itemsOrden = DB::table($settings->prefijo_wp.'woocommerce_order_items')
                        ->select('order_item_name')
                        ->where('order_id', '=', $orden->ID)
                        ->where('order_item_type', '=', 'line_item')
                        ->get();

            $items = "";
            foreach ($itemsOrden as $item){
            $items = $items." ".$item->order_item_name;
            }
            $iduser = $this->indexControl->getIdUser($orden->ID);
            $user = User::find($iduser);
            if ($user != null) {
                if ($filtro != null) {
                    if ($orden->to_ping == $filtro) {
                        $compras[] = [
                            'idorden' => $orden->ID,
                            'nombreusuario' => $user->display_name,
                            'correouser' => $user->user_email,
                            'fechacompra' => $orden->post_date, 
                            'producto' => $items, 
                            'total' => $this->indexControl->getShoppingTotal($orden->ID), 
                            'nivel' => 0, 
                            'activacion' => $orden->to_ping,
                            'estado' => $estadoEntendible 
                        ];
                    }
                }else{
                    $compras[] = [
                        'idorden' => $orden->ID, 
                        'nombreusuario' => $user->display_name, 
                        'correouser' => $user->user_email,
                        'fechacompra' => $orden->post_date, 
                        'producto' => $items, 
                        'total' => $this->indexControl->getShoppingTotal($orden->ID), 
                        'nivel' => 0, 
                        'activacion' => $orden->to_ping,
                        'estado' => $estadoEntendible
                    ];
                }
            }
        };
        return $compras;               
    }

    /**
     * Permite traer las compras de la red de un usuario
     *
     * @param integer $iduser
     * @param array $fecha
     * @return array
     */
    public function pucharseUser($iduser, $fecha): array
    {
        $settings = Settings::first();
        $TodosUsuarios = $this->indexControl->getChidrens2($iduser, [], 1, 'referred_id', 0);
        $compras = array();
        if (!empty($TodosUsuarios)) {
            foreach($TodosUsuarios as $user){
                $ordenes = DB::table($settings->prefijo_wp.'postmeta')
                            ->select('post_id')
                            ->where('meta_key', '=', '_customer_user')
                            ->where('meta_value', '=', $user['ID'])
                            ->orderBy('post_id', 'DESC')
                            ->get();
                foreach ($ordenes as $orden){
                    $compras = $this->getDetailsOrder($orden->post_id, $compras, $user->nivel, $user->display_name, $fecha, $user->user_email);
                }
            }
        }
        return $compras;
    }


    public function network_orders_filtre(Request $request){

        view()->share('title', 'Ordenes de Red');
        $compras = [];
        $ordenes = null;
        $fecha = Carbon::now();
        if (Auth::user()->ID == 1) {
            $ordenes = DB::table('wp_posts')
            ->select('*')
            ->whereDate('post_date', '>', $fecha->subDays(15))
            ->where([
                ['post_type', '=', 'shop_order'],
                ['to_ping', '=', $request->filtro]
            ])->get();
            $compras = $this->pucharseAdmin($request->filtro, $ordenes);
            $ordenes = null;
        }else{
            $compras = $this->pucharseUser(Auth::user()->ID, []);
        }

        return view('dashboard.networkOrders')->with(compact('compras', 'ordenes'));

    }

    

    

     public function buscarnetworkorder(){
          // TITLE
          view()->share('title', 'Ordenes de Red');
         $TodosUsuarios = $this->indexControl->getChidrens2(Auth::user()->ID, [], 1, 'referred_id', 0);
         $settings = Settings::first();
        $compras = array();

        $fecha = [
            'primero' => new Carbon($_POST['fecha1']),
            'segundo' => new Carbon($_POST['fecha2'])
        ];
         if (!empty($TodosUsuarios)) {
        foreach($TodosUsuarios as $user){

            $ordenes = DB::table($settings->prefijo_wp.'postmeta')
                            ->select('post_id')
                            ->where('meta_key', '=', '_customer_user')
                            ->where('meta_value', '=', $user['ID'])
                            ->orderBy('post_id', 'DESC')
                            ->get();
            foreach ($ordenes as $orden){
                $compras = $this->getDetailsOrder($orden->post_id, $compras, $user->nivel, $user->display_name, $fecha, $user->user_email);
            }
        }
    }

        

        return view('dashboard.networkOrders')->with(compact('compras'));

    }

    

    public function buscar(Request $request){

          // TITLE

          view()->share('title', 'Buscar Usuario');



      

     return view('admin.buscar');

    }

    

     public function vista(Request $request){

        // TITLE
        view()->share('title', 'Buscar Usuario');
        $user=User::search($request->get('user_email'))->orderBy('id','ASC')->paginate(1);
        return view('admin.vista')->with('user',$user);
    }

    /**
     * Lleva a la vista de los reportes de los directos
     *
     * @return void
     */
    public function indexReportDirectDate()
    {
        view()->share('title', 'Buscar Usuario');
        $data = [];
        return view('admin.reportDirect', compact('data'));
    }

    public function reportDirectDate(Request $reques)
    {
        $validate = $reques->validate([
            'iduser' => ['required', 'numeric'],
            'desde' => ['required', 'date'],
            'hasta' => ['required', 'date']
        ]);

        $user = User::find($reques->iduser);
        $fechaDesde = new Carbon($reques->desde);
        $fechaHasta = new Carbon($reques->hasta);
        $totalCompra = 0;
        try {
            if ($validate) {
                if ($fechaHasta < $fechaDesde) {
                    return redirect()->back()->with('msj3', 'La fecha hasta no puede ser mayor a la fecha desde');
                }
                $referidos = $this->indexControl->getChidrens2($reques->iduser, [], 1, 'referred_id', 0);
                foreach ($referidos as $refes) {
                    $compras = $this->indexControl->getInforShopping($refes->ID);
                    foreach ($compras as $compra) {
                        $fechaCompra = new Carbon($compra->fecha);
                        if ($fechaDesde >= $fechaCompra && $fechaCompra <= $fechaHasta) {
                            $totalCompra = ($totalCompra + $compra->total);
                        }
                    }
                }
            }
            $data = [
                'iduser' => $user->ID,
                'name' => $user->display_name,
                'total' => $totalCompra,
                'desde' => $fechaDesde->format('d-m-Y'),
                'hasta' => $fechaHasta->format('d-m-Y'),
            ];
            return view('admin.reportDirect', compact('data'));
        } catch (\Throwable $th) {
            \Log::error('Reporte Directo ->'. $th);
            return redirect()->back()->with('msj', 'Ocurrio un error, por favor contacte al administrador');
        }
    }

}

