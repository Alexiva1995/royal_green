<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;

use Illuminate\Support\Facades\View;

use App\User; 

use App\Settings; use App\Monedas; 
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\ActivacionController;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;
use CoinbaseCommerce\ApiClient;
use CoinbaseCommerce\Resources\Charge;
use App\Jobs\coinPaymentCallbackProccedJob;
use CoinPayment;


class TiendaController extends Controller

{

    /**

     * Mostramos el home de la aplicación.

     *

     * @return view()

     */



    //Historial de Comisiones para el usuario

    public function index(){
        // phpinfo();
        // dd('pa');
        $apiKey = env('COINBASE_API_KEY');
        $apiClientObj = ApiClient::init($apiKey);
        $apiClientObj->setTimeout(6);
        view()->share('title', 'Tienda');
        $productos = $this->getProductoWP();
        $moneda = Monedas::where('principal', 1)->get()->first();
        return view('tienda.index')->with(compact('productos', 'moneda'));
    }

    /**
     * Permite mostrar lo que hizo el cliente 
     *
     * @param string $estado
     * @return void
     */
    public function estadoTransacion($estado)
    {
        if ($estado == 'pendiente') {
            $user = Auth::user();
                Mail::send('emails.compra', [], function($msj) use ($user){
                    $msj->subject('Compra Finalizada');
                    $msj->to($user->user_email);
                });
            Session::flash('tipo', 'success');
            return redirect()->route('tienda-index')->with('msj', 'Orden Procesada');
        } else {
            Session::flash('tipo', 'danger');
            return redirect()->route('tienda-index')->with('msj', 'Orden Cancelada por el usuario');
        }
    }

    /**
     * Obtiene los Productos de la tienda de wordpress
     */
    public function getProductoWP()
    {   
        $settings = Settings::first();
        $result = DB::table($settings->prefijo_wp.'posts as wp')
                    ->join($settings->prefijo_wp.'postmeta as wpm', 'wp.ID', '=', 'wpm.post_id' )
                    ->where([
                        ['wpm.meta_key', '=', '_price'],
                        ['wp.post_type', '=', 'product'],
                        ['wp.pinged', '=', 'Visible']
                    ])
                    ->select('wp.ID', 'wp.post_title', 'wp.post_content', 'wp.guid', 'wpm.meta_value', 'wp.post_excerpt as imagen')
                    ->get();
        $cont = 0;
        foreach ($result as $element) {
                // $restante = ($result[$cont]->meta_value * 0.10);
                // $valor = ($result[$cont]->meta_value + $restante);
                $result[$cont]->meta_value = $result[$cont]->meta_value;
                $result[$cont]->link = $this->linkCoinPayMent($result[$cont]);
            $cont++;
        }  
        return $result;
    }

    /**
     * Permite crear el producto link para pagar por coinpayment
     *
     * @param object $producto
     * @return void
     */
    public function linkCoinPayMent(object $producto)
    {
        // $restante = ($producto->meta_value * 0.10);
        // $valor = ($producto->meta_value + $restante);
        // $coinpayment['amountTotal'] = (int) $producto->meta_value; // USD
        // $coinpayment['note'] = $producto->post_title;
        // $coinpayment['idproducto'] = $producto->ID;
        // $coinpayment['buyer_email'] = Auth::user()->user_email;
        // $coinpayment['redirect_url'] = route('coinpayment.ipn.received');
        // $coinpayment['items'][0] = [
        //     'descriptionItem' => $producto->post_title,
        //     'priceItem' => (int) $producto->meta_value, // USD
        //     'qtyItem' => 1,
        //     'subtotalItem' => (int) $producto->meta_value // USD
        // ];

        // $link_transaction1 = CoinPayment::url_payload($coinpayment);
        // return $link_transaction1;
        // $descricion = (empty($producto->post_content)) ? 'N/A' : $producto->post_content;
        $chargerData = [
            'description' => 'No Aplica',
            'metadata' => $producto,
            'pricing_type' => 'fixed_price',
            'redirect_url' => route('tienda.estado', ['pendiente']),
            'cancel_url' => route('tienda.estado', ['cancelada']),
            'local_price' => [
                'amount' => $producto->meta_value,
                'currency' => 'USD'
            ],
            'name' => 'Producto '.$producto->post_title,
            'payments' => [],
            ];
        
        $chargerObj = Charge::create($chargerData);
        return $chargerObj;
    }

