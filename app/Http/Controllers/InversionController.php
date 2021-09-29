<?php

namespace App\Http\Controllers;

use App\Models\Inversion;
use App\Models\OrdenPurchases;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\PorcentajeUtilidad;
use App\Models\User;
use App\Models\Packages;
use App\Http\Controllers\WalletController;

class InversionController extends Controller
{
    /**
     * Lleva a a la vista de las inversiones
     *
     * @param [type] $tipo
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('kyc')->only('index');
        $this->WalletController = new WalletController();
    }

    public function index()
    {
        try {
            $this->checkStatus();

            if (Auth::user()->admin == 1) {
                $inversiones = Inversion::all();
            } else {
                $inversiones = Inversion::where('iduser', '=', Auth::id())->orderBy('status')->get();
            }

            foreach ($inversiones as $invers) {
                $invers->correo = $invers->getInversionesUser->email;
            }

            return view('inversiones.index', compact('inversiones'));
        } catch (\Throwable $th) {
            Log::error('InversionController - index -> Error: ' . $th);
            abort(403, "Ocurrio un error, contacte con el administrador");
        }
    }

    public function activacion()
    {
        $user = User::whereDoesntHave('getUserInversiones', function ($inversion) {
            $inversion->where('status', '=', 1);
        })->get();

        $paquetes = Packages::all();

        return view('inversiones.activacion', compact('user', 'paquetes'));
    }


    public function activaciones(request $request)
    {
        // try {
        //CREAMOS LA ORDEN

        $paquete = Packages::find($request->paquete);

        $user = User::findOrFail($request->id);
        
        $inv = $user->inversionMasAlta();


        if (isset($inv->invertido)) {

            $inversion = $inv;
            $pagado = $inversion->invertido;

            $nuevoInvertido = ($paquete->price - $pagado);
            // $porcentaje = ($nuevoInvertido * 0.03);
            $porcentaje = 0;

            $total = ($nuevoInvertido + $porcentaje);
            //ACTUALIZAMOS LA INVERSION

            $data = [
                'iduser' => $request->id,
                'package_id' => $paquete->id,
                'cantidad' => 1,
                'total' => $total,
                'monto' => $nuevoInvertido,
            ];

            $orden = OrdenPurchases::create($data);
        } else {
            //$porcentaje = ($paquete->price * 0.03);
            $porcentaje = 0;
            // dd('aqui estamos');

            $total = ($paquete->price + $porcentaje);

            if (isset($request->comision)) {
                $rentabilidad = '0';
            }else{
                $rentabilidad = '1';
            }
            if (isset($request->comision)) {
                $comisiones = '0';
            }else{
                $comisiones = '1';
            }


            $data = [
                'iduser' => $request->id,
                'package_id' => $paquete->id,
                'cantidad' => 1,
                'total' => $total,
                'monto' => $paquete->price,
                'status' => '1',
                'manual' => '0',
                'comisiones' => $comisiones,
                'rentabilidad' => $rentabilidad,
            ];

            $orden = OrdenPurchases::create($data);
        }
        
        ////////////////////////////////////
        //LE colocamos los puntos
        //dump('sin puntos');
        if (isset($request->comision)) {
            //dump('con puntos');
            $this->WalletController->payPointsBinary($orden->id);
        }
    
        if (isset($user->inversionMasAlta()->invertido)) {

            $inversion = $user->inversionMasAlta();
            $pagado = $inversion->invertido;

            $nuevoInvertido = ($orden->getPackageOrden->price - $pagado);
            $porcentaje = ($nuevoInvertido * 0.03);

            $total = ($nuevoInvertido + $porcentaje);
            //ACTUALIZAMOS LA INVERSION
            $inversion->invertido += $nuevoInvertido;

            $inversion->limite = $inversion->invertido * 2;

            $inversion->package_id = $orden->package_id;
            $inversion->save();
            $inversion = $inversion->id;
            
        } else {

            $inversion = $this->saveInversion($paquete->id, $orden->monto, $paquete->expired, $user->id);
            // $inversion = $this->saveInversion($paquete->id, $orden->id, $orden->monto, $paquete->expired, $user->id);


            if (isset($request->comision)) {

                $this->WalletController->bonos($user,$orden);
            }
        }

        $orden->inversion_id = $inversion;
        $orden->save();
        
        $user->status = '1';
       
        if (!isset($request->rentabilidad)) {
            
            $user->genera_rentabilidad = 0;
        }
        $user->save();

        // } catch (\Throwable $th) {
        //     Log::error('Inversion - ActivacionManual -> Error: '.$th);
        //     abort(403, "Ocurrio un error, contacte con el administrador");
        // }

        return back()->with('msj-success', 'Orden actualizada exitosamente');
    }


    // public function index($tipo)
    // {
    //    try {
    //        $this->checkStatus();
    //         if ($tipo == '') {
    //             $inversiones = Inversion::all();
    //         } else {
    //             if (Auth::id() == 1) {
    //                 $inversiones = Inversion::where('status', '=', $tipo)->get();
    //             }else{
    //                 $inversiones = Inversion::where([['status', '=', $tipo], ['iduser', '=',Auth::id()]])->get();
    //             }
    //         }

    //         foreach ($inversiones as $inversion) {
    //             $inversion->correo = $inversion->getInversionesUser->email;
    //         }

    //         return view('inversiones.index', compact('inversiones'));
    //     } catch (\Throwable $th) {
    //         Log::error('InversionController - index -> Error: '.$th);
    //         abort(403, "Ocurrio un error, contacte con el administrador");
    //     }
    // }

    /**
     * Permite guardar las nuevas inversiones generadas
     *
     * @param integer $paquete - ID del Paquete Comprado
     * @param float $invertido - Monto Total Invertido
     * @param string $vencimiento - Fecha de Vencimiento del paquete
     * @param integer $iduser - ID del usuario 
     * @return void
     */
    public function saveInversion(int $paquete, float $invertido, $vencimiento, int $iduser)
    {
        try {
            $check = Inversion::where([
                ['iduser', '=', $iduser],
                ['package_id', '=', $paquete],
                //['orden_id', '=', $orden],
            ])->first();

            if ($check == null) {
                $data = [
                    'iduser' => $iduser,
                    'package_id' => $paquete,
                    //'orden_id' => $orden,
                    'invertido' => $invertido,
                    'ganacia' => 0,
                    'retiro' => 0,
                    'capital' => $invertido,
                    'progreso' => 0,
                    'fecha_vencimiento' => $vencimiento,
                    'ganancia_acumulada' => 0,
                ];

                //PARA QUE LOS PAQUETES DE 100 A PARTIR DE AHORA NO GENERE RENTABILIDAD
                if($paquete == 2){
                    $data['rentabilidad'] = 1;
                }

                $inversion = Inversion::create($data);
                return $inversion->id;
            }
        } catch (\Throwable $th) {
            Log::error('InversionController - saveInversion -> Error: ' . $th);
            abort(403, "Ocurrio un error, contacte con el administrador");
        }
    }

