<?php

namespace App\Http\Controllers;

use App\Settings;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use stdClass;
use CoinbaseCommerce\ApiClient;
use CoinbaseCommerce\Resources\Charge;
use App\Http\Controllers\ComisionesController;
use App\Http\Controllers\TiendaController;
use Illuminate\Support\Facades\Auth;
use Hexters\CoinPayment\CoinPayment;



class IndexController extends Controller
{

    /**
     * Permite saber el estado del binario del usuario
     *
     * @param integer $id - id del usuario a revisar
     * @return boolean
     */
    public function statusBinary($id)
    {
        $result = false;
        $derecha = User::where([
            ['referred_id', '=', $id ],
            ['status', '=', 1],
            ['ladomatrix', '=', 'D']
        ])->get()->count('ID');
        $izquierda = User::where([
            ['referred_id', '=', $id ],
            ['status', '=', 1],
            ['ladomatrix', '=', 'I']
        ])->get()->count('ID');

        if ($derecha >= 1 && $izquierda >= 1) {
            $result = true;
        }
        return $result;
    }

    
    /**
     * Permite obtener la informacion para el arbol o matris
     *
     * @param integer $id - id del usuario a obtener sus hijos
     * @param string $type - tipo de estructura a general
     * @return void
     */
    public function getDataEstructura($id, $type)
    {
        $genealogyType = [
            'tree' => 'referred_id',
            'matriz' => 'position_id',
        ];
        
        $childres = $this->getData($id, 1, $genealogyType[$type]);
        $trees = $this->getChildren($childres, 2, $genealogyType[$type]);
        return $trees;
    }


    /**
     * Permite obtener a todos mis hijos y los hijos de mis hijos
     *
     * @param array $users - arreglo de usuarios
     * @param integer $nivel - el nivel en el que esta parado
     * @param string $typeTree - el tipo de arbol a usar
     * @return void
     */
    public function getChildren($users, $nivel, $typeTree)
    {
        if (!empty($users)) {
            foreach ($users as $user) {
                $user->children = $this->getData($user->ID, $nivel, $typeTree);
                $this->getChildren($user->children, ($nivel+1), $typeTree);
            }
            return $users;
        }else{
            return $users;
        }
    }

    /**
     * Se trare la informacion de los hijos 
     *
     * @param integer $id - id a buscar hijos
     * @param integer $nivel - nivel en que los hijos se encuentra
     * @param string $typeTree - tipo de arbol a usar
     * @return void
     */
    private function getData($id, $nivel, $typeTree) : object
    {
        $comisioncontroller = new ComisionesController;
        $resul = User::where($typeTree, '=', $id)->orderBy('ladomatrix', 'desc')->get();
        foreach ($resul as $user) {
            $patrocinado = User::find($user->referred_id);
            $paquete = json_decode($user->paquete);
            $user->avatar = asset('avatar/'.$user->avatar);
            if (!empty($paquete)) {
                $user->avatar = asset('assets/paquetes/iconos/'.$paquete->precio.'.png');
            }
            $user->nivel = $nivel;
            $user->ladomatriz = $user->ladomatrix;
            $user->patrocinador = $patrocinado->display_name;
            $comisioncontroller->checkExictPoint($user->ID);
            $user->puntos = json_decode($user->puntos);
        }
        return $resul;
    }

    /**
     * Permite tener la informacion de los hijos como un listado
     *
     * @param integer $parent - id del padre
     * @param array $array_tree_user - arreglo con todos los usuarios
     * @param integer $nivel - nivel
     * @param string $typeTree - tipo de usuario
     * @param boolean $allNetwork - si solo se va a traer la informacion de los directos o todos mis hijos
     * @return 
     */
    public function getChidrens2($parent, $array_tree_user, $nivel, $typeTree, $allNetwork) : array
    {   
        if (!is_array($array_tree_user))
        $array_tree_user = [];
    
        $data = $this->getData($parent, $nivel, $typeTree);
        if (count($data) > 0) {
            if ($allNetwork == 1) {
                foreach($data as $user){
                    if ($user->nivel == 1) {
                        $array_tree_user [] = $user;
                    }
                }
            }else{
                foreach($data as $user){
                    $array_tree_user [] = $user;
                    $array_tree_user = $this->getChidrens2($user->ID, $array_tree_user, ($nivel+1), $typeTree, $allNetwork);
                }
            }
        }
        return $array_tree_user;
    }