    public function saveCupon(Request $datos)
    {
        $validate = $datos->validate([
            'precio' => 'required',
            'name' => 'required',
        ]);
        if ($validate) {
            if (Auth::user()->wallet_amount < $datos->precio) {
                return redirect()->back()->with('msj2', 'You do not have enough balance to make this purchase.');
            }
            $iduser = Auth::user()->ID;
            $user = User::find($iduser);
            $admin = User::find(1);
            $user->wallet_amount = ($user->wallet_amount - floatval($datos->precio));
            $admin->wallet_amount = ($admin->wallet_amount + floatval($datos->precio));
            $user->save();
            $admin->save();
            $cadena = $iduser.$datos->name.$datos->idproducto.Carbon::now();
            $cupon = md5($cadena);
            $arregloCupon = [
                'idpaquete' => $datos->idproducto,
                'paquete' => $datos->name,
                'precio' => $datos->precio,
                'usuario_regala' => $iduser,
                'tipo' => 'Cupon',
                'cupon' => $cupon
            ];
            $datos = [
                'iduser' => $user->ID,
                'usuario' => $user->display_name,
                'descripcion' => 'Package gift '.$datos->name,
                'descuento' => 0,
                'puntos' => 0,
                'puntosI' => 0,
                'puntosD' => 0,
                'debito' => 0,
                'tantechcoin' => 0,
                'credito' => floatval($datos->precio),
                'balance' => $user->wallet_amount,
                'tipotransacion' => 1,
            ];
            $wallet = new WalletController;
		    $wallet->saveWallet($datos);
            DB::table('cupones')->insert($arregloCupon);
            Mail::send('emails.cuponEmail',  ['cupon' => $cupon], function($msj) use ($user){
				$msj->subject('New Generated Coupon');
				$msj->to($user->user_email);
            });
            return redirect('tienda')->with('msj', 'Successfully created coupon, check your email');
        }
    }

    public function validacionCupon(Request $datos)
    {
        $cupon = DB::table('cupones')->where([
            ['cupon', '=', $datos->cupon],
            ['status', '=', 0]
        ])->first();
        if (!empty($cupon)) {
            $data = [
                'msj' => '',
                'idpaquete' => $cupon->idpaquete,
                'paquete' => $cupon->paquete,
                'precio' => $cupon->precio,
                'tipo' => $cupon->tipo,
                'cupon' => $cupon->cupon
            ];
            return json_encode($data);
        }else{
            $data = [
                'msj' => 'Coupon not valid or already used'
            ];
            return json_encode($data);
        }
    }

