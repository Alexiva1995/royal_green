<?php



namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use App\Rol;
use App\User;
use App\Wallet;
use App\Permiso;
use App\Monedas;
use App\Settings;
use App\Notification;
use App\SettingsEstructura;

use App\Http\Controllers\IndexController;



class AdminController extends Controller

{

	function __construct()

	{

        // TITLE

		view()->share('title', 'Panel de administración');

	}



    public function index()
    {

        if (Auth::user()->ID == 2) {
            $comision = new ComisionesController;
            $comision->clubBono(Auth::user()->ID);
        }
        
        $moneda = Monedas::where('principal', 1)->get()->first();
        session(['tienda' => '0']);
        $settings = Settings::first();
        $settingEstructura = SettingsEstructura::find(1);
        $cantReferidosDirectos = count($this->getReferreds(Auth::user()->ID));
        $cantReferidosIndirectos = 0;
        $cantReferidosActivos = 0;
        $TodosUsuarios = $this->generarArregloUsuario(Auth::user()->ID);
        foreach($TodosUsuarios as $user){
            if ($user['nivel'] > 1){
                $cantReferidosIndirectos++;
            }
            if ($user['status'] == 1){
                $cantReferidosActivos++;
            }
        }
        $fullname= '';

        $datanombre = DB::table('user_campo')->select('firstname', 'lastname')->where('ID', Auth::user()->ID)->get();

        if (!empty($datanombre->toArray())) {

            $fullname = $datanombre[0]->firstname.' '.$datanombre[0]->lastname;

        }

        
        $rolActual = '';
        $rolSig = '';
        $cantRoles = Rol::all()->count('id');
        $logo = asset('assets/img/logo-light.png');
        if ($cantRoles > Auth::user()->rol_id) {
            $tmprol = Rol::find(Auth::user()->rol_id);
            $tmprol2 = Rol::find((Auth::user()->rol_id+1));
            $rolActual = $tmprol->name;
            $logo = asset('assets/img/logo-light.png');
            $img_rolActual = (!empty($tmprol->imagen)) ? asset('rangos/'.$tmprol->imagen) : $logo;
            $rolSig = $tmprol->name;
            $img_rolSig = (!empty($tmprol->imagen)) ? asset('rangos/'.$tmprol->imagen) : $logo;
            if (!empty($tmprol2)) {
                $rolSig = $tmprol2->name;
                $img_rolSig = (!empty($tmprol2->imagen)) ? asset('rangos/'.$tmprol2->imagen) : $logo;
            }
        }elseif ($cantRoles == Auth::user()->rol_id) {
            $tmprol = Rol::find(Auth::user()->rol_id);
            $rolActual = $tmprol->name;
            $img_rolActual = (!empty($tmprol->imagen)) ? asset('rangos/'.$tmprol->imagen) : $logo;
            $rolSig = $tmprol->name;
            $img_rolSig = (!empty($tmprol->imagen)) ? asset('rangos/'.$tmprol->imagen) : $logo;
        }
        // activacion de usuarios

        $fechaProxActivacion = '';

        if (Auth::user()->ID >= 3) {
            $activacion = new ActivacionController;
            $activacion->activarUsuarios(Auth::user()->ID);
            // $this->eliminarOrdenPostmetas();
            $comision = new ComisionesController;
            $comision->rentabilidadMensual();
        }

        $namePack = 'Sin Paquete';
        if (!empty(Auth::user()->paquete)) {
            $tmppack = json_decode(Auth::user()->paquete);
            $namePack = $tmppack->nombre;
        }

        // Informacion del Index

            $informacion = new IndexController;

            // obtiene a los nuevos miembros de los usuarios

            if (Auth::user()->ID != 1) {

                $new_member = $informacion->newMembers(Auth::user()->ID);

            }else{

                $new_member = User::select('display_name as nombre', 'created_at as fecha', 'avatar')->get()->sortByDesc('created_at')->take(7)->toArray();

            }

            $ganancias = Wallet::where([
                ['iduser', '=', Auth::user()->ID],
                ['descripcion', '!=', 'Pago Rechazado por el Administrador']
            ])->get()->sum('debito');

            // cantidad de todos los rangos en el sistema

            // $rangos = json_decode($informacion->chartRangos(Auth::user()->ID));

            // Ranking de mayo usuario con mas comisiones

            // $rankingComisiones = $informacion->rankingComisiones();

            // Ranking de mayo usuario con mas Ventas

            // $rankingVentas = $informacion->rankingVentas();

            // noticia

            //  $noticias = $informacion->noticias();

            // cantidad de ventas 

            // $cantventas = $informacion->countOrderNetwork(Auth::user()->ID);

            // cantidad de montos de  ventas 

            // $cantventasmont = $informacion->countOrderNetworkMont(Auth::user()->ID);

            // todos los usuarios de un determinado

            // $cantAllUsers = (Auth::user()->ID != 1) ? count($TodosUsuarios) : User::all()->count('ID') ;

            // $cantAllUsers = ($cantAllUsers == 0) ? 1 : $cantAllUsers;

            // todos los Tickes

            // $contTicket = Ticket::all()->count('ID');

            $puntosRed = $this->puntosRed(Auth::user()->ID);


            
            $porc_rentabilidad = (empty(Auth::user()->porc_rentabilidad)) ? 0 : Auth::user()->porc_rentabilidad;
            $tmp_rentabilidad = (empty(Auth::user()->rentabilidad)) ? 1 : Auth::user()->rentabilidad;
            $tmp_rentabilidad = ($tmp_rentabilidad == 0) ? 1 : $tmp_rentabilidad;
            $rentabilidad = (($porc_rentabilidad/$tmp_rentabilidad) * 100);

        // Permite Registrar las fichas en el sistema

        //fin Informacion Index


 $permiso = [];

            if(Auth::user()->rol_id == 0){ 
            
//            $this->ActualizarTodo();

                $permiso = Permiso::where('iduser', Auth::user()->ID)->get()->toArray();

                         DB::table($settings->prefijo_wp.'users')

                        ->where('ID', '=', Auth::user()->ID)

                        ->update(['status' => 1 ]);

    

                       Auth::user()->status = true;

                         }

                         view()->share('title', 'Balance General');

        return view('dashboard.index')->with(compact(
            'cantReferidosDirectos', 'cantReferidosIndirectos', 'cantReferidosActivos', 'fechaProxActivacion', 'new_member',
            'fullname', 'permiso', 'moneda',
            'rolActual', 'rolSig', 'puntosRed', 'img_rolActual', 'img_rolSig', 'rentabilidad', 'ganancias', 'namePack'
            ));

    }


