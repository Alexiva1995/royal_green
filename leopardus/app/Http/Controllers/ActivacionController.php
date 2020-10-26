<?php

namespace App\Http\Controllers;
use App\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\SettingActivacion;
use App\SettingsComision;
use App\SettingsEstructura;
use App\Http\Controllers\ComisionesController;
use App\Settings;

use function GuzzleHttp\json_decode;

class ActivacionController extends Controller
{

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
                    ->orderBy('post_id', 'DESC')
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
     * Permite llevar la informacion del nivel de pago
     *
     * @param integer $idproduct
     * @return void
     */
    private function getNivelPayment($idproduct)
    {
        $settings = Settings::first();
        $valor = 0;
        $result = DB::table($settings->prefijo_wp.'posts as wp')
                    ->where([
                        ['wp.ID', '=', $idproduct],
                    ])->select('wp.post_password as nivel_pago')->first();

        if (!empty($result)) {
            $valor = $result->nivel_pago;
        }
        return $valor;
    }

    /**
     * Permite llevar la informacion del porcentaje a cobrar
     *
     * @param integer $idproduct
     * @return void
     */
    private function getPorcentaje($idproduct)
    {
        $settings = Settings::first();
        $valor = 0;
        $result = DB::table($settings->prefijo_wp.'posts as wp')
                    ->where([
                        ['wp.ID', '=', $idproduct],
                    ])->select('wp.to_ping as porcentaje')->first();

        if (!empty($result)) {
            $valor = $result->porcentaje;
        }
        return $valor;
    }

