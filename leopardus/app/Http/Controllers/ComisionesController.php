<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Carbon\Carbon;
use App\User; 
use App\Settings;
use App\Commission; 
use App\Notification;
use App\SettingsComision;
use App\Http\Controllers\IndexController;
use App\Http\Controllers\WalletController;

use function GuzzleHttp\json_decode;

class ComisionesController extends Controller
{

    /**
     * Mostramos el home de la aplicación.
     *
     * @return view()
     */
    //Historial de Comisiones para el usuario
    public function index(){
    	// DO MENU
        view()->share('do', collect(['name' => 'inicio', 'text' => 'Inicio']));
        //
        $comisiones = Commission::where('user_id', '=', Auth::user()->ID)->get();
        //******************
        //Marcar como leídas las notificaciones pendientes de Nuevas Comisiones de Fin de Mes
        $notificaciones_pendientes = DB::table('notifications')
                                        ->select('id')
                                        ->where('user_id', '=', Auth::user()->ID)
                                        ->where('notification_type', '=', 'CO')
                                        ->where('status', '=', 0)
                                        ->get();
        foreach ($notificaciones_pendientes as $not){
            Notification::find($not->id)->update(['status' => 1]);
        }
        //********************
        return view('dashboard.commissionsRecords')->with(compact('comisiones'));
    }
    //Función que devuelve los referidos de un determinado usuario
    public function getReferreds($user_id){
		//Referidos Directos (Nivel 1)
        $referidos = User::select('ID', 'user_email', 'status')->where('referred_id', $user_id)->get()->toArray();
		return $referidos;
	}
	//Función que devuelve el ID de las compras de un determinado usuario
	//que no hayan sido procesadas en una comisión anterior.
	public function getShopping($user_id){
        $settings = Settings::first();
        $comprasID = DB::table($settings->prefijo_wp.'postmeta')
                    ->select('post_id')
                    ->where('meta_key', '=', '_customer_user')
                    ->where('meta_value', '=', $user_id)
                    ->get();
        return $comprasID;
    }
    
    /**
     * Permite obtener al usuario que compro este producto
     *
     * @param integer $idpost
     * @return void
     */
    public function getIdUser($idpost)
    {
        $settings = Settings::first();
        $comprasID = DB::table($settings->prefijo_wp.'postmeta')
                    ->select('meta_value')
                    ->where('meta_key', '=', '_customer_user')
                    ->where('post_id', '=', $idpost)
                    ->first();
        return $comprasID->meta_value;
    }