    /**
     * Permite ordenar el arreglo primario con las claves de los arreglos segundarios
     * 
     * @access public
     * @param array $arreglo - el arreglo que se va a ordenar, string $clave - con que se hara la comparecion de ordenamiento,
     * stridn $forma - es si es cadena o numero
     * @return array
     */
    public function ordenarArreglosMultiDimensiones($arreglo, $clave, $forma)
    {
        usort($arreglo, $this->build_sorter($clave, $forma));
        return $arreglo;
    }

    /**
     * compara las clave del arreglo
     * 
     * @access private
     * @param string $clave - llave o clave del arreglo segundario a comparar
     * @return string
     */
    private function build_sorter($clave, $forma) {
        return function ($a, $b) use ($clave, $forma) {
            if ($forma == 'cadena') {
                return strnatcmp($a[$clave], $b[$clave]);
            } else {
                return $b[$clave] - $a[$clave] ;
            }
            
        };
    }

    /**
     * Permite obtener la informacion completa de las compras
     *
     * @param integer $iduser
     * @return void
     */
    public function getInforShopping($iduser) : array
    {
        $arreCompras = [];
        $compras = $this->getShopping($iduser);
        if (!empty($compras)) {
            foreach ($compras as $compra) {
                $detallesCompra = $this->getShoppingDetails($compra->post_id);
                if ($detallesCompra->null) {
                    $arregProducto = $this->getProductos($compra->post_id);
                    if ($arregProducto->null) {
                        $productos = [];
                        foreach ($arregProducto as $product) {
                            $idProducto = $this->getIdProductos($product->order_item_id);
                            $detalleProduct = $this->getProductDetails($idProducto);
                            if ($detalleProduct->null) {
                                $precio = $this->getTotalProductos($product->order_item_id);
                                $productos [] = [
                                    'idproducto' => $idProducto,
                                    'precio' => $precio,
                                    'nombre' => $detalleProduct->post_title,
                                    'img' => $detalleProduct->post_excerpt,
                                    'img2' => asset('assets/paquetes/rg'.$precio.'.png'),
                                    'porc_binario' => $detalleProduct->bono_binario
                                ];
                            }
                        }
                        $arreCompras [] = [
                            'idusuario' => $iduser,
                            'idcompra' => $compra->post_id,
                            'fecha' => $detallesCompra->post_date,
                            'productos' => $productos,
                            'total' => $this->getShoppingTotal($compra->post_id),
                            'tipo_activacion' => $detallesCompra->to_ping
                        ];
                    }
                }
            }
        }
        return $arreCompras;
    }

    /**
     * Permite obtener las compras que hizo un usuario
     *
     * @param integer $user_id
     * @return void
     */
    public function getShopping($user_id) : object
    {
        $settings = Settings::first();
        $comprasID = DB::table($settings->prefijo_wp.'postmeta')
                    ->select('post_id')
                    ->where('meta_key', '=', '_customer_user')
                    ->where('meta_value', '=', $user_id)
                    ->get();
        return $comprasID;
    }

    /**
     * Permite obtener el id del usuario que hizo la compra
     *
     * @param integer $idpost
     * @return void
     */
    public function getIdUser($idpost) : int
    {
        $settings = Settings::first();
        $comprasID = DB::table($settings->prefijo_wp.'postmeta')
                    ->select('meta_value')
                    ->where('meta_key', '=', '_customer_user')
                    ->where('post_id', '=', $idpost)
                    ->first();
        $valor = ($comprasID != null) ? $comprasID->meta_value : 0;
        return $valor;
    }

    /**
     * Permite obtener informacion del estado y fecha de la compra
     *
     * @param integer $shop_id
     * @return void
     */
    public function getShoppingDetails($shop_id) : object
    {
        $settings = Settings::first();
		$datosCompra = DB::table($settings->prefijo_wp.'posts')
                        ->select('post_date', 'to_ping')
                        ->where('ID', '=', $shop_id)
                        ->where('post_status', '=', 'wc-completed')
                        ->first();

        if (empty($datosCompra)) {
            $datosCompra = new stdClass();
            $datosCompra->null = false;
        }else{
            $datosCompra->null = true;
        }
        return $datosCompra;
    }