    /**
     * Permite Guardar la informacion de la entrada en wp
     *
     * @access public
     * @param request $datos - informacion de la compra
     * @return view
     */
    public function saveOrdenPosts(Request $datos)
    {
        $validate = $datos->validate([
            'precio' => 'required',
            'name' => 'required',
        ]); 
        $settings = Settings::first();
        if ($validate) {
            $verificarCode = DB::table($settings->prefijo_wp.'posts')->where('code_coinbase', $datos->code_coinbase)->first();
            if (!empty($verificarCode)) {
                $ruta = 'https://commerce.coinbase.com/charges/'.$datos->code_coinbase;
                return redirect($ruta);
            }else{
                $fecha = new Carbon();
                $title = 'Orden&ndash;'.$fecha->now()->toFormattedDateString().' @ '.$fecha->now()->format('h:i A');
                $tpmname = str_replace(' ', '-', $fecha->now()->toFormattedDateString());
                $tpmname = str_replace(',', '', $tpmname);
                $tpmname2 = str_replace(' ', '-', $fecha->now()->format('hi a'));
                $name = 'perdido-'.$tpmname.'-'.$tpmname2;
                $id = DB::table($settings->prefijo_wp.'posts')->insertGetId([
                    'post_author' => 1,
                    'post_date' => $fecha->now(),
                    'post_date_gmt' => $fecha->now(),
                    'post_content' => ' ',
                    'post_title' => $title,
                    'post_excerpt' => ' ',
                    'post_status' => 'wc-on-hold',
                    'comment_status' => 'open',
                    'ping_status' => 'closed',
                    'post_password' => 'order_'.base64_encode($fecha->now()),
                    'post_name' => $name,
                    'to_ping' => ' ',
                    'pinged' => ' ',
                    'post_modified' => $fecha->now(),
                    'post_modified_gmt' => $fecha->now(),
                    'post_content_filtered' => ' ',
                    'post_parent' => 0,
                    'menu_order' => 0,
                    'post_type' => 'shop_order',
                    'post_mime_type' => ' ',
                    'comment_count' => 1,
                    'id_coinbase' => $datos->id_coinbase,
                    'code_coinbase' => $datos->code_coinbase,
                ]);
                // if ($datos->tipo == 'Coinpayment') {
                //     DB::table('cointpayment_log_trxes')->where('payment_id', $datos->idpayment)->update(['idcomprawp' => $id]);
                // }
                // if ($datos->tipo == 'Cupon') {
                //     $cupon = DB::table('cupones')->where([
                //         ['cupon', '=', $datos->cupon],
                //         ['status', '=', 0]
                //     ])->update(['status' => 1, 'usuario_recibe' => Auth::user()->ID]);
                // }
                $data = [
                    '_order_key' => 'wc_order_'.base64_encode($fecha->now()),
                    'ip' => $datos->ip(),
                    'total' => $datos->precio.'.00',
                    'idproducto' => $datos->idproducto
                ];
                if ($id) {
                    $linkProducto = str_replace('mioficina', '?post_type=shop_order&#038;p=', $datos->root());
                    DB::table($settings->prefijo_wp.'posts')->where('ID', $id)->update([
                        'guid' => $linkProducto.$id
                    ]);
                    $this->saveOrdenPostmeta($id, $data, $datos->tipo);
                    $this->saveOrderItems($id, $datos->name, $data);
                    // if ($datos->tipo != 'Coinpayment') {
                    //     $this->accionSolicitud($id, 'wc-completed');
                    // }
                }
                if (Auth::user()->ID >= 315 || Auth::user()->ID <= 318) {
                    $this->accionSolicitud($id, 'wc-completed');
                    return redirect()->route('tienda-index')->with('msj', 'Compra Realizada con exito');
                }elseif (Auth::user()->ID == 2) {
                    $this->accionSolicitud($id, 'wc-completed');
                    return redirect()->route('tienda-index')->with('msj', 'Compra Realizada con exito');
                }else{
                    $ruta = 'https://commerce.coinbase.com/charges/'.$datos->code_coinbase;
                    return redirect($ruta);
                }
                
                // if ($datos->tipo != 'Coinpayment') {
                //     return redirect('tienda')->with('msj', 'Purchase '.$id.' Processed ');
                // }
            }
        }
    }



