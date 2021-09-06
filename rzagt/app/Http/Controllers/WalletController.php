<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use App\User; 
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon; 
use App\Wallet;
use App\MetodoPago; use App\SettingsComision; use App\Pagos; use App\Monedas;
use App\Http\Controllers\IndexController;
use PragmaRX\Google2FA\Google2FA;
use Illuminate\Support\Facades\Mail;


class WalletController extends Controller
{
	function __construct()
	{
        // TITLE
		view()->share('title', 'Wallet');
	}
	
	/**
	 *  Va a la vista principal de la billetera cash
	 * 
	 * @access public
	 * @return view
	 */
	public function index(){
	   
		$moneda = Monedas::where('principal', 1)->get()->first();
		$metodopagos = MetodoPago::all();
		$comisiones = SettingsComision::select('comisionretiro', 'comisiontransf')->where('id', 1)->get();
		$cuentawallet = '';
		$arrayBilletera = [];
		$walletCreditar = 0;
		$totalGanado = 0;
		$pagosPendientes = false;
		$validarPagos = Pagos::where([
			['iduser', '=', Auth::user()->ID],
			['estado', '=', 10]
		])->first();
		if (!empty($validarPagos)) {
			$pagosPendientes = true;
		}
		if (Auth::user()->ID != 1) {
			$wallets = Wallet::where([
				['iduser', '=', Auth::user()->ID], 
				['debito', '!=', 0],
			])->orWhere([
				['iduser', '=', Auth::user()->ID], 
				['credito', '!=', 0]
				])->get();
			// $totalGanado = $wallets->sum('credito');
			$cuentawallet = DB::table('user_campo')->where('ID', Auth::user()->ID)->select('paypal')->get()[0];
			$cuentawallet = $cuentawallet->paypal;
	
			$disponible = Auth::user()->wallet_amount;
			$walletRentabilida = DB::table('log_rentabilidad')->where([
				['iduser', '=', Auth::user()->ID],
				['limite', '>', 'retirado'],
				['progreso', '<', '100']
			])->first();
			// if (!empty($walletRentabilida)) {
			// 	if ($walletRentabilida->nivel_minimo_cobro > 0) {
			// 		if (Auth::user()->rol_id < 7) {
			// 			$walletCreditar = Wallet::where([
			// 				['iduser', '=', Auth::user()->ID],
			// 				['descripcion', 'like', '%Pago de utilidades%']
			// 			])->get()->sum('debito');
			// 			$disponible = ($disponible - $walletCreditar);
			// 		}
			// 	}
			// }
		}else{
			$disponible = 0;
			$wallets = Wallet::where([
				['id', '=', 0],
			])->get();
		}

		foreach ($wallets as $wallet) {
			$arrayBilletera [] = [
				'id' => $wallet->id,
				'usuario' => $wallet->usuario,
				'email' => $wallet->email_referred,
				'fecha' => date('Y-m-d', strtotime($wallet->created_at)),
				'descripcion' => $wallet->descripcion,
				'debito' => $wallet->debito,
				'credito' => $wallet->credito,
				'descuento' => $wallet->descuento,
				'balance' => $wallet->balance
			];
		}



		// foreach ($walletRentabilida as $wallet) {
		// 	$arrayBilletera [] = [
		// 		'id' => $wallet->id,
		// 		'usuario' => 'Rentabilidad',
		// 		'email' => '',
		// 		'fecha' => date('d-m-Y', strtotime($wallet->created_at)),
		// 		'descripcion' => $wallet->concepto,
		// 		'debito' => $wallet->debito,
		// 		'credito' => $wallet->credito,
		// 		'descuento' => 0,
		// 		'balance' => $wallet->balance
		// 	];
		// }

		$index = new IndexController;
		$wallets = $index->ordenarArreglosMultiDimensiones($arrayBilletera, 'fecha', 'cadena');
		$walletCreditar = 0;
		$nombre_user = '';
		
	   	return view('wallet.indexwallet')->with(compact('metodopagos', 'comisiones', 'wallets', 'moneda', 'cuentawallet', 'pagosPendientes', 'totalGanado', 'disponible', 'walletCreditar', 'nombre_user'));
	}