    /**
     * Permite obtener todos los productos de la compras
     *
     * @param integer $shop_id
     * @return object
     */
	public function getProductos($shop_id): object
	{
        $settings = Settings::first();
		$totalProductos = DB::table($settings->prefijo_wp.'woocommerce_order_items')
													->select('order_item_id')
													->where('order_id', '=', $shop_id)
                                                    ->get();

        if (empty($totalProductos)) {
            $totalProductos = new stdClass();
            $totalProductos->null = false;
        }else{
            $totalProductos->null = true;
        }
		return $totalProductos;
	}
    
    /**
     * Permite obtener el id de los productos
     *
     * @param integer $id_item
     * @return void
     */
	public function getIdProductos($id_item): int
	{
        $settings = Settings::first();
        $valor = 0;
		$IdProducto = DB::table($settings->prefijo_wp.'woocommerce_order_itemmeta')
													->select('meta_value')
													->where('order_item_id', '=', $id_item)
													->where('meta_key', '=', '_product_id')
                                                    ->first();
        if (!empty($IdProducto)) {
            if (!empty($IdProducto->meta_value)) {
                $valor = $IdProducto->meta_value;
            }
        }
		return $valor;
    }
    
    /**
     * Permite obtener informacion de los productos
     *
     * @param integer $shop_id
     * @return void
     */
    public function getProductDetails($shop_id) : object
    {
        $settings = Settings::first();
		$datosCompra = DB::table($settings->prefijo_wp.'posts')
                        ->select('post_excerpt', 'post_title', 'post_password as bono_binario')
                        ->where('ID', '=', $shop_id)
                        ->first();

        if (empty($datosCompra)) {
            $datosCompra = new stdClass();
            $datosCompra->null = false;
        }else{
            $datosCompra->null = true;
        }
        return $datosCompra;
    }
    
    /**
     * Permite obtener el precio de los productos comprado
     *
     * @param integer $id_item
     * @return void
     */
	public function getTotalProductos($id_item) : float
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
     * Permite obtener el monto total de la compra realizada
     *
     * @param integer $shop_id
     * @return void
     */
    public function getShoppingTotal($shop_id): float
    {
        $settings = Settings::first();
		$totalCompra = DB::table($settings->prefijo_wp.'postmeta')
				        ->select('meta_value')
				        ->where('post_id', '=', $shop_id)
				        ->where('meta_key', '=', '_order_total')
				        ->first();
		return $totalCompra->meta_value;
    }
    
    /**
     * Se trare la informacion de los hijos 
     *
     * @param integer $id - id a buscar hijos
     * @param integer $nivel - nivel en que los hijos se encuentra
     * @param string $typeTree - tipo de arbol a usar
     * @return void
     */
    private function getDataSponsor($id, $nivel, $typeTree) : object
    {
        $resul = User::where($typeTree, '=', $id)->get();
        foreach ($resul as $user) {
            $user->avatar = asset('avatar/'.$user->avatar);
            $user->nivel = $nivel;
            $user->ladomatriz = $user->ladomatrix;
        }
        return $resul;
    }

    /**
     * Permite obtener a todos mis patrocinadores
     *
     * @param integer $child - id del hijo
     * @param array $array_tree_user - arreglo de patrocinadores
     * @param integer $nivel - nivel a buscar
     * @param string $typeTree - llave a buscar
     * @param string $keySponsor - llave para buscar el sponsor, position o referido
     * @return array
     */
    public function getSponsor($child, $array_tree_user, $nivel, $typeTree, $keySponsor): array
    {
        if (!is_array($array_tree_user))
        $array_tree_user = [];
    
        $data = $this->getDataSponsor($child, $nivel, $typeTree);
        if (count($data) > 0) {
            foreach($data as $user){
                $array_tree_user [] = $user;
                $array_tree_user = $this->getSponsor($user->$keySponsor, $array_tree_user, ($nivel+1), $typeTree, $keySponsor);
            }
        }
        return $array_tree_user;
    }