    /**

     * Función que devuelve los patrocinados de un determinado usuario

     * 

     * @access private

     * @param int $id - id del usuario 

     * @return array

     */

    private function getSponsor($user_id){

        $tmp = User::select('ID', 'user_email', 'status', 'display_name', 'created_at', 'rol_id', 'paquete', 'referred_id')->where('position_id', $user_id)->get()->toArray();

		return $tmp;

    }

    

   /**

     * Función que devuelve los referidos de un determinado usuario

     * 

     * @access private

     * @param int $user_id - id del usuario

     * @return array - listado de los referidos del usuario

     */

    public function getReferreds($user_id){

        $referidos = User::select('ID', 'user_email', 'status', 'display_name', 'created_at', 'rol_id', 'paquete', 'referred_id')->where('referred_id', $user_id)->get()->toArray();

		return $referidos;

	}

    

    /**

     * Obtienen a todo los usuarios referidos de un usuario determinado

     * 

     * @access private

     * @param array $arregloUser - listado de usuario, int $niveles - niveles a recorrer,

     * int $para - nivel a detenerse, array $allUser - todos los usuario referidos

     * @return array - listado de todos los usuario

     */

	private function getReferredsAll($arregloUser, $niveles, $para, $allUser, $tipoestructura)

    {

        if ($niveles <= $para) {

            $llaves =  array_keys($arregloUser);

            $finFor = end($llaves);

            $cont = 0;

            $tmparry = [];

            foreach ($arregloUser as $user) {

                $patrocinado = User::find($user['referred_id']);

                $allUser [] = [

                    'ID' => $user['ID'],

                    'email' => $user['user_email'],

                    'nombre' => $user['display_name'],

                    'status' => $user['status'],

                    'nivel' => $niveles,

                    'patrocinador' => $patrocinado->display_name,

                    'fecha' => $user['created_at'],

                    'rol' => $user['rol_id'],

                    'paquete' => $user['paquete']

                ];

                if ($tipoestructura == 'arbol') {

                    if (!empty($this->getReferreds($user['ID']))) {

                        $tmparry [] = $this->getReferreds($user['ID']);

                    }

                }else{

                    if (!empty($this->getSponsor($user['ID']))) {

                        $tmparry [] = $this->getSponsor($user['ID']);

                    }

                }

                if ($finFor == $cont) {

                    if (!empty($tmparry)) {

                        $tmp2 = $tmparry[0];

                        for($i = 1; $i < count($tmparry); $i++){

                            $tmp2 = array_merge($tmp2,$tmparry[$i]);

                        }

                        $this->getReferredsAll($tmp2, ($niveles+1), $para, $allUser, $tipoestructura);

                    }else{

                        $GLOBALS['allUsers'] = $allUser;

                    }

                }else{

                    $cont++;

                }

          }

        }else{

            $GLOBALS['allUsers'] = $allUser;

        }

    }



