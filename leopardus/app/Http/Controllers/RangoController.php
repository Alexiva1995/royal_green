<?php

namespace App\Http\Controllers;



use App\Commission;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\SettingsRol;
use App\Rol;
use App\SettingsEstructura;
use App\Settings;
use App\Wallet;
use App\Http\Controllers\ComisionesController;
use App\Http\Controllers\IndexController;

class RangoController extends Controller
{
	function __construct()
	{
     
    }
    
    public function listRangos()
    {
        view()->share('title', 'User Report By Rank');
        $users = User::All();
        $rol = Rol::all();
        $usuarios = [];
        foreach ($users as $user) {
            foreach ($rol as $item ) {
                $item->estado2 = 0;
                $verificar = DB::table('rolespagados')->where([
                    ['id_rol', '=', $item->id],
                    ['iduser', '=', $user->ID],
                ])->first();
                if (!empty($verificar) && $verificar->estado != 0) {
                    $item->estado2 = 1;
                } 
            }
            
            if ($user->rol_id > 1) {
                $usuarios [] = [
                    'id' => $user->ID,
                    'nombre' => $user->display_name,
                    'rolactual' => $user->rol_id,
                    'roles' => $rol
                ];
            }
        }
        return view('admin.rangos')->with(compact('usuarios'));
    }

    public function cambiarEstadoDelosrangos($iduser, $idrango, $estado)
    {
        $verificar = DB::table('rolespagados')->where([
            ['id_rol', '=', $idrango],
            ['iduser', '=', $iduser],
        ])->first();
        if (empty($verificar)) {
            DB::table('rolespagados')->insert([
                'id_rol' => $idrango,
                'iduser' => $iduser,
                'estado' => $estado
            ]);
            $msj = '';
            $tipo = 'msj';
            if ($estado == 1) {
                $msj = 'The prize was awarded';
            }elseif($estado == 2){
                $tipo = 'msj2';
                $msj = 'The prize was rejected';
            }
            return redirect()->route('info.list-rango')->with($tipo, $msj);
        }
    }
    
     /**
     * Función que devuelve los patrocinados de un determinado usuario
     * 
     * @access private
     * @param int $id - id del usuario 
     * @return array
     */
    private function getSponsor($user_id){
        $tmp = User::select('ID', 'user_email', 'status', 'display_name', 'created_at', 'puntos', 'paquete', 'rol_id')->where('position_id', $user_id)->get()->toArray();
		return $tmp;
    }
    /**
     * Función que devuelve los referidos de un determinado usuario
     * 
     * @access public
     * @param int $user_id - id del usuario
     * @return array - listado de los referidos del usuario
     */
    public function getReferreds($user_id){
        $referidos = User::select('ID', 'user_email', 'status', 'display_name', 'created_at', 'puntos', 'paquete', 'rol_id')->where('referred_id', $user_id)->get()->toArray();
		return $referidos;
	}
    