    /**
     * Obtener todas las compras para la rentabilidad
     *
     * @return array
     */
    public function getAllComprasRentabilidad(): array
    {
        $settings = Settings::first();
        $compras = DB::table($settings->prefijo_wp.'posts')
                    ->select('*')
                    ->where([
                        ['post_type', '=', 'shop_order'],
                        ['post_status', '=', 'wc-completed'],
                    ])
                    ->get();
        $arreCompras = [];
        foreach ($compras as $compra) {
            $arregProducto = $this->getProductos($compra->ID);
            $iduser = $this->getIdUser($compra->ID);
            if ($arregProducto->null && $iduser > 0) {
                $productos = [];
                foreach ($arregProducto as $product) {
                    $idProducto = $this->getIdProductos($product->order_item_id);
                    $detalleProduct = $this->getProductDetails($idProducto);
                    if ($detalleProduct->null) {
                        $precio = $this->getTotalProductos($product->order_item_id);
                        $productos [] = [
                            'idproducto' => $idProducto,
                            'precio' => $precio,
                            'nombre' => $detalleProduct->post_title,
                            'img' => $detalleProduct->post_excerpt,
                            'img2' => asset('assets/paquetes/rg'.$precio.'.png'),
                            'porc_binario' => $detalleProduct->bono_binario
                        ];
                    }
                }
                $arreCompras [] = [
                    'idusuario' => $iduser,
                    'idcompra' => $compra->ID,
                    'fecha' => $compra->post_date,
                    'productos' => $productos,
                    'total' => $this->getShoppingTotal($compra->ID),
                    'tipo_activacion' => $compra->to_ping
                ];
            }
        }
        return $arreCompras;
    }

    /**
     * Permite Obtener las ultimas compras realizadas en los ultimos 30 dias, para pagar los bonos correspondientes
     *
     * @return array
     */
    public function getAllCompras(): array
    {
        $fecha = Carbon::now();
        $settings = Settings::first();
        $compras = DB::table($settings->prefijo_wp.'posts')
                    ->select('*')
                    ->whereDate('post_date', '>', $fecha->subDay(30))
                    ->where([
                        ['post_type', '=', 'shop_order'],
                        ['post_status', '=', 'wc-completed'],
                        ['to_ping', '=', 'Coinbase']
                    ])->orWhere([
                        ['post_type', '=', 'shop_order'],
                        ['post_status', '=', 'wc-completed'],
                        ['ID', '=', 6571]
                    ])
                    ->orderBy('ID')
                    ->get();
        $arreCompras = [];
        foreach ($compras as $compra) {
            $arregProducto = $this->getProductos($compra->ID);
            $iduser = $this->getIdUser($compra->ID);
            if ($arregProducto->null && $iduser > 0) {
                $productos = [];
                $membresia = false;
                foreach ($arregProducto as $product) {
                    $idProducto = $this->getIdProductos($product->order_item_id);
                    $detalleProduct = $this->getProductDetails($idProducto);
                    if ($detalleProduct->null) {
                        $precio = $this->getTotalProductos($product->order_item_id);
                        $productos [] = [
                            'idproducto' => $idProducto,
                            'precio' => $precio,
                            'nombre' => $detalleProduct->post_title,
                            'img' => $detalleProduct->post_excerpt,
                            'img2' => asset('assets/paquetes/rg'.$precio.'.png'),
                            'porc_binario' => $detalleProduct->bono_binario
                        ];
                    }
                }
                $arreCompras [] = [
                    'idusuario' => $iduser,
                    'idcompra' => $compra->ID,
                    'fecha' => $compra->post_date,
                    'productos' => $productos,
                    'total' => $this->getShoppingTotal($compra->ID),
                ];
            }
        }
        return $arreCompras;
    }