        /**

     * Devuelve el tipo de estructura con que se esta trabajando en el sistema

     * 

     * @access public

     * @return string

     */

    public function obtenerEstructura()

    {

        $settingEstructura = SettingsEstructura::find(1);

        $estructura = "";

        if ($settingEstructura->tipoestructura == 'arbol') {

            $estructura = "arbol";

        } elseif ($settingEstructura->tipoestructura == 'matriz') {

            $estructura = "matriz";

        }else{

            if ($settingEstructura->estructuraprincipal == 1) {

                if ($settingEstructura->usuarioprincipal == 1) {

                    $estructura = "arbol";

                } else {

                    $estructura = "matriz";

                }

            } else {

                if ($settingEstructura->usuarioprincipal == 1) {

                    $estructura = "arbol";

                } else {

                    $estructura = "matriz";

                }

            }

        }

        return  $estructura;

    }





    /**

     * Genera el Arreglo de los usuarios referidos

     * 

     * @access public

     * @param $iduser - id del usuario 

     * @return array

     */

    public function generarArregloUsuario($iduser)

    {

        $settingEstructura = SettingsEstructura::find(1);

        $GLOBALS['allUsers'] = [];

        if ($iduser == 1) {
            $settingEstructura->cantnivel = 500;
        }else{
            $settingEstructura->cantnivel = 4;
        }

        // if ($this->obtenerEstructura() == 'arbol') {

            $referidosDirectos = $this->getReferreds($iduser);

            $this->getReferredsAll($referidosDirectos, 1, $settingEstructura->cantnivel, [], 'arbol');

        // } else {

        //     $referidosDirectos = $this->getSponsor($iduser);

        //     $this->getReferredsAll($referidosDirectos, 1, $settingEstructura->cantnivel, [], 'matriz');

        // }



        return $GLOBALS['allUsers'];

    }





    public function direct_records(){

        // TITLE

        view()->share('title', 'Usuarios Directos');



        // DO MENU

        view()->share('do', collect(['name' => 'network', 'text' => 'Red de Usuarios']));



            $referidosDirectos = User::where('referred_id', '=', Auth::user()->ID)

                                ->orderBy('created_at', 'DESC')

                                ->get();

        // dd($referidosDirectos);

        //******************

        //Marcar como leídas las notificaciones pendientes de Registros Directos

        $notificaciones_pendientes = DB::table('notifications')

                                        ->select('id')

                                        ->where('user_id', '=', Auth::user()->ID)

                                        ->where('notification_type', '=', 'RD')

                                        ->where('status', '=', 0)

                                        ->get();



        foreach ($notificaciones_pendientes as $not){

            Notification::find($not->id)->update(['status' => 1]);

        }

        //********************



        return view('dashboard.directRecords')->with(compact('referidosDirectos'));

    }

    

    public function buscardirectos(){

        // TITLE

        view()->share('title', 'Usuarios Directos');



        // DO MENU

        view()->share('do', collect(['name' => 'network', 'text' => 'Red de Usuarios']));



        $primero = new Carbon($_POST["fecha1"]);

        $segundo = new Carbon($_POST["fecha2"]);

         

        // if ($this->obtenerEstructura() == 'arbol') {

            $referidosDirectos =User::whereDate("created_at",">=",$primero)

             ->whereDate("created_at","<=",$segundo)

             ->where('referred_id', '=', Auth::user()->ID)

             ->orderBy('created_at', 'DESC')

             ->get();

        // } else {

        //     $referidosDirectos =User::whereDate("created_at",">=",$primero)

        //      ->whereDate("created_at","<=",$segundo)

        //      ->where('sponsor_id', '=', Auth::user()->ID)

        //      ->orderBy('created_at', 'DESC')

        //      ->get();

        // }

      



        return view('dashboard.buscardirectos')->with(compact('referidosDirectos'));

    }

    