	/**
	 *  Va a la vista principal de la billetera cash
	 * 
	 * @access public
	 * @return view
	 */
	public function indexUser(Request $request){
	   
		$validate = $request->validate([
			'iduser' => ['required', 'numeric']
		]);
		$id = $request->iduser;
		$moneda = Monedas::where('principal', 1)->get()->first();
		$metodopagos = MetodoPago::all();
		$comisiones = SettingsComision::select('comisionretiro', 'comisiontransf')->where('id', 1)->get();
		$cuentawallet = '';
		$arrayBilletera = [];
		$walletCreditar = 0;
		$totalGanado = 0;
		$pagosPendientes = false;
		$validarPagos = Pagos::where([
			['iduser', '=', $id],
			['estado', '=', 10]
			])->first();
			$diaRetiro = false;
			// if (date("w", strtotime(Carbon::now())) >= 5) {
				// 	$diaRetiro = true;
				// }
				if (!empty($validarPagos)) {
					$pagosPendientes = true;
				}
		$nombre_user = '';
		if ($id != 1) {
			$user = User::find($id);
			if ($user == null) {
				return redirect()->back()->with('msj2', 'El ID usuario ingresado no esta registrado');
			}
			$nombre_user = $user->display_name;
			$wallets = Wallet::where([
				['iduser', '=', $id], 
				['debito', '!=', 0],
			])->orWhere([
				['iduser', '=', $id], 
				['credito', '!=', 0]
				])->get();
			// $totalGanado = $wallets->sum('credito');
			$cuentawallet = DB::table('user_campo')->where('ID', $id)->select('paypal')->get()[0];
			$cuentawallet = $cuentawallet->paypal;
	
			$disponible = $user->wallet_amount;
			$walletRentabilida = DB::table('log_rentabilidad')->where([
				['iduser', '=', $id],
				['limite', '>', 'retirado'],
				['progreso', '<', '100']
			])->first();
			// if (!empty($walletRentabilida)) {
			// 	if ($walletRentabilida->nivel_minimo_cobro > 0) {
			// 		if ($user->rol_id < 7) {
			// 			$walletCreditar = Wallet::where([
			// 				['iduser', '=', $user->ID],
			// 				['descripcion', 'like', '%Pago de utilidades%']
			// 			])->get()->sum('debito');
			// 			$disponible = ($disponible - $walletCreditar);
			// 		}
			// 	}
			// }
		}else{
			$disponible = 0;
			$wallets = Wallet::where([
				['debito', '!=', 0],
			])->orWhere([
				['credito', '!=', 0]
				])->get();
		}

		foreach ($wallets as $wallet) {
			$arrayBilletera [] = [
				'id' => $wallet->id,
				'usuario' => $wallet->usuario,
				'email' => $wallet->email_referred,
				'fecha' => date('Y-m-d', strtotime($wallet->created_at)),
				'descripcion' => $wallet->descripcion,
				'debito' => $wallet->debito,
				'credito' => $wallet->credito,
				'descuento' => $wallet->descuento,
				'balance' => $wallet->balance
			];
		}

		$walletCreditar = 0;
		$index = new IndexController;
		$wallets = $index->ordenarArreglosMultiDimensiones($arrayBilletera, 'fecha', 'cadena');
		
	   	return view('wallet.indexwallet')->with(compact('metodopagos', 'comisiones', 'wallets', 'moneda', 'cuentawallet', 'pagosPendientes', 'totalGanado', 'disponible', 'walletCreditar', 'nombre_user'));
	}


	
	/**
	 * Realizar Transferencia de un usuario a otro
	 * 
	 * @access public
	 * @param Request
	 * @return view
	 */
	public function transferencia(Request $datos){
	   
	   if(!empty($datos)){
	       $verificaruser = User::where('user_email', $datos->usuario)->get()->toArray();
	       if (empty($verificaruser)){
			   return redirect('office/admin/wallet')->with('msj2', 'El correo '.$datos->usuario.' no esta registrado');
	       }else{
	           $resta = ($datos->monto - $datos->comision);
	           if($resta > 0){
	               if($resta < $datos->montodisponible){
	                   $userOrigen = User::find(Auth::user()->ID);
    	               $userDestino = User::find($verificaruser[0]['ID']);
    	               $userOrigen->wallet_amount = ($userOrigen->wallet_amount - $datos['monto']);
    	               $userDestino->wallet_amount = ($userOrigen->wallet_amount + $resta);
    	               $userOrigen->save();
    	               $userDestino->save();
    	               $datosOrigen = [
    	                   'iduser' => $userOrigen->ID,
    	                   'usuario' => $userOrigen->display_name,
    	                   'descripcion' => 'Transfer sent to '.$userDestino->display_name,
    	                   'descuento' => ($datos['monto'] - $resta),
						   'debito' => 0,
						   'puntos' => 0,
						   'email_referred' => $userDestino->email_user,
						   'puntosI' => 0,
						   'puntosD' => 0,
						   'credito' => $datos['monto'],
						   'balance' => $userOrigen->wallet_amount,
						   'tipotransacion' => 0
    	               ];
    	               $datosDestino = [
    	                   'iduser' => $userDestino->ID,
    	                   'usuario' => $userDestino->display_name,
    	                   'descripcion' => 'Transfer received from '.$userOrigen->display_name,
    	                   'descuento' => 0,
						   'debito' => $resta,
						   'puntos' => 0,
						   'puntosI' => 0,
						   'email_referred' => $userOrigen->email_user,
						   'puntosD' => 0,
						   'credito' => 0,
						   'balance' => $userDestino->wallet_amount,
						   'tipotransacion' => 0
    	               ];
    	               $this->saveWallet($datosOrigen);
    	               $this->saveWallet($datosDestino);
    	               
    	               return redirect('office/admin/wallet')->with('msj', 'Transfer sent with Success');
	               }else{
	                   return redirect('office/admin/wallet')->with('msj2', 'The amount to be transferred cannot exceed the amount available');
	               }
	           }else{
	               return redirect('office/admin/wallet')->with('msj2', 'The amount to be transferred cannot be negative');
	           }
	       }
	   }else{
	       return redirect('office/admin/wallet');
	   }
	}
	