    /**
     * Guarda la informacion necesaria en esta tabla con respecto a la compra
     * 
     * @access public 
     * @param int $post_id - id de la compra, string $name - nombre del Producto, array $data - informacion compra
     */
    public function saveOrderItems($post_id, $name, $data)
    {
        $settings = Settings::first();
        $id = DB::table($settings->prefijo_wp.'woocommerce_order_items')->insertGetId([
            'order_item_name' => $name,
            'order_item_type' => 'line_item',
            'order_id' => $post_id,
        ]);
        $this->saveOrderItemeta($id, $data);
    }
    /**
     * Guarda la informacion necesaria en esta tabla con respecto a la compra
     * 
     * @access public
     * @param int $post_id - id de la compra, array $data - informacion de la compra
     */
    public function saveOrderItemeta($post_id, $data)
    {
        $settings = Settings::first();
        DB::table($settings->prefijo_wp.'woocommerce_order_itemmeta')->insert([
            ['order_item_id' => $post_id, 'meta_key' => '_product_id', 'meta_value' => $data['idproducto']],
            ['order_item_id' => $post_id, 'meta_key' => '_variation_id', 'meta_value' => 0],
            ['order_item_id' => $post_id, 'meta_key' => '_qty', 'meta_value' => 1],
            ['order_item_id' => $post_id, 'meta_key' => '_tax_class', 'meta_value' => ''],
            ['order_item_id' => $post_id, 'meta_key' => '_line_subtotal', 'meta_value' => $data['total']],
            ['order_item_id' => $post_id, 'meta_key' => '_line_subtotal_tax', 'meta_value' => 0],
            ['order_item_id' => $post_id, 'meta_key' => '_line_total', 'meta_value' => $data['total']],
            ['order_item_id' => $post_id, 'meta_key' => '_line_tax', 'meta_value' => 0],
            ['order_item_id' => $post_id, 'meta_key' => '_line_tax_data', 'meta_value' => 'a:2:{s:5:"total";a:0:{}s:8:"subtotal";a:0:{}}'],
        ]);
    }