    public function buscarnetwork(){

        // TITLE

        view()->share('title', 'Usuarios en Red');

        view()->share('do', collect(['name' => 'network', 'text' => 'Red de Usuarios']));


        $allReferido = $this->generarArregloUsuario(Auth::user()->ID);
        return view('dashboard.buscarnetwork')->with(compact('allReferido','primero','segundo'));

    }

    public function buscarnetworknivel(Request $request)
    {
                // TITLE

                view()->share('title', 'Usuarios en Red');

                // DO MENU
        
                view()->share('do', collect(['name' => 'network', 'text' => 'Red de Usuarios']));
                
                $allReferidotmp = $this->generarArregloUsuario(Auth::user()->ID);
                $allReferido = [];
                foreach ($allReferidotmp as $user ) {
                    if ($request->nivel > 0) {
                        if ($user['nivel'] == $request->nivel) {
                            $allReferido [] = $user;
                        }
                    } else {
                            $allReferido [] = $user;
                    }
                    
                }
                return view('dashboard.networkRecords')->with(compact('allReferido'));
    }



    public function network_records(){

        // TITLE

        view()->share('title', 'Usuarios en Red');



        // DO MENU

        view()->share('do', collect(['name' => 'network', 'text' => 'Red de Usuarios']));

        // $GLOBALS['allUsers'] = [];
        // $settingEstructura = SettingsEstructura::find(1);
        // $referidosDirectos = $this->getReferreds(Auth::user()->ID);
        // $this->getReferredsAll($referidosDirectos, 1, $settingEstructura->cantnivel, [], 'matris');
        
        $allReferido = $this->generarArregloUsuario(Auth::user()->ID);

        //******************

        //Marcar como leídas las notificaciones pendientes de Registros Indirectos

        $notificaciones_pendientes = DB::table('notifications')

                                        ->select('id')

                                        ->where('user_id', '=', Auth::user()->ID)

                                        ->where('notification_type', '=', 'RI')

                                        ->where('status', '=', 0)

                                        ->get();



        foreach ($notificaciones_pendientes as $not){

            Notification::find($not->id)->update(['status' => 1]);

        }

        //********************



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

            return redirect('mioficina/admin/userrecords')->with('msj', 'Todos los usuarios han sidos borrados menos el Administrador');

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
         //******************
        //Marcar como leídas las notificaciones pendientes de Órdenes Directas
        $notificaciones_pendientes = DB::table('notifications')
                                        ->select('id')
                                        ->where('user_id', '=', Auth::user()->ID)
                                        ->where('notification_type', '=', 'OD')
                                        ->where('status', '=', 0)
                                        ->get();
        foreach ($notificaciones_pendientes as $not){
            Notification::find($not->id)->update(['status' => 1]);
        }
        //********************
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
     * @access public
     * @param int $order_id - orden de la compra, array $array_datos - informacion de las compras, int $level - nivel del usuario
     * @return array
     */
    public function getDetailsOrder($order_id, $array_datos, $level, $nombre, $fecha){
        $settings = Settings::first();
        $numOrden = DB::table($settings->prefijo_wp.'postmeta')
                        ->select('meta_value')
                        ->where('post_id', '=', $order_id)
                        ->where('meta_key', '=', '_order_key')
                        ->first();
        $fechaOrden = DB::table($settings->prefijo_wp.'posts')
                        ->select('post_date')
                        ->where('ID', '=', $order_id)
                        ->first();
        $totalOrden = DB::table($settings->prefijo_wp.'postmeta')
                        ->select('meta_value')
                        ->where('post_id', '=', $order_id)
                        ->where('meta_key', '=', '_order_total')
                        ->first();
        $nombreOrden = DB::table($settings->prefijo_wp.'postmeta')
                        ->select('meta_value')
                        ->where('post_id', '=', $order_id)
                        ->where('meta_key', '=', '_billing_first_name')
                        ->first();
        $apellidoOrden = DB::table($settings->prefijo_wp.'postmeta')
                        ->select('meta_value')
                        ->where('post_id', '=', $order_id)
                        ->where('meta_key', '=', '_billing_last_name')
                        ->first();
		$nombreCompleto = $nombre;
        if (!empty($nombreOrden->meta_value) && !empty($apellidoOrden->meta_value)) {
    	$nombreCompleto = $nombreOrden->meta_value." ".$apellidoOrden->meta_value;
        }
        $itemsOrden = DB::table($settings->prefijo_wp.'woocommerce_order_items')
                        ->select('order_item_name')
                        ->where('order_id', '=', $order_id)
                        ->where('order_item_type', '=', 'line_item')
                        ->get();
        $estadoOrden = DB::table($settings->prefijo_wp.'posts')
                        ->select('post_status')
                        ->where('ID', '=', $order_id)
                        ->first();
        $estadoEntendible = '';
        switch ($estadoOrden->post_status) {
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
        $items = "";
        foreach ($itemsOrden as $item){
            $items = $items." ".$item->order_item_name;
        }
        if (!empty($fecha)) {
            $fechaCompra = new Carbon($fechaOrden->post_date);
            if ($fechaCompra->format('ymd') >= $fecha['primero']->format('ymd') && $fechaCompra->format('ymd') <= $fecha['segundo']->format('ymd')) {
                array_push($array_datos, array($order_id, $nombreCompleto, $fechaOrden->post_date, $items, $totalOrden->meta_value, $level, $estadoEntendible) );
            }
        } else {
            array_push($array_datos, array($order_id, $nombreCompleto, $fechaOrden->post_date, $items, $totalOrden->meta_value, $level, $estadoEntendible) );
        }
        
        
        return($array_datos);
    }



    /**

     * Genera todas las ordenes de red de usuarios

     * 

     * @access public

     * @return view - vista de transacciones

     */

    public function network_orders(){

        view()->share('title', 'Ordenes de Red');

        $settings = Settings::first();

        $TodosUsuarios = $this->generarArregloUsuario(Auth::user()->ID);

        $compras = array();

        $fecha = [];

         if (!empty($TodosUsuarios)) {

        foreach($TodosUsuarios as $user){

            $ordenes = DB::table($settings->prefijo_wp.'postmeta')

                            ->select('post_id')

                            ->where('meta_key', '=', '_customer_user')

                            ->where('meta_value', '=', $user['ID'])

                            ->orderBy('post_id', 'DESC')

                            ->get();



            foreach ($ordenes as $orden){

                $compras = $this->getDetailsOrder($orden->post_id, $compras, '1', $user['nombre'], $fecha);

            }

        }

    }



        //******************

        //Marcar como leídas las notificaciones pendientes de Órdenes en Red

        $notificaciones_pendientes = DB::table('notifications')

                                        ->select('id')

                                        ->where('user_id', '=', Auth::user()->ID)

                                        ->where('notification_type', '=', 'OR')

                                        ->where('status', '=', 0)

                                        ->get();



        foreach ($notificaciones_pendientes as $not){

            Notification::find($not->id)->update(['status' => 1]);

        }

        //********************



        return view('dashboard.networkOrders')->with(compact('compras'));

    }

    

    

     public function buscarnetworkorder(){
          // TITLE
          view()->share('title', 'Ordenes de Red');
         $TodosUsuarios = $this->generarArregloUsuario(Auth::user()->ID);
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
                $compras = $this->getDetailsOrder($orden->post_id, $compras, '1', $user['nombre'], $fecha);
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
     * obtengo los puntos de mi red
     * 
     * @access private
     * @param int $iduser
     * @return integer
     */
    private function puntosRed($iduser)
    {
        // $usuario = User::find($iduser);
        // $resul = false;
        // $todoUsuarios = $this->generarArregloUsuario($iduser);
        // $inicio = Carbon::now()->startOfMonth();
        // $fin = Carbon::now()->endOfMonth();
        $totalgrupa = 0;
        $lado1 = 0; $lado2 = 0;
        // foreach ($todoUsuarios as $user) {
            $lado1 = ( $lado1 + Wallet::where('iduser', $iduser)
                                ->get()->sum('puntosI'));
            $lado2 = ( $lado2 + Wallet::where('iduser', $iduser)
                                ->get()->sum('puntosD'));
        // }
        $totalgrupa = ($lado1 + $lado2);
        return $totalgrupa;
    }

    // /**
    //  * Permite eliminar las ordenes del postmetas
    //  *
    //  * @return void
    //  */
    // public function eliminarOrdenPostmetas()
    // {
    //     $sql = "SELECT * FROM `wp_postmeta` WHERE meta_value like 'wc_order%' ";
    //     $postmetas = DB::select($sql);
    //     foreach ($postmetas as $post) {
    //         if ($post->post_id < 5505) {
    //             DB::statement("DELETE FROM `wp_postmeta` WHERE post_id =".$post->post_id);
    //         }
            
    //     }
    // }

}