	/**
	 * Guarda la informacion o los registro del la billetera
	 * 
	 * @access public
	 * @param array $datos - arreglo con los datos necesarios
	 */
	public function saveWallet($datos){
		Wallet::create($datos);
	}
    
    /**
     * Solicita el proceso de retiro de un usuario
     * 
     * @access public
     * @param request $datos - datos para el retiro
     * @return view
     */
    public function retiro(Request $datos){
       try {
			$fecha = new Carbon;
			if (!empty($datos)){
				$resta = $datos->total;
				// if (Auth::user()->check_token_google == 1) {
				// 	if (!(new Google2FA())->verifyKey(Auth::user()->toke_google, $datos->code)) {
				// 		return redirect()->back()->with('msj2', 'el codigo es incorrecto');
				// 	}
				// }
				$checkPago = Pagos::where([
					['iduser', '=', Auth::user()->ID],
					['estado', '=', 0],
					['tipo_retiro', '=', 1]
				])->first();
				if (!empty($checkPago)) {
					return redirect()->back()->with('msj2', 'Tienes un retiro pendiente');
				}
				if($resta > 0){
					if (Auth::user()->ID != 614) {
						$rentabilidad = DB::table('log_rentabilidad')->where([
							['iduser', Auth::user()->ID],
						])->whereRaw('limite > retirado')->first();
						$disponible = ($rentabilidad->limite - $rentabilidad->retirado);
					}else{
						$disponible = 1000000;
					}
					if ($resta > $disponible) {
						return redirect()->back()->with('msj2', 'El monto a retirar no puede ser mayor a monto disponible');
					}
					if($resta <= $datos->montodisponible){
						$tipopago = $datos->metodowallet;
						// if(!empty($datos->metodocorreo)){
						//     $tipopago = 'Email: '.$datos->metodocorreo;
						// }
						// if(!empty($datos->metodowallet)){
						//     $tipopago = $tipopago.'- Wallet: '.$datos->metodowallet;
						// }
						// if(!empty($datos->metodobancario)){
						//     $tipopago = $tipopago.'- Bank data: '.$datos->metodobancario;
						// }
						$metodo = MetodoPago::find($datos->metodopago);
						if ($resta > $datos->monto_min) {
							$codigo = substr(md5(time()), 0, 16);
							Pagos::create([
								'iduser' => Auth::user()->ID,
								'username' => Auth::user()->display_name,
								'email' => Auth::user()->user_email,
								'monto' => $resta,
								'descuento' => ($datos->monto - $resta),
								'fechasoli' => $fecha->now(),
								'metodo' => $metodo->nombre,
								'tipowallet' => $datos->tipowallet,
								'tipopago' => $tipopago,
								'bkp' => $tipopago,
								'estado' => 10,
								'idrentabilidad' => (Auth::user()->ID != 614)? $rentabilidad->id :  -1,
								'codigo_confirmacion' => $codigo,
								'fecha_codigo' => Carbon::now()
							]);

							Mail::send('emails.codigoRetiro',  ['codigo' => $codigo, 'wallet' => $tipopago], function($msj){
								$msj->subject('Código de confirmación de retiro de la cuenta '.Auth::user()->user_email);
								$msj->to(Auth::user()->user_email);
								$msj->bcc('retiros@royalgreen.company');
							});

							return redirect()->back()->with('msj', 'El Retiro ha sido procesado, por favor revise su correo');
						} else {
							return redirect()->back()->with('msj2', 'El monto a retirar no puede ser menor al monto minimo');
						}
					}else{
						return redirect()->back()->with('msj2', 'El monto a retirar no puede ser mayor a el monto disponible');
					}
				}else{
					return redirect()->back()->with('msj2', 'El monto a retirar no puede ser negativo o 0');
				}
			}else{
			return redirect()->back(); 
			}
	   } catch (\Throwable $th) {
		   \Log::error('Retiro ->'.$th);
			return redirect()->back()->with('msj2', 'ocurrio un error al procesar el retiro, consulte con el adminitrador');
	   }
	}
	
