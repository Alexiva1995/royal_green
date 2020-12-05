<?php

namespace App\Http\Controllers;

use App\MetodoPago;
use App\Pagos;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RentabilidadController extends Controller
{

    public function __construct()
    {
        // TITLE
		view()->share('title', 'Wallet Rentabilidad');
    }

    public function index()
    {
        $rentabilidads = DB::table('log_rentabilidad')->where('iduser', Auth::user()->ID)->get();
        $metodopagos = MetodoPago::all();

        foreach ($rentabilidads as $rentabilidad) {
            $rentabilidad->producto = json_decode($rentabilidad->detalles_producto);
        }

        $cuentawallet = DB::table('user_campo')->where('ID', Auth::user()->ID)->select('paypal')->get()[0];
		$cuentawallet = $cuentawallet->paypal;

        return view('rentabilidad.index', compact('rentabilidads', 'metodopagos', 'cuentawallet'));
    }

    public function retiro(Request $datos){
       try {
        $fecha = new Carbon;
        if (!empty($datos)){
			$resta = $datos->total;
			$checkPago = Pagos::where([
				['iduser', '=', Auth::user()->ID],
				['estado', '=', 0],
				['tipo_retiro', '=', 2]
			])->first();
			if (!empty($checkPago)) {
				return redirect()->back()->with('msj2', 'Tienes un retiro pendiente');
			}
            if($resta > 0){
                if($resta <= $datos->montodisponible){
                    $tipopago = '';
                    if(!empty($datos->metodocorreo)){
                        $tipopago = 'Email: '.$datos->metodocorreo;
                    }
                    if(!empty($datos->metodowallet)){
                        $tipopago = $tipopago.'- Wallet: '.$datos->metodowallet;
                    }
                    if(!empty($datos->metodobancario)){
                        $tipopago = $tipopago.'- Bank data: '.$datos->metodobancario;
                    }
                    $metodo = MetodoPago::find($datos->metodopago);
                    if ($resta > $datos->monto_min) {
						// DB::table('user_campo')->where('ID', Auth::user()->ID)->update(['paypal' => $datos->metodowallet]);
						$rentabilidad = DB::table('log_rentabilidad')->where('id', $datos->idrentabilidad)->first();

                        $ganado = $rentabilidad->ganado;
                        $balance = ($ganado - $datos->monto);

                        $dataUpdate = [
                            'balance' => $balance,
                            'retirado' => $datos->monto
                        ];
                        
                        $dataLogRentabilidadPay = [
                            'iduser' => Auth::user()->ID,
                            'id_log_renta' => $datos->idrentabilidad,
                            'porcentaje' => 0,
                            'debito' => 0,
                            'credito' => $datos->monto,
                            'balance' => $balance,
                            'fecha_pago' => Carbon::now(),
                            'concepto' => 'Retiro de la rentabilidad '.$datos->idrentabilidad.', por un monto de'.$datos->monto
                        ];

                        DB::table('log_rentabilidad_pay')->insert($dataLogRentabilidadPay);
                        DB::table('log_rentabilidad')->where('id', $rentabilidad->id)->update($dataUpdate);
						
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
                            'estado' => 0,
                            'tipo_retiro' => 2,
                            'idrentabilidad' => $datos->idrentabilidad
						]);
						return redirect()->back()->with('msj', 'El Retiro ha sido procesado');
					} else {
						return redirect()->back()->with('msj2', 'El monto a retirar no puede ser menor la monto minimo');	
					}
                }else{
                    return redirect()->back()->with('msj2', 'El monto a retirar no puede ser mayor a monto disponible');
                }
            }else{
                return redirect()->back()->with('msj2', 'El monto a retirar no puede ser negativo o 0');
			}
        }else{
           return redirect()->back(); 
        }
       } catch (\Throwable $th) {
            return redirect()->back()->with('msj2', 'Ocurrio un error al momento de retirar, por favor comunicarse con el administrado');
       }
    }
}