    /**
     * Permite Verificar si una inversion esta terminada
     *
     * @return void
     */
    public function checkStatus()
    {
        Inversion::whereDate('fecha_vencimiento', '<', Carbon::now())->update(['status' => 2]);
    }

    public function updateGanancia(int $iduser, $paquete, float $ganacia, int $ordenId = 0, $porcentaje = null)
    {
        try {
            if ($ordenId != 0) {
                $inversion = Inversion::where([
                    ['iduser', '=', $iduser],
                    ['status', '=', 1],
                    ['orden_id', '=', $ordenId]
                ])->first();
            } else {
                $inversion = Inversion::where([
                    ['iduser', '=', $iduser],
                    ['status', '=', 1]
                ])->first();
            }

            if ($inversion != null) {

                $capital = ($inversion->capital + $ganacia);
                $inversion->ganacia = ($inversion->ganacia + $ganacia);
                $inversion->capital = $capital;
                $inversion->porcentaje_fondo = $porcentaje;

                $inversion->save();
            }
        } catch (\Throwable $th) {
            Log::error('InversionController - updateGanancia -> Error: ' . $th);
            abort(403, "Ocurrio un error, contacte con el administrador");
        }
    }

    public function updatePorcentaje(int $iduser, int $paquete, float $ganacia, int $ordenId = 0, $porcentaje = null)
    {
        try {
            if ($ordenId != 0) {
                $inversion = Inversion::where([
                    ['iduser', '=', $iduser],
                    ['status', '=', 1],
                    ['orden_id', '=', $ordenId]
                ])->first();
            } else {
                $inversion = Inversion::where([
                    ['iduser', '=', $iduser],
                    ['package_id', '=', $paquete],
                    ['status', '=', 1]
                ])->first();
            }

            if ($inversion != null) {

                $inversion->porcentaje_fondo = $porcentaje;

                $inversion->save();
            }
        } catch (\Throwable $th) {
            Log::error('InversionController - updateGanancia -> Error: ' . $th);
            abort(403, "Ocurrio un error, contacte con el administrador");
        }
    }

    public function updatePorcentajeGanancia(Request $request)
    {
        $porcentaje = $request->porcentaje_ganancia / 100;
        
        PorcentajeUtilidad::create(['porcentaje_utilidad' => $porcentaje]);
    
        $this->WalletController->pagarUtilidad();

        return redirect()->back()->with('msj-success', 'Porcentaje actualizado correctamente');
    }
}