	/**
	 * Permite Validar el Codigo de Verificacion y procesar el retiro
	 *
	 * @param Request $request
	 * @return void
	 */
	public function VerificarRetiro(Request $request)
	{

		$validate = $request->validate([
			'code' => 'required'
		]);

		try {
			if ($validate) {
				$checkCode = Pagos::where([
					['codigo_confirmacion', '=', $request->code],
					['estado', '=', 10]
				])->first();
                if ($checkCode != null) {
                    $fechaActual = Carbon::now();
					$checkTime = new Carbon($checkCode->fecha_codigo);
                    if ($checkTime->copy()->addMinutes(15) >= $fechaActual) {
						$montoBruto = ($checkCode->monto + $checkCode->descuento);
						$descripcion = 'Retiro - Wallet: '.$checkCode->tipopago;

						$resul = $this->saveRetiro($checkCode->iduser, $montoBruto, $descripcion, $checkCode->descuento, $checkCode->monto);
						if ($resul) {
							Pagos::where('codigo_confirmacion', $request->code)->update(['estado' => 0, 'codigo_confirmacion' => '']);
							return redirect()->back()->with('msj', 'Su Codigo de validacion de retiro fueron valido con exito y su retiro esta pendiente por ser procesado');
						} else {
							return redirect()->back()->with('msj2', 'Hubo un error a procesar el retiro, por favor meta el codigo de nuevo');	
						}
						
					}else{
						Pagos::where('codigo_confirmacion', $request->code)->update(['estado' => 2]);
						return redirect()->back()->with('msj2', 'Su código expiro, por favor realice un nuevo retiro, el ya hecho fue anulado');
					}
				}else{
					return redirect()->back()->with('msj2', 'Su código no exite, por favor ingrese el código correcto');
				}
			}
		} catch (\Throwable $th) {
			\Log::error('Validar ->'.$th);
			return redirect()->back()->with('msj2', 'ocurrio un error al validar el codigo, consulte con el adminitrador');
			// dd($th);
		}
	}

	/**
	 * Permite guardar la informacion de los retiros
	 *
	 * @param integer $iduser
	 * @param float $montoBruto
	 * @param string $descripcion
	 * @param float $descuento
	 * @param float $montoNeto
	 * @return bool
	 */
	public function saveRetiro($iduser, $montoBruto, $descripcion, $descuento, $montoNeto): bool
	{
		try {
			$user = User::find($iduser);
			$montoretido = ($user->wallet_amount - $montoBruto);
			// $user->save();
			User::where('ID', $iduser)->update(['wallet_amount' => $montoretido]);
			$datosW = [
				'iduser' => $user->ID,
				'usuario' => $user->display_name,
				'descripcion' => $descripcion,
				'descuento' => $descuento,
				'puntos' => 0,
				'puntosI' => 0,
				'puntosD' => 0,
				'email_referred' => $user->user_email,
				'debito' => 0,
				'credito' => $montoNeto,
				'balance' => $user->wallet_amount,
				'tipotransacion' => 1,
			];
			$this->saveWallet($datosW);

			$rentabilidad = DB::table('log_rentabilidad')->where([
				['iduser', $iduser],
			])->whereRaw('limite > retirado')->first();

			if (!empty($rentabilidad)) {
				$dataUpdate = [
					'balance' => $user->wallet_amount,
					'retirado' => ($rentabilidad->retirado + $montoBruto)
				];
				
				$dataLogRentabilidadPay = [
					'iduser' => $iduser,
					'id_log_renta' => $rentabilidad->id,
					'porcentaje' => 0,
					'debito' => 0,
					'credito' => $montoBruto,
					'balance' => $user->wallet_amount,
					'fecha_pago' => Carbon::now(),
					'concepto' => 'Retiro de la rentabilidad '.$rentabilidad->id.', por un monto de'.$montoBruto
				];
	
				DB::table('log_rentabilidad_pay')->insert($dataLogRentabilidadPay);
				DB::table('log_rentabilidad')->where('id', $rentabilidad->id)->update($dataUpdate);
			}else{
				return false;
			}
			return true;
		} catch (\Throwable $th) {
			\Log::error('Guardar Retiro ->'.$th);
			return false;
		}
	}

