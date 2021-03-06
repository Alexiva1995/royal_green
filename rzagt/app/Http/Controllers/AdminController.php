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
	function __construct()
	{
        // TITLE
        $this->indexControl = new IndexController;
        $this->rangoControl = new RangoController;
	}



    public function index()
    {
        $data = $this->getDataDashboard(Auth::user()->ID);
        view()->share('title', 'Resumen');
        $principal = 1;
        return view('dashboard.index', compact('data', 'principal'));
    }

    public function getDataDashboard($iduser)
    {
        $user = User::find($iduser);

        $comi = new ComisionesController;
        // $comi->registePackageToRentabilizar($iduser);
        if ($iduser == 614) {
            // $comi->bonoBinario();
            // $comi->payBonus();
            // $comi->eliminarRegistros(86452);
            // $comi->recorrerDuplicados();
            // $comi->despagarComisionesErroneas('Felipewilches1999@gmail.com');
            // dump('division');
            // $comi->despagarComisionesErroneas('Juanrestrepo11978@gmail.com');
            // $this->indexControl->ordenesSistema();
            // $this->indexControl->activarPaquetes();
            // $comi->arreglarBilletera();
            // $comi->puntosBinarios();
            // $comi->usuarioEliminarPuntos('jdrd31@mail.ru');
            // dd('parar 3');
        }

        // if ($iduser == 1) {
        //     $comi->payBonus();
        //     // $this->indexControl->ordenesSistema();
        // }

        $activacion = new ActivacionController;
        if ($iduser != 1 && $iduser != 614) {
            $activacion->activarUsuarios($iduser);
        }

        $paquetes = DB::table('log_rentabilidad')->get();
        if ($user->ID != 1) {
            $paquetes = DB::table('log_rentabilidad')->where('iduser', $iduser)->orderBy('id', 'desc')->take(1)->get();
        }

        $walletlast = Wallet::where([
            ['iduser', '=', $iduser],
            ['debito', '!=', 0]
        ])->orWhere([
            ['iduser', '=', $iduser],
            ['credito', '!=', 0]
        ])
        ->orderBy('id', 'DESC')->get()->take(10);
        $arrayWallet = [];


        foreach ($walletlast as $wallet) {
            $arrayWallet [] = [
                'signo' => ($wallet->tipotransacion == 2) ? 0 : 1,
                'monto' => ($wallet->tipotransacion == 2) ? $wallet->debito : $wallet->credito,
                'tipo' => ($wallet->tipotransacion == 2) ? 'Comision' : 'Retiro',
                'fecha' => date('Y-m-d', strtotime($wallet->created_at))
            ];
        }

        foreach ($paquetes as $paquete) {
            $paquete->img = asset('assets/paquetes/rg'.$paquete->precio.'.png');
            $paquete->detalles_producto = json_decode($paquete->detalles_producto);
        }

        $this->rangoControl->checkRango($iduser);

        $bienvenida = $this->indexControl->bonoBienvenida($iduser);

        $data = [
            'paquetes' => $paquetes,
            'wallets' => $arrayWallet,
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

    /**

     * Permite Borrar a todos los usuarios del sistema menos al admin

     *

     * @return void

     */

    public function deleteTodos()

        {

            $usuario = User::All();



		foreach ($usuario as $usuari) {

			if ($usuari->ID != 1) {

            $user = User::find($usuari->ID);

            DB::table('user_campo')->where('ID', $usuari->ID)->delete();

            $user->delete();  

            }

		}

            return redirect('office/admin/userrecords')->with('msj', 'Todos los usuarios han sidos borrados menos el Administrador');

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
        
        $compras = [];
        if (Auth::user()->ID == 1) {
            $ordenes = DB::table('wp_posts')
            ->select('*')
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
        if (Auth::user()->ID == 1) {
            $ordenes = DB::table('wp_posts')
            ->select('*')
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

}