    /**
     * Permite verificar las compras procesadas
     * y pagar los bonos y activar los usuarios
     *
     * @return void
     */
    public function ordenesSistema()
    {
        $tienda = new TiendaController;
        $fecha = Carbon::now();
        $compras = DB::table('coinpayment_transactions')->where('status', '=', 0)->whereDate('created_at', '>', $fecha->subDays(4))->get();
        foreach ($compras as $compra) {
            $result = CoinPayment::getstatusbytxnid($compra->txn_id);
            if (is_array($result) && $result['status'] == 100) {
                $tienda->accionSolicitud($compra->order_id, 'wc-completed', 'Coinbase');
                $tienda->actualizarBD($compra->order_id, 'wc-completed', 'Coinbase');
            }
        }

        $compras2 = DB::table('coinpayment_transactions')->where('status', '=', 100)->whereDate('created_at', '>', $fecha->subDays(4))->get();
        foreach ($compras2 as $compra) {
            $tienda->accionSolicitud($compra->order_id, 'wc-completed', 'Coinbase');
            $tienda->actualizarBD($compra->order_id, 'wc-completed', 'Coinbase');
        }
    }

    /**
     * permite activar paquete activas sino se activan de la otra forma
     *
     * @return void
     */
    public function activarPaquetes()
    {
        $tienda = new TiendaController;
        $solicitudes = $tienda->ArregloCompra3();
        foreach ($solicitudes as $solicitud) {
            if ($solicitud['estado'] == 'Completado') {
                $comisioncontroller = new ComisionesController;
                $comisioncontroller->registePackageToRentabilizar($solicitud['iduser']);   
            }
        }
    }

    /**
     * Permite obtener la cantidad de usuarios por mes
     *
     * @return string
     */
    public function chartUsuarios() : string
    {
        $iduser = Auth::user()->ID;
        $allUser = $this->getChidrens2($iduser, [], 1, 'referred_id', 1);
        $Ano_Actual = Carbon::now()->format('Y');
        $totalMeses = [];
        for ($i=1; $i < 13; $i++) {
            $totalmes = 0;
            foreach ($allUser as $user) {
                $fecha_register = new Carbon($user->created_at);
                if ($Ano_Actual == $fecha_register->format('Y')) {
                    if ($fecha_register->format('m') == $i) {
                        $totalmes++;
                    }

                }
            }
            $totalMeses [] = $totalmes;
        }
        return json_encode($totalMeses);
    }

    /**
     * Permite recibir el bono de bienvenidad
     *
     * @param integer $iduser
     * @return array
     */
    public function bonoBienvenida($iduser): array
    {

        $user = User::find($iduser);

        $fechaActual = Carbon::now();
        $fechaActivacion = new Carbon($user->created_at);

        $arrayBono = [
            ['requisito' => 5000, 'bono' => 500],
            ['requisito' => 10000, 'bono' => 1000],
            ['requisito' => 20000, 'bono' => 2000],
            ['requisito' => 30000, 'bono' => 3000],
            ['requisito' => 40000, 'bono' => 4000],
            ['requisito' => 50000, 'bono' => 5000],
        ];

        $directos = $this->getChidrens2($iduser, [], 1, 'referred_id', 1);

        $totalCompra = 0;
        foreach ($directos as $directo) {
            $compras = $this->getInforShopping($directo->ID);
            foreach ($compras as $compra) {
                if ($compra['tipo_activacion'] == 'Coinbase') {
                    $fechaCompra = new Carbon($compra['fecha']);
                    if ($fechaCompra <= $fechaActivacion->addDays(30)) {
                        $totalCompra = ($totalCompra + $compra['total']);
                    }
                }
            }
        }

        $requisito = 0;
        $bono = 0;
        for ($i=0; $i < count($arrayBono); $i++) { 
            if ($arrayBono[$i]['requisito'] <= $totalCompra) {
                $bono = $arrayBono[$i]['bono'];
                if ($i < 5) {
                    $requisito = $arrayBono[$i+1]['requisito'];
                }
                if ($i == 5) {
                    $requisito = $arrayBono[$i]['requisito'];
                }
            }
            if ($i == 0) {
                if ($arrayBono[$i]['requisito'] > $totalCompra) {
                    $requisito = $arrayBono[$i]['requisito'];
                }
            }
        }

        $progreso = (($totalCompra / $requisito) * 100);

        $arrayBienvenido = [
            'bono' => $bono,
            'requisito' => $requisito,
            'progreso' => $progreso
        ];

        return $arrayBienvenido;
    }

}