	//Función que devuelve los datos de una compra determinada
	public function getShoppingDetails($shop_id){
        $settings = Settings::first();
		$datosCompra = DB::table($settings->prefijo_wp.'posts')
                        ->select('post_date', 'post_status')
                        ->where('ID', '=', $shop_id)
                        ->first();
        return $datosCompra;
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
	//Función que devuelve el total pagado en una compra determinada
	public function getShoppingTotal($shop_id){
        $settings = Settings::first();
		$totalCompra = DB::table($settings->prefijo_wp.'postmeta')
				        ->select('meta_value')
				        ->where('post_id', '=', $shop_id)
				        ->where('meta_key', '=', '_order_total')
				        ->first();
		return $totalCompra->meta_value;
	}
  // Obtiene al usuario a a todo los usuarios

  public function ObtenerUsuarios()

  {

    $GLOBALS['settingsComision'] = SettingsComision::find(1);

    if (Auth::user()->rol_id == 0) {
        $this->bonoUnilevel(Auth::user()->ID);
    //   $usuarios = User::select('ID', 'status', 'rol_id', 'display_name')->get();

    //   foreach ($usuarios as $user) {
    //     // $this->bonoDirecto($user->ID);
    //     // $this->generarComision($user->ID);
    //     // $this->bonoLiderazgo($user->ID);
    //   }

      return redirect('mioficina/admin/commissionrecords')->with('mjs', 'The commissions have been generated successfully.');

    } else {
        // if (Auth::user()->ID == 178) {
        // //     $this->generarComision(Auth::user()->ID);
        // //     // $this->rentabilidadMensual();
        // //     // $this->bonoPorPuntos(Auth::user()->ID);
            $this->bonoUnilevel(Auth::user()->ID);
        // }

      return redirect('mioficina/admin/network/commissionsrecords');

    }

  }


  // detalla la comision para luego ser guardada
  public function detallesComision($iduser, $idreferido, $referred_email, $nivel, $valorComision, $lado)
  {   
    $detalles = "";
    $settings = Settings::first();
    $user = User::find($iduser);
    $compras = $this->getShopping($idreferido);
        foreach ($compras as $compra) {
            $idcomision = '123'.$compra->post_id;
            $check = DB::table('commissions')
                        ->select('id')
                        ->where('user_id', '=', $iduser)
                        ->where('compra_id', '=', $idcomision)
                        ->first();
            if ($check == null) {
              //Se obtienen los datos de cada compra
              $datosCompra = $this->getShoppingDetails($compra->post_id);
              //Se verifica que la compra sea del mes que se está pagando
              $fechaCompra = new Carbon($datosCompra->post_date);
              $fechaActivacion = new Carbon($user->fecha_activacion);
              if ($datosCompra->post_status == 'wc-completed') {
                  $referido = User::find($idreferido);
                // $totalCompra = $this->getShoppingTotal($compra->post_id);
                $totalProductos = $this->getProductos($compra->post_id);
                foreach ($totalProductos as $producto) {
                    $totalPrecioProducto = $this->getTotalProductos($producto->order_item_id);
                    
                    if ($totalPrecioProducto != 0) {
                        // $this->PuntosPaquetes($iduser, $totalPrecioProducto, $referred_email, $lado);
                        $this->guardarComision($iduser, $idcomision, 0, $referred_email, $nivel, 'Primera Compra sin Comision', 'referido');
                    }
                }
              }
            }
         }
  }
  /**
   * Agrega los puntos obtenido por los paquetes comprando mis usuarios
   *
   * @param integer $iduser - id usuario
   * @param integer $totalcomision - puntos obtenidos
   * @return void
   */
  public function PuntosPaquetes(int $iduser, float $totalcomision, string $referred_email, $lado)
  {
        if ($iduser != 1) {
            $user = User::find($iduser);
            // dd($user, $totalcomision, $lado);
            $userIActive = User::where([
                ['position_id', '=', $user->ID],
                ['status', '=', 1],
                ['ladomatrix', '=', 'I']
            ])->first();
            $userDActive = User::where([
                ['position_id', '=', $user->ID],
                ['status', '=', 1],
                ['ladomatrix', '=', 'D']
            ])->first();
            if (!empty($userIActive) && !empty($userDActive)) {
                if ($lado != '' && $user->porc_rentabilidad < $user->rentabilidad) {
                    $referido = User::where('user_email', $referred_email)->first();
                    $puntosI = 0; $puntosD = 0;
                    if ($lado == 'P') {
                        $user->puntosP = ($user->puntosP + $totalcomision);
                        $user->save();
                    } else {
                        if ($referido->ID != $iduser) {
                            if ($lado == 'I') {
                                $user->puntosizq = ($user->puntosizq + $totalcomision);
                                $puntosI = $totalcomision;
                            }elseif($lado == 'D'){
                                $user->puntosder = ($user->puntosder + $totalcomision);
                                $puntosD = $totalcomision;
                            }
                            $user->save();
                            $concepto = 'Puntos por las compras del usuario '.$referido->display_name;
                            $datos = [
                                'iduser' => $iduser,
                                'usuario' => $user->display_name,
                                'descripcion' => $concepto,
                                'puntos' => 0,
                                'puntosI' => $puntosI,
                                'puntosD' => $puntosD,
                                'tantechcoin' => 0,
                                'descuento' => 0,
                                'debito' => 0,
                                'credito' => 0,
                                'balance' => 0,
                                'tipotransacion' => 2
                            ];
                            $funciones = new WalletController;
                            // $funciones->saveWallet($datos);
                        }
                    }
                }
            }
        }
  }

  // guarda la comision una vez procesada

  public function guardarComision($iduser, $idcompra, $totalComision, $referred_email, $referred_level, $concepto, $tipo_comision)
  {
        // if ($iduser > 4) {
            // $user = User::find($iduser);
            $dinero = 0; $puntos = 0;
            $dinero = $totalComision;
            $comision = new Commission();
            $comision->user_id = $iduser;
            $comision->compra_id = $idcompra;
            $comision->date = Carbon::now();
            $comision->total = $totalComision;
            $comision->concepto = $concepto;
            $comision->tipo_comision = $tipo_comision;
            $comision->referred_email = $referred_email;
            $comision->referred_level = $referred_level;
            $comision->status = true;

            if ($concepto != 'Primera Compra sin Comision') {
                $user = User::find($iduser);
                if ($user->porc_rentabilidad < $user->rentabilidad) {
                    
                    if ($idcompra == 51) {
                        $user->porc_rentabilidad = ($user->porc_rentabilidad + $totalComision);
                        if ($user->porc_rentabilidad >= $user->rentabilidad) {
                            $user->porc_rentabilidad = $user->rentabilidad;
                        }
                    }
                    $user->wallet_amount = ($user->wallet_amount + $dinero);
                    $user->save();
                    $datos = [
                        'iduser' => $iduser,
                        'usuario' => $user->display_name,
                        'descripcion' => $concepto,
                        'puntos' => 0,
                        'puntosI' => 0,
                        'puntosD' => 0,
                        'descuento' => 0,
                        'debito' => $dinero,
                        'tantechcoin' => 0,
                        'credito' => 0,
                        'balance' => $user->wallet_amount,
                        'tipotransacion' => 2
                    ];
                    $funciones = new WalletController;
                    $funciones->saveWallet($datos);
                }else{
                    if ($user->ID == 1) {
                        $user->wallet_amount = ($user->wallet_amount + $dinero);
                        $user->save();
                        $datos = [
                            'iduser' => $iduser,
                            'usuario' => $user->display_name,
                            'descripcion' => $concepto,
                            'puntos' => 0,
                            'puntosI' => 0,
                            'puntosD' => 0,
                            'descuento' => 0,
                            'debito' => $dinero,
                            'tantechcoin' => 0,
                            'credito' => 0,
                            'balance' => $user->wallet_amount,
                            'tipotransacion' => 2
                        ];
                        $funciones = new WalletController;
                        $funciones->saveWallet($datos);
                    }
                }
            }
            $comision->save();
        // }
  }

  // verifica en que nivel de comision va cobrar la comision

  public function generarComision($iduser)
  {
        $user = User::find($iduser);
        $funciones = new IndexController;
        $todousuario = User::where([
            ['position_id', '=', $iduser],
        ])->get();
        $lado = '';
        if (!empty($todousuario)) {
            foreach ($todousuario as $usuario) {
                if ($usuario['status'] == 1) {
                    $this->detallesComision($iduser, $usuario['ID'], $usuario['user_email'], 1, 0, $usuario['ladomatrix']);
                }
                if ($usuario['ladomatrix'] == 'D') {
                    $todousuarioD = $funciones->generarArregloUsuario($usuario['ID']);
                    if (!empty($todousuarioD)) {
                        foreach ($todousuarioD as $usuarioD) {
                            if ($usuarioD['status'] == 1) {
                                $this->detallesComision($iduser, $usuarioD['ID'], $usuarioD['email'], $usuarioD['nivel']+1, 0, 'D');
                            }
                        }
                    }
                }
                if ($usuario['ladomatrix'] == 'I') {
                    $todousuarioI = $funciones->generarArregloUsuario($usuario['ID']);
                    if (!empty($todousuarioI)) {
                        foreach ($todousuarioI as $usuarioI) {
                            if ($usuarioI['status'] == 1) {
                                $this->detallesComision($iduser, $usuarioI['ID'], $usuarioI['email'], $usuarioI['nivel']+1, 0, 'I');
                            }
                        }
                    }
                }
            }
        }
  }

  /**
   * Permite pagar el pono unilevel
   *
   * @param integer $iduser
   * @return void
   */
  public function bonoUnilevel($iduser)
    {
        $user = User::find($iduser);
        $funciones = new IndexController;
        $GLOBALS['allUsers'] = [];
        $referidosDirectos = $funciones->getReferreds($iduser);
        $funciones->getReferredsAll($referidosDirectos, 1, 4, [], 'arbol');
        $TodosUsuarios = $funciones->ordenarArreglosMultiDimensiones($GLOBALS['allUsers'], 'ID', 'numero');
        $settings = Settings::first();
        $valores = json_decode($settings->valor_niveles);
        if (!empty($user->paquete)) {
            $paquete = json_decode($user->paquete);
            if ($paquete->nivel != 0) {
                foreach ($TodosUsuarios as $user) {
                    if ($user['nivel'] <= $paquete->nivel) {
                        $nivel = 'nivel'.$user['nivel'];
                        $compras = $this->getShopping($user['ID']);
                        foreach ($compras as $compra) {
                            $idcomision = '34'.$compra->post_id;
                            $check = DB::table('commissions')
                                    ->select('id')
                                    ->where('user_id', '=', $iduser)
                                    ->where('compra_id', '=', $idcomision)
                                    ->first();
                            if ($check == null) {
                                //Se obtienen los datos de cada compra
                                $datosCompra = $this->getShoppingDetails($compra->post_id);

                                if ($datosCompra->post_status == 'wc-completed') {
                                    $totalProductos = $this->getProductos($compra->post_id);
                                    foreach ($totalProductos as $producto) {
                                        $totalPrecioProducto = $this->getTotalProductos($producto->order_item_id);
                                        $porcent = ((int)$valores->$nivel / 100);
                                        if ($porcent > 0 && $totalPrecioProducto > 0) {
                                            $pagar = ($totalPrecioProducto * $porcent);
                                            $this->guardarComision($iduser, $idcomision, $pagar, $user['email'], $user['nivel'], 'Bono Unilevel Usuario '.$user['nombre'].' por la orden '.$compra->post_id, 'referido');
                                            $this->matchingBonus($iduser, $idcomision, $pagar, $user['email'], $compra->post_id, $user['nombre']);
                                        }
                                    }
                                }
                            }
                        }
                    }else{
                        $compras = $this->getShopping($user['ID']);
                        foreach ($compras as $compra) {
                            $idcomision = '34'.$compra->post_id;
                            $check = DB::table('commissions')
                                    ->select('id')
                                    ->where('user_id', '=', $iduser)
                                    ->where('compra_id', '=', $idcomision)
                                    ->first();
                            if ($check == null) {
                                $datosCompra = $this->getShoppingDetails($compra->post_id);
                                if ($datosCompra->post_status == 'wc-completed') {
                                    $totalProductos = $this->getProductos($compra->post_id);
                                    foreach ($totalProductos as $producto) {
                                        $pagar = 0;
                                        $this->guardarComision($iduser, $idcomision, $pagar, $user['email'], $user['nivel'], 'Primera Compra sin Comision', 'referido');
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * Permite pagar el bono matching generado por el bono directo
     *
     * @param integer $iduser
     * @param integer $idcomision
     * @param float $total
     * @param string $email_referred
     * @param integer $idcompra
     * @param string $name_referred
     * @return void
     */
    public function matchingBonus($iduser, $idcomision, $total, $email_referred, $idcompra, $name_referred)
    {
        if ($total > 0) {
            $sponsors = $this->getSponsor($iduser, [], 0, 'ID', 'referred_id');
            foreach ($sponsors as $sponsor) {
                if ($sponsor->ID != $iduser) {
                    $porcent = 0.10;
                    if ($sponsor->nivel == 2 || $sponsor->nivel == 4) {
                        $porcent = 0.05;
                    }
                    $userAPagar = User::find($iduser);
                    if ($sponsor->ID == 2) {
                        $pagar = ($total * $porcent);
                        $idcomision2 = $idcomision.'20';
                        $check = DB::table('commissions')
                                                ->select('id')
                                                ->where('user_id', '=', $sponsor->ID)
                                                ->where('compra_id', '=', $idcomision2)
                                                ->first();
                        if ($check == null) {
                            $concepto = 'Bono Matching Usuario '.$userAPagar->display_name.' por la orden '.$idcompra;
                            $this->guardarComision($sponsor->ID, $idcomision2, $pagar, $email_referred, $sponsor->nivel, $concepto, 'matching');
                        }
                    }elseif ($sponsor->ID >= 315 || $sponsor->ID <= 318) {
                        $pagar = ($total * $porcent);
                        $idcomision2 = $idcomision.'20';
                        $check = DB::table('commissions')
                                                ->select('id')
                                                ->where('user_id', '=', $sponsor->ID)
                                                ->where('compra_id', '=', $idcomision2)
                                                ->first();
                        if ($check == null) {
                            $concepto = 'Bono Matching Usuario '.$userAPagar->display_name.' por la orden '.$idcompra;
                            $this->guardarComision($sponsor->ID, $idcomision2, $pagar, $email_referred, $sponsor->nivel, $concepto, 'matching');
                        }
                    }
                }
            } 
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
    private function getDataSponsor($id, $nivel, $typeTree) : object
    {
        $resul = User::where($typeTree, '=', $id)->get();
        foreach ($resul as $user) {
            $user->nivel = $nivel;
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
        if (count($data) > 0 && $nivel <= 4) {
            foreach($data as $user){
                $array_tree_user [] = $user;
                $array_tree_user = $this->getSponsor($user->$keySponsor, $array_tree_user, ($nivel+1), $typeTree, $keySponsor);
            }
        }
        return $array_tree_user;
    }

  	public function aprobarComision($id)
	{
		$comision = Commission::find($id);
		$user = User::find($comision->user_id);
		$user->wallet_amount = $user->wallet_amount + $comision->total;
		$user->save();
		$comision->status = '1';
		$comision->save();
        return redirect('mioficina/admin/commissionrecords')->with('msj', 'The Commission of the user '.$user->display_name.' has been approved');
	}



    public function record_commissions(){
    	// DO MENU
    	view()->share('title', 'Historial de Comisiones');
        view()->share('do', collect(['name' => 'Historial de Comisiones', 'text' => 'Reportes']));
        //
        $settings = Settings::first();
        $sql="SELECT c.*, wu.display_name FROM commissions c, ".$settings->prefijo_wp."users wu WHERE c.user_id=wu.ID and c.tipo=1  order by c.date desc";
            $comisiones =DB::select($sql);
    	return view('admin.commissionRecords')->with(compact('comisiones'));
    }

     //comisiones con filtro de fechas 
    public function comisiones_filter()
    {
        //TITLE
        View::share('title', 'Comisiones');
        $comision = Commission::orderBy('id','DESC')->where('user_id',auth()->user()->ID)->paginate(5);
        return view('admin.comisiones_filter')->with(compact('comision'));
    }

    //filtro de fechas funcionando  
    public function filter_comisiones()
    {
        //TITLE
        View::share('title', 'Comisiones');
        $primero = $_POST["primero"];
        $segundo = $_POST["segundo"];
        $comision=Commission::whereDate("date",">=",$primero)
             ->whereDate("date","<=",$segundo)
             ->where('user_id', '=', Auth::user()->ID )
             ->get(); 
        return view('admin.filter_comisiones')->with(compact('comision'));
    }
    /**
     * Trae la informacion de las compras y las comisiones en general
     *
     * @return void
     */
    public function reporteCompraComision($balance, $tipo)
    {
        // TITLE
        view()->share('title', 'Reports');
        
        $users = User::where('ID', '!=', 1)->get();
        $datos = [];
        $tmp = [];
        $fechas = [];
        foreach ($users as $user) {
            $compras = $this->getShopping($user->ID);
            foreach ($compras as $compra) {
                $datocompra = $this->getShoppingDetails($compra->post_id);
                if ($datocompra->post_status == 'wc-completed') {
                    if ($balance != 'puntos') {
                        if ($tipo == 'ingreso' || $tipo == 'todo') {
                            $datos [] = [
                                'iduser' => $user->ID,
                                'nombre' => $user->display_name,
                                'descripcion' => 'Buy order '.$compra->post_id,
                                'totalcompra' => $this->getShoppingTotal($compra->post_id),
                                'totalcomision' => 0,
                                'fecha' => $datocompra->post_date
                            ];
                        }
                    }
                }
            }
            $comisiones = DB::table('walletlog')->where([
                ['iduser', '=', $user->ID],
                ['descripcion', '!=', 'Primera Compra sin Comision'],
                [$balance, '!=', 0]
            ])->select('debito', 'puntos', 'descripcion', 'created_at')->get();
            foreach ($comisiones as $comi) {
                if ($tipo == 'egreso' || $tipo == 'todo') {
                    $datos [] = [
                        'iduser' => $user->ID,
                        'nombre' => $user->display_name,
                        'descripcion' => $comi->descripcion,
                        'totalcompra' => 0,
                        'totalcomision' => $comi->$balance,
                        'fecha' => $comi->created_at
                    ];
                }
            }
        }
        // foreach ($tmp as $item ) {
        //     if ($item['totalcompra'] != 0 || $item['totalcomision'] != 0) {
        //         $datos [] = $item;
        //     }
        // }
        return view('admin.comprascomision')->with(compact('datos', 'fechas', 'balance', 'tipo'));
    }
    
    public function reporteCompraComisionxFecha(Request $fechas)
    {
        // TITLE
        view()->share('title', 'Reports');
        
        $users = User::where('ID', '!=', 1)->get();
        $datos = [];
        $balance = $fechas->balance;
        $tipo = $fechas->tipo;
        foreach ($users as $user) {
            $compras = $this->getShopping($user->ID);
            foreach ($compras as $compra) {
                $datocompra = $this->getShoppingDetails($compra->post_id);
                $fechaCompra = new Carbon($datocompra->post_date);
                $fecha1 = new Carbon($fechas->primero);
                $fecha2 = new Carbon($fechas->segundo);
                if ($datocompra->post_status == 'wc-completed' && $fechaCompra->format('ymd') >= $fecha1->format('ymd') && $fechaCompra->format('ymd') <= $fecha2->format('ymd')) {
                    if ($balance != 'puntos') {
                        if ($tipo == 'ingreso' || $tipo == 'todo') {
                            $datos [] = [
                                'iduser' => $user->ID,
                                'nombre' => $user->display_name,
                                'descripcion' => 'Buy order '.$compra->post_id,
                                'totalcompra' => $this->getShoppingTotal($compra->post_id),
                                'totalcomision' => 0,
                                'fecha' => $datocompra->post_date
                            ];
                        }
                    }
                }
            }
            $comisiones = DB::table('walletlog')
                        ->where('iduser', '=', $user->ID)
                        ->whereDate("created_at",">=",$fechas->primero)
                        ->whereDate("created_at","<=",$fechas->segundo)
                        ->where($balance, '!=', 0)
                        ->select('debito', 'puntos', 'descripcion', 'created_at')
                        ->get();
            foreach ($comisiones as $comi) {
                if ($tipo == 'egreso' || $tipo == 'todo') {
                    $datos [] = [
                        'iduser' => $user->ID,
                        'nombre' => $user->display_name,
                        'descripcion' => $comi->descripcion,
                        'totalcompra' => 0,
                        'totalcomision' => $comi->$balance,
                        'fecha' => $comi->created_at
                    ];
                }
            }
        }
        return view('admin.comprascomision')->with(compact('datos', 'fechas', 'balance', 'tipo'));
    }

    /**
     * Trae el monto total de las compras
     * 
     * @param int $iduser - usuario a verificar
     * @return  string
     */
    private function montoCompraTotales($iduser, $fechas)
    {
        $compras = $this->getShopping($iduser);
        $comprasTotal = 0;
        foreach ($compras as $compra) {
            $datocompra = $this->getShoppingDetails($compra->post_id);
            if (empty($fechas)) {
                if ($datocompra->post_status == 'wc-completed') {
                    $comprasTotal = ($comprasTotal + $this->getShoppingTotal($compra->post_id));
                }
            }else{
              $fechaCompra = new Carbon($datocompra->post_date);
              $fecha1 = new Carbon($fechas->primero);
              $fecha2 = new Carbon($fechas->segundo);
              if ($datocompra->post_status == 'wc-completed' && $fechaCompra->format('ymd') >= $fecha1->format('ymd') && $fechaCompra->format('ymd') <= $fecha2->format('ymd')) {
                $comprasTotal = ($comprasTotal + $this->getShoppingTotal($compra->post_id));
              }
            }
        }
        return $comprasTotal;
    }

    /**
     * Permite pagar los bonos por los puntos acumulado de los usuarios a lo largo del dia
     *
     * @param integer $iduser
     * @return void
     */
    public function bonoPorPuntos($iduser)
    {
        $user = User::find($iduser);
        $settings = Settings::first();
        $userIActive = User::where([
            ['position_id', '=', $user->ID],
            ['status', '=', 1],
            ['ladomatrix', '=', 'I']
        ])->first();
        $userDActive = User::where([
            ['position_id', '=', $user->ID],
            ['status', '=', 1],
            ['ladomatrix', '=', 'D']
        ])->first();
        $user->paquete = json_decode($user->paquete);
        if (!empty($userDActive) && !empty($userIActive)) {
            $pagar = 0;
            if ($user->puntosizq >= $user->puntosder) {
                $pagar = $user->puntosder;
            }else{
                $pagar = $user->puntosizq;
            }
            if (!empty($user->paquete)) {
                if ($pagar > $user->paquete->monto) {
                    $pagar = $user->paquete->monto;
                }
            }
            if ($pagar != 0) {
                $user->puntosizq = ($user->puntosizq - (float)$pagar);
                $user->puntosder = ($user->puntosder - (float)$pagar);
                $porcentaje = ($settings->valortantech / 100);
                $totalcomision = ((float)$pagar * $porcentaje);
                
                $user->paquete = json_encode($user->paquete);
                // $this->guardarComision($iduser, 20, $totalcomision, $user->user_email, 0, 'Bonos Binario', 'bono');
                $user->save();
            }
        }
    }

    /**
     * Permite pagar mensualemente un bono de rentabilidad
     *
     * @return void
     */
    public function rentabilidadMensual()
    {
        $settings = Settings::first();
        // $rentabilidad = (($settings->valorrentabilidad / 100));
        $users = User::all();
        foreach ($users as $user) {
            // $userIActive = User::where([
            //     ['position_id', '=', $user->ID],
            //     ['status', '=', 1],
            //     ['ladomatrix', '=', 'I']
            // ])->first();
            // $userDActive = User::where([
            //     ['position_id', '=', $user->ID],
            //     ['status', '=', 1],
            //     ['ladomatrix', '=', 'D']
            // ])->first();
            // if (!empty($userDActive) && !empty($userIActive)) {
                if ($user->ID != 1) {
                    if (!empty($user->paquete)) {
                        $paquete = json_decode($user->paquete);
                        $valorRentado = 0;
                        if (!empty($paquete)) {
                            if (!empty($paquete->tipo_pago)) {
                                if ($paquete->tipo_pago != 'asr') {
                                    $valorRentado = ($paquete->monto * $paquete->porcentaje);
                                }   
                            }else{
                                $valorRentado = ($paquete->monto * $paquete->porcentaje);
                            }
                        }
                        if ($valorRentado > 0) {
                            $this->guardarComision($user->ID, 51, $valorRentado, $user->user_email, 0, 'Rentabilidad Mensual', 'bono');
                        }
                    }
                }
            // }
        }
    }

    /**
     * Permite pagar el bono por las cantidad de compras que han hecho en mi red
     *
     * @param integer $iduser
     * @return void
     */
    public function clubBono($iduser)
    {
        $funciones = new IndexController;
        $GLOBALS['allUsers'] = [];
        $referidosDirectos = $funciones->getReferreds($iduser);
        $funciones->getReferredsAll($referidosDirectos, 1, 2, [], 'arbol');
        $TodosUsuarios = $funciones->ordenarArreglosMultiDimensiones($GLOBALS['allUsers'], 'ID', 'numero');
        $totalPuchaseRed = 0;
        foreach ($TodosUsuarios as $user) {
            if ($user['nivel'] == 1) {
                $compras = $this->getShopping($user['ID']);
                foreach ($compras as $compra ) {
                    $detelles = $this->getShoppingDetails($compra->post_id);
                    if ($detelles->post_status == 'wc-completed') {
                        $totalPuchaseRed += $this->getShoppingTotal($compra->post_id);
                    }
                }
            }
        }
        if ($totalPuchaseRed > 0) {
            $restriciones = $this->getArrayCondition();
            foreach ($restriciones as $indexRestri => $restrincion) {
                if ($totalPuchaseRed >= $restrincion['facturacion']) {
                    $tipo_comision = 'bono '.$indexRestri;
                    $idcomision2 = $iduser.'70';
                    $check = DB::table('commissions')
                                            ->select('id')
                                            ->where('user_id', '=', $iduser)
                                            ->where('compra_id', '=', $idcomision2)
                                            ->where('tipo_comision', '=', $tipo_comision)
                                            ->first();
                    if ($check == null) {
                        $user = User::find($iduser);
                        $this->guardarComision($iduser, $idcomision2, $restrincion['bono'], $user->user_email, 0, 'Bonos Liderazgo - Nivel '.$indexRestri, $tipo_comision);
                    }   
                }
            }
        }
    }

    /**
     * Permite obtener las restriciones para poder ganar los bonos
     *
     * @return array
     */
    public function getArrayCondition(): array
    {
        return [
            'L1' => [
                'facturacion' => 950,
                'bono' => 234
            ],
            'L2' => [
                'facturacion' => 1900,
                'bono' => 234
            ],
            'L3' => [
                'facturacion' => 2850,
                'bono' => 234
            ],
            'L4' => [
                'facturacion' => 3750,
                'bono' => 234
            ],
            'L5' => [
                'facturacion' => 4700,
                'bono' => 234
            ],
            'L6' => [
                'facturacion' => 5650,
                'bono' => 234 
            ],
            'L7' => [
                'facturacion' => 7550,
                'bono' => 506
            ],
            'L8' => [
                'facturacion' => 7450,
                'bono' => 506
            ],
            'L9' => [
                'facturacion' => 12550,
                'bono' => 856
            ],
            'L10' => [
                'facturacion' => 15700,
                'bono' => 856
            ],
            'L11' => [
                'facturacion' => 18850,
                'bono' => 856
            ],
            'L12' => [
                'facturacion' => 23600,
                'bono' => 1359
            ],
            'L13' => [
                'facturacion' => 28300,
                'bono' => 1359
            ],
            'L14' => [
                'facturacion' => 35400,
                'bono' => 2039
            ],
            'L15' => [
                'facturacion' => 42450,
                'bono' => 2039
            ],
            'L16' => [
                'facturacion' => 49550,
                'bono' => 2039
            ],
            'L17' => [
                'facturacion' => 56650,
                'bono' => 2039
            ],
            'L18' => [
                'facturacion' => 69200,
                'bono' => 3776
            ],
            'L19' => [
                'facturacion' => 81800,
                'bono' => 3776
            ],
            'L20' => [
                'facturacion' => 94400,
                'bono' => 3776
            ],
        ];
    }


}