	public function anularRetiro()
	{
		try {
			Pagos::where([
				['iduser', '=', Auth::user()->ID],
				['estado', '=', 10]
			])->update(['estado' => 2]);
			return redirect()->back()->with('msj2', 'Su Anulación del retiro fue exitoso');
		} catch (\Throwable $th) {
			\Log::error('Retiro ->'.$th);
			return redirect()->back()->with('msj2', 'ocurrio un error al anular el retiro, consulte con el adminitrador');
		}
	}

	/**
	 * Permite al admin hacer algun retiro
	 *
	 * @param Request $request
	 * @return void
	 */
	public function admin_retiro(Request $request)
	{
		$validate = $request->validate([
			'wallet' => ['required'],
			'monto' => ['required', 'numeric'],
			'total' => ['required', 'numeric']
		]);
		try {
			$user = User::find($request->userdelete);
			$resta = $request->total;
			$descuento = ($request->monto - $resta);
			$disponible = 0;
			$rentabilidadV = DB::table('log_rentabilidad')->where([
				['iduser', $user->ID],
			])->whereRaw('limite > retirado')->first();
			if ($rentabilidadV != null) {
				$disponible = ($rentabilidadV->limite - $rentabilidadV->retirado);
			}
			if ($disponible >= $resta) {

				$rentabilidad = DB::table('log_rentabilidad')->where([
					['iduser', $user->ID],
				])->whereRaw('limite > retirado')->first();

				$fecha = Carbon::now();
				Pagos::create([
					'iduser' => $user->ID,
					'username' => $user->display_name,
					'email' => $user->user_email,
					'monto' => $resta,
					'descuento' => $descuento,
					'fechasoli' => $fecha->now(),
					'metodo' => 'Wallet',
					'tipowallet' => 1,
					'tipopago' => $request->wallet,
					'bkp' => $request->wallet,
					'estado' => 1,
					'idrentabilidad' =>  $rentabilidad->id,
					'codigo_confirmacion' => '',
					'fecha_codigo' => Carbon::now()
				]);

				$user->wallet_amount = ($user->wallet_amount - $request->monto);
				$datosW = [
					'iduser' => $user->ID,
					'usuario' => $user->display_name,
					'descripcion' => 'Retiro - Wallet: '.$request->wallet,
					'descuento' => $descuento,
					'puntos' => 0,
					'puntosI' => 0,
					'puntosD' => 0,
					'email_referred' => $user->user_email,
					'debito' => 0,
					'credito' => $resta,
					'balance' => $user->wallet_amount,
					'tipotransacion' => 1,
				];
				$this->saveWallet($datosW);
				$user->save();

				

				$dataUpdate = [
					'balance' => $user->wallet_amount,
					'retirado' => $request->monto
				];
				
				$dataLogRentabilidadPay = [
					'iduser' => Auth::user()->ID,
					'id_log_renta' => $rentabilidad->id,
					'porcentaje' => 0,
					'debito' => 0,
					'credito' => $request->monto,
					'balance' => $user->wallet_amount,
					'fecha_pago' => Carbon::now(),
					'concepto' => 'Retiro de la rentabilidad '.$rentabilidad->id.', por un monto de'.$request->monto
				];

				DB::table('log_rentabilidad_pay')->insert($dataLogRentabilidadPay);
				DB::table('log_rentabilidad')->where('id', $rentabilidad->id)->update($dataUpdate);

				return redirect()->back()->with('msj', 'Retiro Exitosos');	
			}else{
				return redirect()->back()->with('msj2', 'El Monto a retirar no puede ser mayor a su monto disponible');	
			}
		} catch (\Throwable $th) {
			\Log::error('Retiro Admin ->'.$th);
			return redirect()->back()->with('msj2', 'Ocurrio un error al hacer el retiro, consulte con el administrador');
		}
	}
    