    /**
     * Guarda la informacion necesaria en esta tabla con respecto a la compra
     * 
     * @access public
     * @param int $post_id - id de la compra, array $data - informacion de la compra
     */
    public function saveOrdenPostmeta($post_id, $datos, $tipo)
    {
        $settings = Settings::first();
        $user = User::find(Auth::user()->ID);
        $infofull = $user->names.' '.$user->last_names.' '.$user->address.' '.$user->departamento.' '.$user->country.' '.$user->user_email.' '.$user->phone;
        DB::table($settings->prefijo_wp.'postmeta')
            ->insert([
                ['post_id' => $post_id, 'meta_key' => '_orden_key', 'meta_value' => $datos['_order_key']],
                ['post_id' => $post_id, 'meta_key' => '_customer_user', 'meta_value' => $user->ID],
                ['post_id' => $post_id, 'meta_key' => '_payment_method', 'meta_value' => 'bacs'],
                ['post_id' => $post_id, 'meta_key' => '_payment_method_title', 'meta_value' => $tipo],
                ['post_id' => $post_id, 'meta_key' => '_transaction_id', 'meta_value' => ' '],
                ['post_id' => $post_id, 'meta_key' => '_customer_ip_address', 'meta_value' => $datos['ip']],
                ['post_id' => $post_id, 'meta_key' => '_customer_user_agent', 'meta_value' => $_SERVER['HTTP_USER_AGENT']],
                ['post_id' => $post_id, 'meta_key' => '_created_via', 'meta_value' => 'checkout'],
                ['post_id' => $post_id, 'meta_key' => '_date_completed', 'meta_value' => ''],
                ['post_id' => $post_id, 'meta_key' => '_completed_date', 'meta_value' => ''],
                ['post_id' => $post_id, 'meta_key' => '_date_paid', 'meta_value' => ''],
                ['post_id' => $post_id, 'meta_key' => '_paid_date', 'meta_value' => ''],
                ['post_id' => $post_id, 'meta_key' => '_cart_hash', 'meta_value' => md5(Carbon::now())],
                ['post_id' => $post_id, 'meta_key' => '_billing_first_name', 'meta_value' => $user->names],
                ['post_id' => $post_id, 'meta_key' => '_billing_last_name', 'meta_value' => $user->last_names],
                ['post_id' => $post_id, 'meta_key' => '_billing_company', 'meta_value' => ''],
                ['post_id' => $post_id, 'meta_key' => '_billing_address_1', 'meta_value' => $user->address],
                ['post_id' => $post_id, 'meta_key' => '_billing_address_2', 'meta_value' => ''],
                ['post_id' => $post_id, 'meta_key' => '_billing_city', 'meta_value' => $user->departamento],
                ['post_id' => $post_id, 'meta_key' => '_billing_state', 'meta_value' => $user->departamento],
                ['post_id' => $post_id, 'meta_key' => '_billing_postcode', 'meta_value' => ''],
                ['post_id' => $post_id, 'meta_key' => '_billing_country', 'meta_value' => $user->country],
                ['post_id' => $post_id, 'meta_key' => '_billing_email', 'meta_value' => $user->user_email],
                ['post_id' => $post_id, 'meta_key' => '_billing_phone', 'meta_value' => $user->phone],
                ['post_id' => $post_id, 'meta_key' => '_shipping_first_name', 'meta_value' => ''],
                ['post_id' => $post_id, 'meta_key' => '_shipping_last_name', 'meta_value' => ''],
                ['post_id' => $post_id, 'meta_key' => '_shipping_company', 'meta_value' => ''],
                ['post_id' => $post_id, 'meta_key' => '_shipping_address_1', 'meta_value' => ''],
                ['post_id' => $post_id, 'meta_key' => '_shipping_address_2', 'meta_value' => ''],
                ['post_id' => $post_id, 'meta_key' => '_shipping_city', 'meta_value' => ''],
                ['post_id' => $post_id, 'meta_key' => '_shipping_state', 'meta_value' => ''],
                ['post_id' => $post_id, 'meta_key' => '_shipping_postcode', 'meta_value' => ''],
                ['post_id' => $post_id, 'meta_key' => '_shipping_country', 'meta_value' => ''],
                ['post_id' => $post_id, 'meta_key' => '_order_currency', 'meta_value' => 'USD'],
                ['post_id' => $post_id, 'meta_key' => '_cart_discount', 'meta_value' => 0],
                ['post_id' => $post_id, 'meta_key' => '_cart_discount_tax', 'meta_value' => 0],
                ['post_id' => $post_id, 'meta_key' => '_order_shipping', 'meta_value' => 0.00],
                ['post_id' => $post_id, 'meta_key' => '_order_shipping_tax', 'meta_value' => 0],
                ['post_id' => $post_id, 'meta_key' => '_order_tax', 'meta_value' => 0],
                ['post_id' => $post_id, 'meta_key' => '_order_total', 'meta_value' => $datos['total']],
                ['post_id' => $post_id, 'meta_key' => '_order_version', 'meta_value' => '3.5.2'],
                ['post_id' => $post_id, 'meta_key' => '_prices_include_tax', 'meta_value' => 'no'],
                ['post_id' => $post_id, 'meta_key' => '_billing_address_index', 'meta_value' => $infofull],
                ['post_id' => $post_id, 'meta_key' => '_shipping_address_index', 'meta_value' => ''],
                ['post_id' => $post_id, 'meta_key' => '_recorded_sales', 'meta_value' => 'yes'],
                ['post_id' => $post_id, 'meta_key' => '_recorded_coupon_usage_counts', 'meta_value' => 'yes'],
                ['post_id' => $post_id, 'meta_key' => '_order_stock_reduced', 'meta_value' => 'yes'],
            ]);
    }    
    /**
     * LLeva a la Vista para aceptar o rechazar las solicitudes
     * 
     * @access public
     * @return view
     */
    public function solicitudes()
    {
        view()->share('title', 'Solicitudes de compra');
        $solicitudes = $this->ArregloCompra();
        $moneda = Monedas::where('principal', 1)->get()->first();
        return view('tienda.solicitudes')->with(compact('solicitudes', 'moneda'));
    }
    /**
     * Obtiene todas las compras que fueron hecha dentro del sistema
     * 
     * @access public
     * @return array
     */
	public function getShopping(){
        $settings = Settings::first();
        $comprasID = DB::table($settings->prefijo_wp.'postmeta as wpm')
                    ->join($settings->prefijo_wp.'posts as wp', 'wp.ID', 'wpm.post_id')
                    ->select('wpm.post_id', 'wp.post_date', 'wp.post_status', 'code_coinbase', 'id_coinbase')
                    ->where([
                        ['meta_key', '=', '_payment_method_title'],
                        ['meta_value', '=', 'Wallet']
                    ])
                    ->orWhere([
                        ['meta_key', '=', '_payment_method_title'],
                        ['meta_value', '=', 'Coinpayment']
                    ])
                    ->orWhere([
                        ['meta_key', '=', '_payment_method_title'],
                        ['meta_value', '=', null]
                    ])
                    ->get();
        return $comprasID;
    }