    /**
     * Permite llevar la informacion del detalle de pago
     *
     * @param integer $idproduct
     * @return void
     */
    private function getDetallPay($idproduct)
    {
        $settings = Settings::first();
        $valor = [
            'tipo_pago' => 'acr',
            'dias_activos' => 0
        ];
        $result = DB::table($settings->prefijo_wp.'posts as wp')
                    ->where([
                        ['wp.ID', '=', $idproduct],
                    ])->select(
                        'wp.post_content_filtered as tipo_pago',
                        'wp.post_parent as dias_activos'
                    )->first();

        if (!empty($result)) {
            $valor = [
                'tipo_pago' => $result->tipo_pago,
                'dias_activos' => $result->dias_activos
            ];
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
    
    // funcion para obtener el precio de los productos comprados
	public function getTotalProductos($id_item)
	{
        $valor = 0;
        $settings = Settings::first();
		$IdProducto = DB::table($settings->prefijo_wp.'woocommerce_order_itemmeta')
                            ->select('meta_value')
                            ->where('order_item_id', '=', $id_item)
                            ->where('meta_key', '=', '_line_total')
                            ->first();
                            
        if (!empty($IdProducto)) {
            $restante = $IdProducto->meta_value;
            $valor = $restante;
        }
		return $valor;
    }
    
    /**
     * Permite tener el nombre de los productos
     *
     * @param integer $idproduct
     * @return void
     */
    public function getNameProduct($idproduct)
    {
        $settings = Settings::first();
        $valor = 'Producto no encontrado';
        $result = DB::table($settings->prefijo_wp.'posts as wp')
                    ->where([
                        ['wp.ID', '=', $idproduct],
                    ])
                    ->select('wp.ID', 'wp.post_title')
                    ->first();
        if (!empty($result)) {
            $valor = $result->post_title;
        }
        return $valor;
    }
	  
    /**
     * Verifica que es estado de los usuarios 
     * 
     * @access public 
     * @param int $userid - id del usuarios a verificar
     * @return string
     */
    public function activarUsuarios($userid)
    {
        $user = User::find($userid);
        $fechaProxActivacion = $user->fecha_activacion;
        // if ($user->fecha_activacion == '') {
            $fechaProxActivacion = '';
            $fechaUltimaCompra = null;
            $settingAct = SettingActivacion::find(1);
            $settings = Settings::first();   
            if ($settingAct->tipoactivacion == 1) {
                $fechaUltimaCompra = $this->verificarActID($userid, $settingAct->requisitoactivacion);
            }elseif($settingAct->tipoactivacion == 2){
                $fechaUltimaCompra = $this->verificarActMonto($userid, $settingAct->requisitoactivacion);
            }
            
            if ($settingAct->tiporecompra != 0 && $user->activacion != 0) {
                if ($settingAct->tiporecompra == 1) {
                    $fechaUltimaCompra = $this->verificarActID($userid, $settingAct->requisitorecompra);
                }elseif($settingAct->tiporecompra == 2){
                    $fechaUltimaCompra = $this->verificarActMonto($userid, $settingAct->requisitorecompra);
                }
            }
            // dd($fechaUltimaCompra);
            // *** VERIFICAR QUE EL AFILIADO TENGA SU COMPRA MENSUAL CORRESPONDIENTE ***//
            if ($fechaUltimaCompra != null){
                $fechaUltimaCompra = new Carbon($fechaUltimaCompra);
                // $fechaProxActivacion = $fechaUltimaCompra->addMonth(1);
                    $fechaActual = Carbon::now();
                    DB::table($settings->prefijo_wp.'users')
                        ->where('ID', '=', $userid)
                        ->update(['status' => true,
                        'fecha_activacion' =>  $fechaUltimaCompra]);

                    $user = User::find($userid);
                    if ($user->activacion == 0) {
                        $user->activacion = 1;
                        $user->save();
                        $settinComision = SettingsComision::find(1);
                        $comisiones = new ComisionesController;
                        if ($settinComision->bonoactivacion != 0) {
                            $iduser = 1;
                            $concepto = 'User Activation Bonus: '.$user->display_name;
                            if ($settinComision->directos == 0) {
                                if ($this->obtenerEstructura() == 'arbol') {
                                    $iduser = $user->position_id;
                                } else {
                                    $iduser = $user->position_id;
                                }
                                $comisiones->guardarComision($iduser, 15, $settinComision->bonoactivacion, $user->user_email, 0, $concepto, 'bono');
                            } else {
                                if ($this->obtenerEstructura() == 'arbol') {
                                    $iduser = $user->referred_id;
                                } else {
                                    $iduser = $user->sponsor_id;
                                }
                                $comisiones->guardarComision($iduser, 15, $settinComision->bonoactivacion, $user->user_email, 0, $concepto, 'bono');
                            }
                        }
                    }
            }else{
                $fechaProxActivacion = 'No Purchases';

                DB::table($settings->prefijo_wp.'users')
                        ->where('ID', '=', $userid)
                        ->update(['status' => false ]);

            }
        // }
        return $fechaProxActivacion;
    }

    /**
     * Permite Verificar la activacion por el Id del producto
     * 
     * @access private
     * @param int $iduser - usuario a verificar, int $productoVerificar - producto a comparar
     * @return  string
     */
    private function verificarActID($iduser, $productoVerificar)
    {
        $compras = $this->getShopping($iduser);
        $fechaUltimaCompra = null;
        foreach ($compras as $compra ) {

            $datocompra = $this->getShoppingDetails($compra->post_id);
            if ($datocompra->post_status == 'wc-completed') {
                $productos = $this->getProductos($compra->post_id);
                foreach ($productos as $item ) {
                    if ($productoVerificar == $this->getIdProductos($item->order_item_id)) {
                        $fechaUltimaCompra = $datocompra->post_date;
                    }
                }
            }
        }
        return $fechaUltimaCompra;
    }

    /**
     * Permite Verificar la activacion por el Monto Minimo
     * 
     * @access private
     * @param int $iduser - usuario a verificar, float $montoVerificar
     * @return  string
     */
    private function verificarActMonto($iduser, $montoVerificar)
    {
        $compras = $this->getShopping($iduser);
        $fechaUltimaCompra = null;
        $user = User::find($iduser);
        // $comisiones = new ComisionesController;
        $rentabilidad = 0;
        if ($compras->isNotEmpty()) {
            foreach ($compras as $compra ) {
                $datocompra = $this->getShoppingDetails($compra->post_id);
                if ($datocompra->post_status == 'wc-completed') {
                    if ($this->getShoppingTotal($compra->post_id) >= $montoVerificar) {
                        $productos = $this->getProductos($compra->post_id);
                        $paquete = [];
                        foreach ($productos as $item ) {
                            $idprod = (int) $this->getIdProductos($item->order_item_id);
                            $DetallePago = $this->getDetallPay($idprod);
                            $paquete = [
                                'nombre' => $this->getNameProduct($idprod),
                                'ID' => $idprod,
                                'monto' => $this->getTotalProductos($item->order_item_id),
                                'nivel' => $this->getNivelPayment($idprod),
                                'porcentaje' => ($this->getPorcentaje($idprod) / 100),
                                'tipo_pago' => $DetallePago['tipo_pago'],
                                'dias_activos' => $DetallePago['dias_activos']
                            ];
                            // dd($paquete, $idprod, $item);
                            $rentabilidad = 0;
                            if ($DetallePago['tipo_pago'] == 'acr') {
                                $rentabilidad = ($this->getTotalProductos($item->order_item_id) * 2);
                            }
                            $icono_paquete = 'MASTER.png';
                            
                            if (!empty($paquete)) {
                                break;
                            }
                        }
                        
                        if (!empty($paquete)) {
                            if ($user->activacion == 0) {
                                $user->activacion = 1;
                            }
                            if (!empty($user->paquete)) {
                                $tmpPaquete = json_decode($user->paquete);
                                if($tmpPaquete->nivel < $paquete['nivel']){
                                    $user->paquete = json_encode($paquete);
                                    User::where('ID', $iduser)->update(['paquete' => json_encode($paquete)]);
                                }
                            }else{
                                $user->paquete = json_encode($paquete);
                                User::where('ID', $iduser)->update(['paquete' => json_encode($paquete)]);
                            }
                            
                            if (empty($user->rentabilidad)) {
                                $user->rentabilidad = $rentabilidad;
                            }elseif ($user->rentabilidad < $rentabilidad) {
                                $user->rentabilidad = $rentabilidad;
                            }
                            $user->icono_paquete = '/img/paquetes/'.$icono_paquete;
                            $user->save();
                        }
                        $fechaActal = Carbon::now();
                        $fechaCompra = new Carbon($datocompra->post_date);
                        if (!empty($paquete)) {
                            if ($paquete['tipo_pago'] == 'asr') {
                                if ($fechaActal > $fechaCompra->addDays($paquete['dias_activos'])) {
                                    $fechaUltimaCompra = null;
                                }else{
                                    $fechaUltimaCompra = $datocompra->post_date;
                                }
                            }else{
                                $fechaUltimaCompra = $datocompra->post_date;
                            }
                        } else {
                            $fechaUltimaCompra = $datocompra->post_date;
                        }
                        
                    }
                }else{
                    if ($user->status == 1) {
                        $fechaUltimaCompra = $user->fecha_activacion;
                    }
                }
            }
        }else{
            if ($user->status == 1) {
                $fechaUltimaCompra = $user->fecha_activacion;
            }
        }
        return $fechaUltimaCompra;
    }

    /**
     * Función que devuelve los patrocinados de un determinado usuario
     * 
     * @access private
     * @param int $id - id del usuario 
     * @return array
     */
    private function getSponsor($user_id){
        $tmp = User::select('ID', 'user_email', 'status', 'display_name', 'created_at', 'rol_id')->where('position_id', $user_id)->get()->toArray();
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
        $referidos = User::select('ID', 'user_email', 'status', 'display_name', 'created_at', 'rol_id')->where('position_id', $user_id)->get()->toArray();
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
                $allUser [] = [
                    'ID' => $user['ID'],
                    'email' => $user['user_email'],
                    'nombre' => $user['display_name'],
                    'status' => $user['status'],
                    'nivel' => $niveles,
                    'fecha' => $user['created_at'],
                    'rol' => $user['rol_id']
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

        if ($this->obtenerEstructura() == 'arbol') {
            $referidosDirectos = $this->getReferreds($iduser);
            $this->getReferredsAll($referidosDirectos, 1, $settingEstructura->cantnivel, [], 'arbol');
        } else {
            $referidosDirectos = $this->getSponsor($iduser);
            $this->getReferredsAll($referidosDirectos, 1, $settingEstructura->cantnivel, [], 'matriz');
        }

        return $GLOBALS['allUsers'];
    }

    public function scriptMax()
    {
        $max_cash = 0;
        $max_point = 0;
        $users = User::Where('paquete', '!=', '')->get();
        foreach ($users as $usuario) {
            switch ($usuario->paquete) {
                case 'MASTER':
                    $max_cash = 300;
                    $max_point = 25000;
                    break;
                case 'MASTER PLUS':
                    $max_cash = 1050;
                    $max_point = 58333;
                    break;
                case 'POWER':
                    $max_cash = 8750;
                    $max_point = 125000;
                    break;
                case 'POWER MAX':
                    $max_cash = 9000;
                    $max_point = 300000;
                    break;
                case 'MASTER INFINITY':
                    $max_cash = 24000;
                    $max_point = 1000000;
                    break;
            }
            $user = User::find($usuario->ID);
            $user->max_cash = $max_cash;
            $user->max_point = $max_point;
            $user->save();
        }
    }

}