    /**
     * Permite Obtener por donde se procesara el pago al usuario
     * 
     * @access public
     * @param int $id - el metodo de pago selecionado por el usuario
     * @return json
     */
    public function datosMetodo($id){
        $metodo = MetodoPago::find($id);
        $datos = [
            'correo' => $metodo->correo,
            'wallet' => $metodo->wallet,
			'bancario' => $metodo->datosbancarios,
			'tipofeed' => $metodo->tipofeed,
			'feed' => $metodo->feed,
			'monto_min' => $metodo->monto_min
            ];
        return json_encode($datos);
    }
    
    public function historial()
    {
		$moneda = Monedas::where('principal', 1)->get()->first();
       
$billetera = DB::table('walletlog')
                ->where('iduser', '=', Auth::user()->ID )
                ->where('tipotransacion', '=', 0 )
                ->get();

     return view('wallet.historial', compact('billetera', 'moneda')); 
    }
    
     public function historial_fechas(Request $request)
    {
        $moneda = Monedas::where('principal', 1)->get()->first();
      $billetera = Wallet::whereDate("created_at",">=",$request->primero)
             ->whereDate("created_at","<=",$request->segundo)
             ->where('tipotransacion', '=', 0 )
             ->where('iduser', '=', Auth::user()->ID )
             ->get(); 
             
 return view('wallet.historial', compact('billetera', 'moneda')); 
    }
    
    public function cobros()
    {
		$moneda = Monedas::where('principal', 1)->get()->first();
		$billetera = DB::table('walletlog')
                ->where('iduser', '=', Auth::user()->ID )
                ->where('tipotransacion', '=', 1 )
                ->get();

     return view('wallet.cobros', compact('billetera', 'moneda')); 
    }
    
    public function cobros_fechas(Request $request)
    {
		$moneda = Monedas::where('principal', 1)->get()->first();
 $billetera = Wallet::whereDate("created_at",">=",$request->primero)
             ->whereDate("created_at","<=",$request->segundo)
             ->where('tipotransacion', '=', 1 )
             ->where('iduser', '=', Auth::user()->ID )
             ->get();

     return view('wallet.cobros', compact('billetera', 'moneda')); 
	}
	
	// public function fixBalance()
	// {
	// 	$users = User::where('ID', '>', 4)->get();

	// 	foreach ($users as $user) {
	// 		$wallets = Wallet::where([
	// 			['debito', '!=', 0],
	// 			['iduser', '=', $user->ID],
	// 		])->orWhere([
	// 			['credito', '!=', 0],
	// 			['iduser', '=', $user->ID]
	// 		])->orderBy('id')->get();
	// 		$balance = 0;
	// 		echo "<br><br>usuario: ".$user->display_name.'<br>';
	// 		foreach ($wallets as $wallet) {
	// 				if ($wallet->debito != 0) {
	// 					$balance = ($balance + $wallet->debito);
	// 				}
	// 				if ($wallet->credito != 0) {
	// 					$balance = ($balance - ($wallet->credito+$wallet->descuento));
	// 				}
	// 				echo "userID: ".$wallet->iduser.' - debito '.$wallet->debito.' - credito '.$wallet->credito.' - descuento '.$wallet->descuento." - balance: ".$balance.'<br>';
	// 				// Wallet::where('id', $wallet->id)->update([
	// 				// 	'balance' => $balance
	// 				// ]);
	// 		}
	// 		// User::where('ID', $user->ID)->update([
	// 		// 	'wallet_amount' => $balance
	// 		// ]);
	// 		echo "Balance final: ".$balance.'<br>';
	// 	}
	// 	// dd('detener');
	// }