    /**
     * Obtienen a todo los usuarios referidos de un usuario determinado
     * 
     * @access public
     * @param array $arregloUser - listado de usuario, int $niveles - niveles a recorrer,
     * int $para - nivel a detenerse, array $allUser - todos los usuario referidos
     * @return array - listado de todos los usuario
     */
	public function getReferredsAll($arregloUser, $niveles, $para, $allUser, $tipoestructura)
    {
        if ($niveles <= $para) {
            $llaves =  array_keys($arregloUser);
            $finFor = end($llaves);
            $cont = 0;
            $tmparry = [];
            foreach ($arregloUser as $user) {
                $rol = Rol::find($user['rol_id']);
                $allUser [] = [
                    'ID' => $user['ID'],
                    'email' => $user['user_email'],
                    'nombre' => $user['display_name'],
                    'status' => $user['status'],
                    'nivel' => $niveles,
                    'rol' => $rol->name,
                    'idrol' => $user['rol_id'],
                    'fecha' => $user['created_at'],
                    'puntos' => $user['puntos'],
                    'paquete' => $user['paquete'],
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
        // if ($this->obtenerEstructura() == 'arbol') {
            $referidosDirectos = $this->getReferreds($iduser);
            $this->getReferredsAll($referidosDirectos, 1, $settingEstructura->cantnivel, [], 'arbol');
        // } else {
        //     $referidosDirectos = $this->getSponsor($iduser);
        //     $this->getReferredsAll($referidosDirectos, 1, $settingEstructura->cantnivel, [], 'matriz');
        // }

        return $GLOBALS['allUsers'];
    }


	/**
     * Devuelve el id de la compra
     * 
     * Función que devuelve el ID de las compras de un determinado usuario
     * que no hayan sido procesadas en una comisión anterior
     * 
     * @access private
     * @param int $user_id - id del usuario
     * @return int - id de la compra
     */
	private function getShopping($user_id){
        $settings = Settings::first();
        $comprasID = DB::table($settings->prefijo_wp.'postmeta')
                    ->select('post_id')
                    ->where('meta_key', '=', '_customer_user')
                    ->where('meta_value', '=', $user_id)
                    ->get();

        return $comprasID;
	}

	/**
     * Función que devuelve los datos de una compra determinada
     * 
     * @access private
     * @param int $shop_id - id de la compra
     * @return array - informacion de la compra
     */
	private function getShoppingDetails($shop_id){
        $settings = Settings::first();
		$datosCompra = DB::table($settings->prefijo_wp.'posts')
                        ->select('post_date', 'post_status')
                        ->where('ID', '=', $shop_id)
                        ->first();

        return $datosCompra;
    }

	/**
     * funcion para encontrar obtener los productos de la factura
     * 
     * @access private
     * @param int $shop_id - id de la compra
     * @return array - listado de los productos de la compra
     */
	private function getProductos($shop_id)
	{   
        $settings = Settings::first();
		$totalProductos = DB::table($settings->prefijo_wp.'woocommerce_order_items')
													->select('order_item_id')
													->where('order_id', '=', $shop_id)
													->get();

		return $totalProductos;
	}

	/**
     * funcion para obtener el id de los productos comprados
     * 
     * @access private
     * @param int $id_item - id de la orden de la factura
     * @return int el id del producto comprado
     */
	private function getIdProductos($id_item)
	{
        $settings = Settings::first();
		$valor = 0;
		$IdProducto = DB::table($settings->prefijo_wp.'woocommerce_order_itemmeta')
													->select('meta_value')
													->where('order_item_id', '=', $id_item)
													->where('meta_key', '=', '_product_id')
													->first();


        if (!empty($IdProducto)) {
            $valor = $IdProducto->meta_value;
        }
		return $valor;
	}

	/**
     * Función que devuelve el total pagado en una compra determinada
     * 
     * @access private
     * @param int $shop_id - id de la compra
     * @return float - el valor total de la compra
     */
	private function getShoppingTotal($shop_id){
        $settings = Settings::first();
		$totalCompra = DB::table($settings->prefijo_wp.'postmeta')
				        ->select('meta_value')
				        ->where('post_id', '=', $shop_id)
				        ->where('meta_key', '=', '_order_total')
				        ->first();

		return $totalCompra->meta_value;
	}
    
    /**
     * Verifica toda las condiciones para subir de rango al usuario
     * 
     * @param integer $user_id - el id de usuario a actualizar
     */
    public function ValidarRango($iduser)
    {
        $user = User::find($iduser);
        $settingsRol = SettingsRol::find(1);
        $rol_actual = $user->rol_id;
        $rol_new = $user->rol_id + 1;
        $rol = Rol::find($rol_new);
        $cantrol = Rol::all()->count('ID');

        $cantrequisito = 0;
        $cantaprobado = 0;

        if($cantrol > $rol_new){ 
            
            // validacion de paquetes
            $cantrequisito++;
            if ($this->ValidarPaquete($rol->paquete, $user->paquete)) {
                $cantaprobado++;
            }
            // verificacion por referidos
            if ($settingsRol->referidos == 1) {
                $cantrequisito++;
                if ($this->verificarReferidos($iduser, $rol->referidos, false, false)) {
                    $cantaprobado++;
                }
            }
            
            // verificacion por referidos directos
            if ($settingsRol->referidosd == 1) {
                $cantrequisito++;
                if ($this->verificarPaquetesORangos($iduser, $rol->rolnecesario,(int) $rol->referidosd)) {
                    $cantaprobado++;
                }
            }
            // dd($cantaprobado, $cantrequisito);
            // verificacion por referidos activos
            if ($settingsRol->referidosact == 1) {
                $cantrequisito++;
                if ($this->verificarReferidos($iduser, $rol->refeact, true, false)) {
                    $cantaprobado++;
                }
            }
            
            // verificacion por compras personal (puntos)
            if ($settingsRol->compras == 1) {
                $cantrequisito++;
                if ($rol->id == 10) {
                    if ($this->verificacionCompraPersonal($iduser, $rol->compras, 1)) {
                        $cantaprobado++;
                    }
                }else{
                    if ($this->verificacionCompraPersonal($iduser, $rol->compras, 0)) {
                        $cantaprobado++;
                    }
                }
            }
            
            // verificacion por compras grupales (puntos)
            if ($settingsRol->grupal == 1) {
                $cantrequisito++;
                if ($this->validarGrupal($iduser, $rol->grupal)) {
                    $cantaprobado++;
                }
            }

            // verificacion por comisiones
            if ($settingsRol->comisiones == 1) {
                $cantrequisito++;
                if ($this->verificacionComisiones($iduser, $rol->comisiones)) {
                    $cantaprobado++;
                }
            }

            if ($rol_actual == $rol->rolprevio) {
                if ($cantrequisito == $cantaprobado) {
                    $this->ActualizarRango($iduser, $rol_new);
                }
            }
        }
    }

    /**
     * Sube de Rango al Usuario
     * 
     * @access private
     * @param int $iduser - id usuario, int $rol_new - el rango a subir
     */
    private function ActualizarRango($iduser, $rol_new)
    {
        $usuario = User::find($iduser);
        $usuario->rol_id = $rol_new;
        $usuario->save();
        $settingsRol = SettingsRol::find(1);
        $rol = Rol::find($rol_new);
        if ($settingsRol->bonos == 1) {
            if ($rol->bonos > 0) {
                $comision = new ComisionesController;
                $concepto = "Bonus for Climbing the ".$rol->name.' Rank';
                $comision->guardarComision($iduser, 10, $rol->bonos, Auth::user()->user_email, 0, $concepto, 'bono');
            }
        }
        // $settings = Settings::first();
        // $rol = Rol::find($rol_new);
        // $rangowp = 'a:1:{s:'.$valors.':"'.strtolower($rol->name).'";b:1;}';
        // DB::table($settings->prefijo_wp.'usermeta')
        // 		->where('user_id', '=', $iduser)
        // 		->where('meta_key', '=', $settings->prefijo_wp.'capabilities')
        // 		->update(['meta_value' => $rangowp]);
    }

    /**
     * Permite validar los paquetes comprado
     *
     * @param string $requisito
     * @param string $verificacion
     * @return boolean
     */
    public function ValidarPaquete($requisito, $verificacion)
    {
        $resul = false;
        if (strcasecmp($requisito, $verificacion) === 0) {
            $resul = true;
        }else{
            if($requisito == 'Master Plus' && $verificacion == 'POWER'){
                $resul = true;
            }elseif ($requisito == 'Master Plus' && $verificacion == 'POWER MAX') {
                $resul = true;
            }elseif ($requisito == 'Master Plus' && $verificacion == 'MASTER INFINITY') {
                $resul = true;
            }elseif ($requisito == 'Power' && $verificacion == 'POWER MAX') {
                $resul = true;
            }elseif ($requisito == 'Power' && $verificacion == 'MASTER INFINITY') {
                $resul = true;
            }elseif ($requisito == 'Power Max' && $verificacion == 'MASTER INFINITY') {
                $resul = true;
            }
        }
        return $resul;
    }

    /**
     * Permite verificar la cantidad de usuario de un determinado rango
     *
     * @param integer $iduser
     * @param integer $ranfo
     * @param integer $cant
     * @return void
     */
    public function verificarPaquetesORangos($iduser, $rango, $cant)
    {
        $resul = false;
        $lado1 = 0; $lado2 = 0;
        $funciones = new IndexController;
        $inicio = Carbon::now()->startOfMonth();
        $fin = Carbon::now()->endOfMonth();
        // $todousuario = User::where([
        //     ['position_id', '=', $iduser],
        // ])->get();
        $suma = 0;
        $todousuario = $this->generarArregloUsuario($iduser);
        if (!empty($todousuario)) {
            foreach ($todousuario as $usuario) {
                if ($usuario['nivel'] == 1 && $usuario['idrol'] >= $rango && $usuario['status'] == 1) {
                    $suma++;
                }
                // if ($usuario->ladomatrix == 'D') {
                //     if ($usuario->rol_id >= $rango) {
                //         $lado1++;
                //     }
                //     $todousuarioD = $funciones->generarArregloUsuario($usuario->ID);
                //     if (!empty($todousuarioD)) {
                //         foreach ($todousuarioD as $usuarioD) {
                //             if ($usuarioD['rol'] >= $rango) {
                //                 $lado1++;
                //             }
                //         }
                //     }
                // }
                // if ($usuario->ladomatrix == 'I') {
                //     if ($usuario->rol_id >= $rango) {
                //         $lado2++;
                //     }
                //     $todousuarioD = $funciones->generarArregloUsuario($usuario->ID);
                //     if (!empty($todousuarioI)) {
                //         foreach ($todousuarioI as $usuarioI) {
                //             if ($usuarioI['rol'] >= $rango) {
                //                 $lado2++;
                //             }
                //         }
                //     }
                // }
            }
        }
        // $suma = 0;
        // if ($cant == 2) {
        //     if ($lado1 >= 1 && $lado2 >= 1) {
        //         $suma = ($lado1 + $lado2);
        //     }
        // }
        // if ($cant == 3) {
        //     if ($lado1 >= 1 && $lado2 >= 2) {
        //         $suma = ($lado1 + $lado2);
        //     }
        //     if ($lado1 >= 2 && $lado2 >= 1) {
        //         $suma = ($lado1 + $lado2);
        //     }
        // }
        // if ($cant == 4) {
        //     if ($lado1 >= 2 && $lado2 >= 2) {
        //         $suma = ($lado1 + $lado2);
        //     }
        // }
        // if ($cant == 5) {
        //     if ($lado1 >= 3 && $lado2 >= 2) {
        //         $suma = ($lado1 + $lado2);
        //     }
        //     if ($lado1 >= 2 && $lado2 >= 3) {
        //         $suma = ($lado1 + $lado2);
        //     }
        // }
        // if ($cant == 6) {
        //     if ($lado1 >= 3 && $lado2 >= 3) {
        //         $suma = ($lado1 + $lado2);
        //     }
        // }
        
        if ($suma >= $cant) {
            $resul = true;
        }

        return $resul;
    }
    /**
     * Verifica la condicion de referidos 
     * 
     * Revisa si la condicion de referidos o referidos activos son validas
     * 
     * @access private
     * @param int $iduser - id usuario, int $requisito - requisitos necesario, boolean $activo - si son los activos a sumar, boolean $directos - si son los directos a sumar
     * @return boolean
     */
    private function verificarReferidos($iduser, $requisito, $activo, $directos)
    {
        $todoUsuarios = $this->generarArregloUsuario($iduser);
        $cantReferidosActivos = 0;
        $cantReferidosDirectos = 0;
        $cantReferidos = 0;
        $resul = false;
        foreach($todoUsuarios as $user){
            $cantReferidos++;
            if ($directos) {
                if ($user['nivel'] == 1){
                    $cantReferidosDirectos++;
                }
            }
            if ($activo) {
                if ($user['status'] == 1){
                    $cantReferidosActivos++;
                }
            }
        }
        if ($directos) {
            if ($cantReferidosDirectos >= $requisito){
                $resul = true;
            }
        }
        if ($activo) {
            if ($cantReferidosActivos >= $requisito) {
                $resul = true;
            }
        }else{
            if ($cantReferidos >= $requisito) {
                $resul = true;
            }
        }
        return $resul;
    }

    /**
     * Permite Verificar las compras personales del usuario
     * 
     * @access private
     * @param integer $iduser - id usuario, 
     * @param float $montoRequisito - el monto a superar o igualar
     * @param integer $final
     * @return boolean
     */
    private function verificacionCompraPersonal($iduser, $montoRequisito, $final)
    {
        $user = User::find($iduser);
        $totalCompras = $this->getShopping($iduser);
        $comprastotalesuser = 0;
        $lado1 = 0; $lado2 = 0;
        if ($final == 1) {
            $lado1 = Wallet::where('iduser', $iduser)->get()->sum('puntosI');
            $lado2 = Wallet::where('iduser', $iduser)->get()->sum('puntosD');
            $comprastotalesuser = ($lado1 + $lado2);
        }else {
            $lado1 = Wallet::where('iduser', $iduser)->get()->sum('puntosI');
            $lado2 = Wallet::where('iduser', $iduser)->get()->sum('puntosD');
            if ($lado1 >= $lado2) {
                $comprastotalesuser = $lado2;
            }else{
                $comprastotalesuser = $lado1;
            }
            $comprastotalesuser += $user->puntosP;
        }
        if ($comprastotalesuser >= $montoRequisito) {
            return true;
        }else{
            return false;
        }       
    }

    /**
     * Valida que las compras grupales cumplan el requisito
     * 
     * @access private
     * @param int $iduser, float $montoRequisito
     * @return boolean
     */
    private function validarGrupal($iduser, $montoRequisito)
    {
        $usuario = User::find($iduser);
        $resul = false;
        $todoUsuarios = $this->generarArregloUsuario($iduser);
        $inicio = Carbon::now()->startOfMonth();
        $fin = Carbon::now()->endOfMonth();
        $totalgrupa = 0;
        $lado1 = Wallet::where('iduser', $iduser)
                    // ->whereDate('created_at', '>=', $inicio)
                    // ->whereDate('created_at', '<=', $fin)
                    ->get()->sum('puntosI');
        $lado2 = Wallet::where('iduser', $iduser)
                    // ->whereDate('created_at', '>=', $inicio)
                    // ->whereDate('created_at', '<=', $fin)
                    ->get()->sum('puntosD');
        
        foreach ($todoUsuarios as $user) {
            $lado1 = ( $lado1 + Wallet::where('iduser', $user['ID'])
                                // ->whereDate('created_at', '>=', $inicio)
                                // ->whereDate('created_at', '<=', $fin)
                                ->get()->sum('puntosI'));
            $lado2 = ( $lado2 + Wallet::where('iduser', $user['ID'])
                                // ->whereDate('created_at', '>=', $inicio)
                                // ->whereDate('created_at', '<=', $fin)
                                ->get()->sum('puntosD'));
        }
        $totalgrupa = ($lado1 + $lado2);
        if ($totalgrupa >= $montoRequisito) {
            $resul = true;
        }
        return $resul;
    }

    /**
     * Permite Obtener las compras de las red,  del usuario
     * 
     * @access private
     * @param int $iduser - id usuario
     * @return float
     */
    private function verificacionCompraGrupal($iduser)
    {
        $totalCompras = $this->getShopping($iduser);
        $comprastotalesuser = 0;
        foreach ($totalCompras as $compra) {
            //Se obtienen los datos de cada compra
            $datosCompra = $this->getShoppingDetails($compra->post_id);

            //Se verifica que la compra sea del mes que se está pagando
            $fechaCompra = new Carbon($datosCompra->post_date);
            $mesCompra = $fechaCompra->format('m');
            $fechaActual = Carbon::now();
            $mesActual = $fechaActual->format('m');
            if ($mesCompra == $mesActual && $datosCompra->post_status == 'wc-completed') {
                $comprastotalesuser = ($comprastotalesuser + (int) $this->getShoppingTotal($compra->post_id));
            }        
        }
        return $comprastotalesuser; 
    }

    /**
     * Permite Verificar las comisiones del usuario
     * 
     * @access private
     * @param int $iduser - id usuario, float $montoRequisito - el monto a superar o igualar
     * @return boolean
     */
    private function verificacionComisiones($iduser, $montoRequisito)
    {
        $resul = false;
        $montoObtenido = Commission::where('user_id', $iduser)->sum('total');
        if ($montoObtenido >= $montoRequisito) {
            $resul = true;
        }
        return $resul;
    }

    /**
     * Permite pagar los bonos por los puntos acumulado de los usuarios a lo largo del mes
     *
     * @param integer $iduser
     * @return void
     */
    public function verificarPuntos($iduser, $montoRequisito)
    {
        $resul = false;
        $inicio = Carbon::now()->startOfMonth();
        $fin = Carbon::now()->endOfMonth();
        $user = User::find($iduser);
        $directos = User::where('position_id', $iduser)->get();
        $puntosIzq = 0; $puntosDer = 0;
        if (!empty($directos)) {
            $izquierda = (!empty($directos[0])) ? $this->generarArregloUsuario($directos[0]['ID']) : null;
            $derecha = (!empty($directos[1])) ? $this->generarArregloUsuario($directos[1]['ID']) : null;
            if ($izquierda) {
                $puntosIzq = $directos[0]['puntos'];
                foreach ($izquierda as $izq) {
                    $puntosIzq = ($puntosIzq + $izq['puntos']);
                }
            }
            if ($derecha) {
                $puntosDer = $directos[1]['puntos'];
                foreach ($derecha as $der) {
                    $puntosDer = ($puntosDer + $der['puntos']);
                }
            }
        }
        $montoValidar = 0;
        if ($puntosIzq >= $puntosDer) {
            $montoValidar = $puntosDer;
        }else{
            $montoValidar = $puntosIzq;
        }
        if ($montoValidar >= $montoRequisito) {
            $resul = true;
        }
        return $resul;
    }

}