    // funcion para encontrar obtener los productos de la factura
	public function getProductos($shop_id)
	{
        $settings = Settings::first();
		$totalProductos = DB::table($settings->prefijo_wp.'woocommerce_order_items')
													->select('order_item_id')
													->where('order_id', '=', $shop_id)
													->get();
		return $totalProductos;
    }
    
	// funcion para obtener el id de los productos comprados
	public function getIdProductos($id_item)
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
     * Obtiene informacion detallada de las compras
     * 
     * @access public
     * @param int $idpost - id de la compra
     * @return array
     */
    public function getDatos($idpost)
    {
        $settings = Settings::first();
        $total = DB::table($settings->prefijo_wp.'postmeta')
                    ->select('meta_value')
                    ->where([
                        ['post_id', '=', $idpost],
                        ['meta_key', '=', '_order_total'],
                    ])->first();
        $iduser = DB::table($settings->prefijo_wp.'postmeta')
                    ->select('meta_value')
                    ->where([
                        ['post_id', '=', $idpost],
                        ['meta_key', '=', '_customer_user'],
                    ])->first();
        
        $productos = $this->getProductos($idpost);
        $idproductos = 0;
        foreach ($productos as $producto) {
            $idproductos = $this->getIdProductos($producto->order_item_id);
        }

        $datos = [
            'total' => (!empty($total->meta_value)) ? $total->meta_value : '',
            'iduser' => (!empty($iduser->meta_value)) ? $iduser->meta_value : '',
            'idproducto' => $idproductos
        ];
        return $datos;
    }
    /**
     * Armar el arreglo Completo que se mostrara en la vista
     * 
     * @access public
     * @return array
     */
    public function ArregloCompra()
    {
        $compras = $this->getShopping();
        $arregloCompras = [];
        foreach ($compras as $compra) {
            $estadoEntendible = '';
            switch ($compra->post_status) {
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
            $datos = $this->getDatos($compra->post_id);
            
            $user = User::find($datos['iduser']);
            array_push($arregloCompras,[
                'usuario' => (!empty($user->display_name)) ? $user->display_name : 'Does not apply',
                'idcompra' => $compra->post_id,
                'total' => $datos['total'],
                'billetera' => (!empty($user->wallet_amount)) ? $user->wallet_amount : 0,
                'fecha' => $compra->post_date,
                // 'coinpayment' => $estadoEntendible2,
                'estado' => $estadoEntendible,
                'code_coinbase' => $compra->code_coinbase,
                'id_coinbase' => $compra->id_coinbase,
            ]);
        }
        if (!empty($arregloCompras)) {
            $tmparray = $arregloCompras[0];
            for ($i=1; $i < count($arregloCompras); $i++) { 
                $tmparray = array_merge($tmparray, $arregloCompras[$i]);
            }
        }
        return $arregloCompras;
    }
    /**
     * Actualiza las solicitude pendientes
     * 
     * @access public
     * @param int $id - id compra, string $estado - el estado al que va a pasar la compra
     * @return view
     */
    public function accionSolicitud($id, $estado)
    {
        if ($estado == 'wc-completed') {
            $settings = Settings::first();
            $datoscompra = $this->getDatos($id);
            $user = User::find($datoscompra['iduser']);
            $admin = User::find(1);
            // $coinpayment = DB::table($settings->prefijo_wp.'postmeta')->where([['post_id', '=', $id], ['meta_key', '=', '_payment_method_title']])->select('meta_value')->get()[0];
            $file = DB::table($settings->prefijo_wp.'posts')->where('ID', $datoscompra['idproducto'])->select('guid')->first();

            $tipo_pago = DB::table($settings->prefijo_wp.'posts')->where('ID', $datoscompra['idproducto'])->select('post_content_filtered')->first();
            // if ($coinpayment->meta_value == 'Wallet') {
            //     $user->wallet_amount = ($user->wallet_amount - floatval($datoscompra['total']));
            //     $admin->wallet_amount = ($admin->wallet_amount + floatval($datoscompra['total']));
            //     $user->save();
            //     $admin->save();
            // }

            if ($tipo_pago->post_content_filtered != 'asr' && $datoscompra['idproducto'] != 0) {
                Mail::send('emails.linkFile',  ['ruta' => $file->guid], function($msj) use ($user){

                    $msj->subject('Contenido del Producto');
    
                    $msj->to($user->user_email);
    
                });
            }
            
            $activacion = new ActivacionController;
            $activacion->activarUsuarios($datoscompra['iduser']);
        }
        $this->actualizarBD($id, $estado);
        return redirect('tienda/solicitudes')->with('msj', 'Estado de la Solicitud Actualizada con Exito');
    }
    /**
     * Actualiza la informacion de la ordenes de compra en el wp
     *
     * @access public 
     * @param int $id - id Compra, string $estado - estado de la compra
     */
    public function actualizarBD($id, $estado)
    {
        $settings = Settings::first();
        DB::table($settings->prefijo_wp.'posts')
            ->where('ID', $id)
            ->update([
                'post_status' => $estado,
                'post_modified' => Carbon::now(),
                'post_modified_gmt' => Carbon::now(),
            ]);
        $order_key = DB::table($settings->prefijo_wp.'postmeta')->where(['post_id' => $id, 'meta_key' => '_orden_key'])
                            ->select('meta_value')->first();
        DB::table($settings->prefijo_wp.'postmeta')->insert([
            ['post_id' => $id, 'meta_key' => '_edit_lock', 'meta_value' => Carbon::now()->format('dmYs').':1'],
            ['post_id' => $id, 'meta_key' => '_edit_last', 'meta_value' => 1],
            ['post_id' => $id, 'meta_key' => '_order_key', 'meta_value' => $order_key->meta_value],
        ]);
        if ($estado == 'wc-completed') {
            DB::table($settings->prefijo_wp.'postmeta')->where(['post_id' => $id, 'meta_key' => '_date_completed'])
                    ->update(['meta_value' => Carbon::now()->format('dmYs')]);
            DB::table($settings->prefijo_wp.'postmeta')->where(['post_id' => $id, 'meta_key' => '_completed_date'])
                    ->update(['meta_value' => Carbon::now()->format('dmYs')]);
            DB::table($settings->prefijo_wp.'postmeta')->where([ 'post_id' => $id, 'meta_key' => '_date_paid'])
                    ->update(['meta_value' => Carbon::now()->format('dmYs')]);
            DB::table($settings->prefijo_wp.'postmeta')->where([ 'post_id' => $id, 'meta_key' => '_paid_date'])
                    ->update(['meta_value' => Carbon::now()->format('dmYs')]);
            DB::table($settings->prefijo_wp.'postmeta')->insert([
                ['post_id' => $id, 'meta_key' => '_download_permissions_granted', 'meta_value' => 'yes'],
            ]);
        }        
    }
}