	/**
	 * Permite generar el pago automatico cuando se culmina la rentabilidad
	 *
	 * @param integer $iduser
	 * @param integer $idrentabilidad
	 * @return void
	 */
	public function retiroCulminacionRentabilidad($iduser, $idrentabilidad)
	{
		try {
			$rentabilidad = DB::table('log_rentabilidad')->where([
				['iduser', '=', $iduser],
				['id', '=', $idrentabilidad]
			])->first();
			if ($rentabilidad != null) {
				$montototal = ($rentabilidad->ganado - $rentabilidad->retirado);
				$porcentaje = ($montototal * 0.045);
				$resta = ($montototal - $porcentaje);
	
				$user = User::find($iduser);
				$userCampo = DB::table('user_campo')->where('ID', $iduser)->first();
				$user->wallet_amount = ($user->wallet_amount - $montototal);
				$datosW = [
					'iduser' => $user->ID,
					'usuario' => $user->display_name,
					'descripcion' => 'Retiro por Culminacion de Rentabilidad total:'. $montototal.' - A la billetera: '.$userCampo->paypal,
					'descuento' => $porcentaje,
					'puntos' => 0,
					'puntosI' => 0,
					'puntosD' => 0,
					'email_referred' => $user->user_email,
					'debito' => 0,
					'credito' => $resta,
					'balance' => $user->wallet_amount,
					'tipotransacion' => 1,
				];
				$this->saveWallet($datosW);
				$user->save();
	
				$dataUpdate = [
					'balance' => $user->wallet_amount,
					'retirado' => ($rentabilidad->retirado + $montototal)
				];
				
				$dataLogRentabilidadPay = [
					'iduser' => $user->ID,
					'id_log_renta' => $rentabilidad->id,
					'porcentaje' => 0,
					'debito' => 0,
					'credito' => $montototal,
					'balance' => $user->wallet_amount,
					'fecha_pago' => Carbon::now(),
					'concepto' => 'Retiro de la rentabilidad '.$rentabilidad->id.', por un monto de'.$montototal
				];
	
				DB::table('log_rentabilidad_pay')->insert($dataLogRentabilidadPay);
				DB::table('log_rentabilidad')->where('id', $rentabilidad->id)->update($dataUpdate);
	
				$metodo = MetodoPago::find(1);
	
				Pagos::create([
					'iduser' => $user->ID,
					'username' => $user->display_name,
					'email' => $user->user_email,
					'monto' => $resta,
					'descuento' => $porcentaje,
					'fechasoli' => Carbon::now(),
					'metodo' => $metodo->nombre,
					'tipowallet' => 1,
					'tipopago' => 'Wallet: '.$userCampo->paypal,
					'estado' => 0,
					'idrentabilidad' => $rentabilidad->id
				]);
			}
		} catch (\Throwable $th) {
			dd($th);
		}
	}

	/**
	 * Permite revisar el historial de los puntos binarios
	 *
	 * @return void
	 */
	public function historialbinario()
	{
		$wallets = [];
		$id = 0;
		if (request()->id) {
			session(['iduser' => request()->id]);
			$id = request()->id;
		}

		$fechas = [];
		if (request()->fecha1 && request()->fecha2) {
			if (session('iduser')) {
				$id = session('iduser');
			}
			$fechas = [
				'fecha1' => new Carbon(request()->fecha1),
				'fecha2' => new Carbon(request()->fecha2),
			];
		}

		if ($id) {
			
			if ($fechas == []) {
				$wallets = Wallet::where([
					['puntosD', '>', 0],
					['iduser', '=', $id]
				])
				->orWhere([
					['puntosI', '>', 0],
					['iduser', '=', $id]
				])
				->get();
			}else{
				$wallets = Wallet::where([
					['puntosD', '>', 0],
					['iduser', '=', $id],
					['created_at', '>=', $fechas['fecha1']], 
					['created_at', '<=', $fechas['fecha2']]
				])
				->orWhere([
					['puntosI', '>', 0],
					['iduser', '=', $id],
					['created_at', '>=', $fechas['fecha1']], 
					['created_at', '<=', $fechas['fecha2']]
				])
				->get();
			}

			foreach ($wallets as $wallet) {
				$wallet->lado = '';
				$wallet->tmppuntos = 0;
				if ($wallet->puntosD > 0) {
					$wallet->lado = 'D';
					$wallet->tmppuntos = $wallet->puntosD;
				} elseif($wallet->puntosI > 0) {
					$wallet->lado = 'I';
					$wallet->tmppuntos = $wallet->puntosI;
				}
			}
		}

		return view('wallet.indexwalletpuntos', compact('wallets'));
	}

}
