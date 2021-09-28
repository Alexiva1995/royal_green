<?php

namespace App\Http\Controllers;

use App\Models\OrdenPurchases;
use App\Models\Wallet;
use App\Models\WalletBinary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\TreeController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\PorcentajeUtilidad;
use App\Models\Inversion;
use App\Models\Liquidaction;

class WalletController extends Controller
{
    //

    public $treeController;

    public function __construct()
    {
        $this->treeController = new TreeController;
    }

    /**
     * Lleva a la vista de la billetera
     *
     * @return void
     */


    public function index()
    {
        try {
            $wallets = Auth::user()->getWallet->where('tipo_transaction', 0)->sortByDesc('id');
            $saldoDisponible = $wallets->where('status', 0)->sum('monto');
            return view('wallet.index', compact('wallets', 'saldoDisponible'));
        } catch (\Throwable $th) {
            Log::error('Wallet - Index -> Error: ' . $th);
            abort(403, "Ocurrio un error, contacte con el administrador");
        }
    }


    /**
     * Lleva a la vista de pagos
     *
     * @return void
     */
    public function payments()
    {
        $payments = Liquidaction::where([['iduser', '=', Auth::user()->id], ['status', '=', '1']])->get();

        return view('wallet.payments', compact('payments'));
    }


    /**
     * Permita general el arreglo que se guardara en la wallet
     *
     * @param integer $iduser
     * @param integer $idreferido
     * @param integer $idorden
     * @param float $monto
     * @param string $concepto
     * @param integer $nivel
     * @param string $name
     * @return void
     */
    private function preSaveWallet(int $iduser, int $idreferido, int $cierre_id = null,  float $monto, string $concepto)
    {
        $data = [
            'iduser' => $iduser,
            'referred_id' => $idreferido,
            'orden_purchases_id' => $cierre_id,
            'monto' => $monto,
            'descripcion' => $concepto,
            'status' => 0,
            'tipo_transaction' => 0,
        ];

        $this->saveWallet($data);
    }

    /**
     * Permite obtener las compras de saldo de los ultimos 5 dias
     *
     * @param integer $iduser
     * @return object
     */
    public function getOrdens($iduser = null): object
    {
        try {
            $fecha = Carbon::now();
            if ($iduser == null) {
                $saldos = OrdenPurchases::where([
                    ['status', '=', '1']
                ])->whereDate('created_at', '>=', $fecha->subDay(5))->get();
            } else {
                $saldos = OrdenPurchases::where([
                    ['iduser', '=', $iduser],
                    ['status', '=', '1']
                ])->whereDate('created_at', '>=', $fecha->subDay(5))->get();
            }
            return $saldos;
        } catch (\Throwable $th) {
            Log::error('Wallet - getOrdes -> Error: ' . $th);
            abort(403, "Ocurrio un error, contacte con el administrador");
        }
    }

    /**
     * Permite guardar la informacion de la wallet
     *
     * @param array $data
     * @return void
     */
    public function saveWallet($data)
    {
        try {
            if ($data['iduser'] != 1) {
                if ($data['tipo_transaction'] == 1) {
                    $wallet = Wallet::create($data);
                    $saldoAcumulado = ($wallet->getWalletUser->wallet - $data['monto']);
                    $wallet->getWalletUser->update(['wallet' => $saldoAcumulado]);
                    $wallet->update(['monto' => -$data['monto']]);
                } else {
                    if ($data['orden_purchases_id'] != null) {
                        $check = Wallet::where([
                            ['iduser', '=', $data['iduser']],
                            ['orden_purchases_id', '=', $data['orden_purchases_id']],
                            ['referred_id', '=', $data['referred_id']]
                        ])->first();
                        if ($check == null) {
                            $wallet = Wallet::create($data);
                            // dd($wallet->getWalletUser);
                            $saldoAcumulado = ($wallet->getWalletUser->wallet + $data['monto']);
                            $wallet->getWalletUser->update(['wallet' => $saldoAcumulado]);
                            $this->aceleracion($data['iduser'], $data['referred_id'], $data['monto'], $data['descripcion']);
                        }
                    } else {
                        $wallet = Wallet::create($data);
                        $saldoAcumulado = ($wallet->getWalletUser->wallet + $data['monto']);
                        $wallet->getWalletUser->update(['wallet' => $saldoAcumulado]);
                        $this->aceleracion($data['iduser'], $data['referred_id'], $data['monto'], $data['descripcion']);
                    }
                    // $wallet->update(['balance' => $saldoAcumulado]);
                }
            }
        } catch (\Throwable $th) {
            Log::error('Wallet - saveWallet -> Error: ' . $th);
            abort(403, "Ocurrio un error, contacte con el administrador");
        }
    }


    public function bonoOchoPorciento($id)
    {
        $orden = OrdenPurchases::findOrFail($id);

        $user = $orden->getOrdenUser;

        $inversion = $user->inversionMasAlta();

        //$comision = collect();

        if (isset($inversion)) {
            /*
            $comision->push([
                'porcentaje' => 0.08,
                'iduser' => $inversion->iduser,
                'comision' => $inversion->invertido,
                'referido' => $inversion->getInversionesUser->fullname,
                'inversion_id' => $inversion->id,
                'orden_id' => $inversion->orden_id,
                'package_id' => $inversion->package_id
            ]);
            */

            $sponsors = $this->treeController->getSponsor($user->id, [], 0, 'ID', 'referred_id');

            if (!empty($sponsors)) {
                foreach ($sponsors as $sponsor) {
                    if ($sponsor->nivel === 1) {
                        $pocentaje = 0.08;
                        $comision = ($inversion->invertido * $pocentaje);
                        $concepto = 'Bono inicio';

                        $this->preSaveWallet($sponsor->id, $user->id, $orden->id, $comision, $concepto, $sponsor->nivel, $sponsor->fullname, $pocentaje);

                        if ($sponsor->inversionMasAlta() != null) {
                            $inver = Inversion::findOrFail($sponsor->inversionMasAlta()->id);
                            $inver->ganacia += $comision;

                            $inver->save();
                        }
                    }
                }
            }
        }
    }
    /**
     * Permite obtener el total disponible en comisiones
     *
     * @param integer $iduser
     * @return float
     */
    public function getTotalComision($iduser): float
    {
        try {
            $wallet = Wallet::where([['iduser', '=', $iduser], ['status', '=', 0]])->get()->sum('monto');
            if ($iduser == 1) {
                $wallet = Wallet::where([['status', '=', 0]])->get()->sum('monto');
            }
            return $wallet;
        } catch (\Throwable $th) {
            Log::error('Wallet - getTotalComision -> Error: ' . $th);
            abort(403, "Ocurrio un error, contacte con el administrador");
        }
    }

    /**
     * Permite obtener el total de comisiones por meses
     *
     * @param integer $iduser
     * @return void
     */
    public function getDataGraphiComisiones($iduser)
    {
        try {
            $totalComision = [];
            if (Auth::user()->admin == 1) {
                $Comisiones = Wallet::select(DB::raw('SUM(monto) as Comision'))
                    ->where([
                        ['status', '<=', 1]
                    ])
                    ->groupBy(DB::raw('YEAR(created_at)'), DB::raw('MONTH(created_at)'))
                    ->orderBy(DB::raw('YEAR(created_at)'), 'ASC')
                    ->orderBy(DB::raw('MONTH(created_at)'), 'ASC')
                    ->take(6)
                    ->get();
            } else {
                $Comisiones = Wallet::select(DB::raw('SUM(monto) as Comision'))
                    ->where([
                        ['iduser', '=',  $iduser],
                        ['status', '<=', 1]
                    ])
                    ->groupBy(DB::raw('YEAR(created_at)'), DB::raw('MONTH(created_at)'))
                    ->orderBy(DB::raw('YEAR(created_at)'), 'ASC')
                    ->orderBy(DB::raw('MONTH(created_at)'), 'ASC')
                    ->take(6)
                    ->get();
            }
            foreach ($Comisiones as $comi) {
                $totalComision[] = $comi->Comision;
            }
            return $totalComision;
        } catch (\Throwable $th) {
            Log::error('Wallet - getDataGraphiComisiones -> Error: ' . $th);
            abort(403, "Ocurrio un error, contacte con el administrador");
        }
    }



    public function pagarUtilidad()
    {
        $inversiones = Inversion::where('status', 1)->whereHas('getInversionesUser', function($user){ 
            $user->where('genera_rentabilidad', 1);
        })->get();
    
        foreach($inversiones as $inversion){
            if($inversion->package_id != 2 && $inversion->rentabilidad != 1){
                //establecemos maxima ganancia
                if($inversion->max_ganancia == null){
                    $inversion->max_ganancia = $inversion->invertido * 2;
                    $inversion->restante = $inversion->max_ganancia;
                }
                $porcentaje = PorcentajeUtilidad::orderBy('id', 'desc')->first();
                $cantidad = $inversion->invertido * $porcentaje->porcentaje_utilidad;
                $resta = $inversion->restante - $cantidad;
                
                if($resta < 0){//comparamos si se pasa de lo que puede ganar
                    $cantidad = $inversion->restante;
                    $inversion->restante = 0;
                    $inversion->ganacia = $inversion->max_ganancia;
                    $inversion->status = 2;
                }else{
                    $inversion->restante = $resta;
                    $inversion->ganacia += $cantidad;
                }
                $data = [
                    'iduser' => $inversion->iduser,
                    'referred_id' => null,
                    'cierre_comision_id' => null,
                    'monto' => $cantidad,
                    'descripcion' => 'Profit de '.($porcentaje->porcentaje_utilidad * 100). ' %',
                    'status' => 0,
                    'tipo_transaction' => 0,
                    'orden_purchases_id' => $inversion->orden_id
                ];

                if($data['monto'] > 0){
                    $wallet = Wallet::create($data);
                    $saldoAcumulado = ($wallet->getWalletUser->wallet - $data['monto']);
                    $wallet->getWalletUser->update(['wallet' => $saldoAcumulado]);
                }
                    
                $inversion->save();
            }
        }
    }

    /**
     * Permite accelarar el proceso de la barra de rentabilidad
     *
     * @param integer $iduser
     * @param integer $idreferido
     * @param float $totalComision
     * @param string $concepto
     * @return void
     */
    public function aceleracion($iduser, $idreferido, $totalComision, $concepto)
    {
        $inversion = Inversion::where([
            ['iduser', '=', $iduser],
            ['status', '=', 1]
        ])->first();
        if ($inversion != null) {
            //establecemos maxima ganancia
            if ($inversion->max_ganancia == null) {
                $inversion->max_ganancia = $inversion->invertido * 2;
                $inversion->restante = $inversion->max_ganancia;
            }
            $porcentaje = PorcentajeUtilidad::orderBy('id', 'desc')->first();
            $cantidad = $totalComision;
            $resta = $inversion->restante - $cantidad;

            if ($resta < 0) { //comparamos si se pasa de lo que puede ganar
                $cantidad = $inversion->restante;
                $inversion->restante = 0;
                $inversion->ganacia = $inversion->max_ganancia;
                $inversion->status = 2;
            } else {
                $inversion->restante = $resta;
                $inversion->ganacia += $cantidad;
            }
            // $data = [
            //     'iduser' => $inversion->iduser,
            //     'referred_id' => $idreferido,
            //     'cierre_comision_id' => null,
            //     'monto' => $cantidad,
            //     'descripcion' => 'Profit -> '.$concepto,
            //     'status' => 0,
            //     'tipo_transaction' => 0,
            //     'orden_purchases_id' => $inversion->orden_id
            // ];

            // if($data['monto'] > 0){
            //     $wallet = Wallet::create($data);
            //     // $saldoAcumulado = ($wallet->getWalletUser->wallet - $data['monto']);
            //     // $wallet->getWalletUser->update(['wallet' => $saldoAcumulado]);
            // }

            $inversion->save();
        }
    }

    /**
     * Permite pagar el bono directo
     *
     * @return void
     */
    public function bonos($user, $orden)
    {
        try {
            $comision = ($orden->total * 0.1);
            $sponsor = User::find($user->referred_id);
            // dd($user->inversionMasAlta()->invertido);
            if ($sponsor->status == '1') {
                $concepto = 'Bono Directo  - N° ' . $orden->id . ' - ' . $orden->getOrdenUser->fullname;
                $this->preSaveWallet($sponsor->id, $orden->iduser, $orden->id, $comision, $concepto);
                Log::info('Bono Directo Pagado');
                // dd("Usuario " . $orden->iduser, "Referido " . $sponsor->id, "Id de Orden " . $orden->id, "Comision " . $comision, "Concepto " . $concepto);

            }

            //******PAGO DEL BONO INDIRECTO NIVEL 2 *********//
            if (isset($sponsor->referred_id) && $sponsor->referred_id != 0) {
                $nivel2 = User::find($sponsor->referred_id);
                if (isset($nivel2->inversionMasAlta()->invertido)) {
                    $paqueteReferido = $nivel2->inversionMasAlta()->invertido;
                } else {
                    $paqueteReferido = 0;
                }
                $comision = ($orden->total * 0.03);
                if ($nivel2->status == '1' && $paqueteReferido >= 1000) {
                    // dd("Usuario " . $orden->iduser, "Referido " . $nivel2->id, "Id de Orden " . $orden->id, "Comision " . $comision, "Concepto " . $concepto);
                    $concepto = 'Bono Indirecto - N° ' . $orden->id . ' - ' . $orden->getOrdenUser->fullname;
                    $this->preSaveWallet($nivel2->id, $orden->iduser, $orden->id, $comision, $concepto);
                    Log::info('Bono Indirecto Pagado');
                }
            }

            //******PAGO DEL BONO INDIRECTO NIVEL 3 *********//
            if (isset($nivel2->referred_id) && $nivel2->referred_id != 0) {
                $nivel2 = User::find($sponsor->referred_id);
                $nivel3 = User::find($nivel2->referred_id);
                if (isset($nivel3->inversionMasAlta()->invertido)) {
                    $paqueteReferido = $nivel3->inversionMasAlta()->invertido;
                } else {
                    $paqueteReferido = 0;
                }

                // dd($paqueteReferido);
                $comision = ($orden->total * 0.02);
                if ($nivel3->status == '1' && $paqueteReferido >= 5000) {
                    // dd("Usuario" . $orden->iduser, "Referido " . $nivel3->id, "Id de Orden " . $orden->id, "Comision " . $comision, "Concepto " . $concepto);
                    $concepto = 'Bono Indirecto - N° ' . $orden->id . ' - ' . $orden->getOrdenUser->fullname;
                    $this->preSaveWallet($nivel3->id, $orden->iduser, $orden->id, $comision, $concepto);
                    Log::info('Bono Indirecto Pagado');
                }
            }
        } catch (\Throwable $th) {
            Log::error('Wallet - bonos -> Error: ' . $th);
            abort(403, "Ocurrio un error, contacte con el administrador");
        }
    }


    /**
     * Permite pagar los puntos binarios
     *
     * @return void
     */
    public function payPointsBinary($id = null)
    {
        try {
            if($id == null){
                $ordenes = $this->getOrdens(null);
                foreach ($ordenes as $orden) {
                    $sponsors = $this->treeController->getSponsor($orden->iduser, [], 0, 'id', 'binary_id');
                    $side = $orden->getOrdenUser->binary_side;
                    foreach ($sponsors as $sponsor) {
                        if ($sponsor->id != $orden->iduser) {
                        if ($sponsor->id != 1) {

                                    $check = WalletBinary::where([
                                        ['iduser', '=', $sponsor->id],
                                        ['referred_id', '=', $orden->iduser],
                                        ['orden_purchase_id', '=', $orden->id]
                                    ])->first();
                                    if (empty($check)) {
                                        $concepto = 'Puntos binarios del Usuario '.$orden->getOrdenUser->fullname;
                                        $puntosD = $puntosI = 0;
                                        if ($sponsor->status == '1') {
                                            if ($side == 'I') {
                                                $puntosI = $orden->total;
                                                $puntos_reales = $puntosI;
                                            }elseif($side == 'D'){
                                                $puntosD = $orden->total;
                                                $puntos_reales = $puntosD;
                                            }
                                        }
                                        $dataWalletPoints = [
                                            'iduser' => $sponsor->id,
                                            'referred_id' => $orden->iduser,
                                            'orden_purchase_id' => $orden->id,
                                            'puntos_i' => $puntosI,
                                            'puntos_d' => $puntosD,
                                            'puntos_reales' => $puntos_reales,
                                            'side' => $side,
                                            'status' => 0,
                                            'descripcion' => $concepto
                                        ];
                                        
                                        WalletBinary::create($dataWalletPoints);
                                }
                        }                    
                        }
                        $side = $sponsor->binary_side;
                    }
                }
            }else{
                $orden = OrdenPurchases::findOrFail($id);

                $sponsors = $this->treeController->getSponsor($orden->iduser, [], 0, 'id', 'binary_id');
                $side = $orden->getOrdenUser->binary_side;
                foreach ($sponsors as $sponsor) {
                    if ($sponsor->id != $orden->iduser) {
                       if ($sponsor->id != 1) {

                                $check = WalletBinary::where([
                                    ['iduser', '=', $sponsor->id],
                                    ['referred_id', '=', $orden->iduser],
                                    ['orden_purchase_id', '=', $orden->id]
                                ])->first();
                                if (empty($check)) {
                                    $concepto = 'Puntos binarios del Usuario '.$orden->getOrdenUser->fullname;
                                    $puntosD = $puntosI = 0;
                                    if ($sponsor->status == '1') {
                                        if ($side == 'I') {
                                            $puntosI = $orden->total;
                                            $puntosReales = $puntosI;
                                        }elseif($side == 'D'){
                                            $puntosD = $orden->total;
                                            $puntosReales = $puntosD;
                                        }
                                    }
                                    $dataWalletPoints = [
                                        'iduser' => $sponsor->id,
                                        'referred_id' => $orden->iduser,
                                        'orden_purchase_id' => $orden->id,
                                        'puntos_d' => $puntosD,
                                        'puntos_reales' => $puntosReales,
                                        'puntos_i' => $puntosI,
                                        'side' => $side,
                                        'status' => 0,
                                        'descripcion' => $concepto
                                    ];
                                    
                                    WalletBinary::create($dataWalletPoints);
                            }
                        }
                    }
                    $side = $sponsor->binary_side;
                }
            }
        } catch (\Throwable $th) {
            Log::error('Wallet - payPointsBinary -> Error: ' . $th);
            abort(403, "Ocurrio un error, contacte con el administrador");
        }
    }

    /**
     * Permite pagar el bono binario
     *
     * @return void
     */
    public function bonoBinario()
    {
        $binarios = WalletBinary::where([
            ['status', '=', 0],
            ['puntos_d', '>', 0],
        ])->orWhere([
            ['status', '=', 0],
            ['puntos_i', '>', 0],
        ])->selectRaw('iduser, SUM(puntos_d) as totald, SUM(puntos_i) as totali')->groupBy('iduser')->get();
        // dd(($binarios));
        foreach ($binarios as $binario) {
            $puntos = 0;
            $side_mayor = $side_menor = '';
            if ($binario->totald >= $binario->totali) {
                $puntos = $binario->totali;
                $side_mayor = 'D';
                $side_menor = 'I';
            } else {
                $puntos = $binario->totald;
                $side_mayor = 'I';
                $side_menor = 'D';
            }

            if ($puntos > 0) {

                $sponsor = User::find($binario->iduser);
                if ($sponsor->inversionMasAlta() != null) {
                    $paquete = $sponsor->inversionMasAlta()->getPackageOrden;
                    if ($paquete->price < 1000) {
                        $comision = ($puntos * 0.08);
                    } elseif ($paquete->price >= 1000 && $paquete->price < 5000) {
                        $comision = ($puntos * 0.09);
                    } elseif ($paquete->price >= 5000 && $paquete->price < 25000) {
                        $comision = ($puntos * 0.10);
                    } elseif ($paquete->price >= 25000 && $paquete->price < 50000) {
                        $comision = ($puntos * 0.11);
                    } elseif ($paquete->price >= 50000) {
                        $comision = ($puntos * 0.12);
                    }

                    $sponsor->point_rank += $puntos;
                    $concepto = 'Bono Binario - ' . $puntos;
                    $idcomision = $binario->iduser . Carbon::now()->format('Ymd');
                    $this->setPointBinaryPaid($puntos, $side_menor, $binario->iduser, $side_mayor);
                    $this->preSaveWallet($sponsor->id, $sponsor->id, null, $comision, $concepto);
                    $sponsor->save();
                }
            }
        }
    }

    /**
     * Permite descontar los puntos ya pagados
     *
     * @param float $pagar
     * @param string $ladomenor
     * @param integer $iduser
     * @param string $ladomayor
     * @return void
     */
    private function setPointBinaryPaid(float $pagar, string $ladomenor, int $iduser, string $ladomayor)
    {
        //LADO MAYOR
        $binarios = WalletBinary::where([
            ['side', '=', $ladomayor],
            ['iduser', '=', $iduser],
            ['status', '=', 0]
        ])->orderBy('id', 'asc')->get();
        $field_side = ($ladomayor == 'D') ? 'puntos_d' : 'puntos_i';
        $this->foreachSetPoint($binarios,  $pagar, $field_side);

        //LADO MENOR
        $binarios = WalletBinary::where([
            ['side', '=', $ladomenor],
            ['iduser', '=', $iduser],
            ['status', '=', 0]
        ])->orderBy('id', 'asc')->get();
        $field_side = ($ladomenor == 'I') ? 'puntos_i' : 'puntos_d';
        $this->foreachSetPoint($binarios,  $pagar, $field_side);
    }

    /**
     * Bucle para descontar los puntos
     *
     * @param collection $binarios
     * @param integer $pagar
     * @param string $field_side
     * @return void
     */
    public function foreachSetPoint($binarios, $pagar, $field_side)
    {
        $lisComision = [];
        $pagar_copy = $pagar;
        foreach ($binarios as $binario) {
            $wallet = WalletBinary::findOrFail($binario->id);
            if ($pagar_copy > 0) {
                if ($pagar_copy <= $binario->$field_side) {
                    $adecontar = $pagar_copy;
                } else {
                    $adecontar = $binario->$field_side;
                }
                $pagar_copy -=  $adecontar;
                $wallet->$field_side -= $adecontar;
                if ($wallet->$field_side == 0) {
                    $lisComision[] = $binario->id;
                }
                $wallet->save();
            } else {
                break;
            }
        }
        WalletBinary::whereIn('id', $lisComision)->update(['status' => '1']);
    }

    /**
     * Permite pagar todo los bonos y puntos 
     *
     * @return void
     */
    public function payAll()
    {
        $this->payPointsBinary();
        Log::info('Puntos Binarios Pagado');
        if (env('APP_ENV' != 'local')) {
            $this->bonoBinario();
        }
    }


    public function logWallet()
    {
        try {
            return view('logs.wallet');
        } catch (\Throwable $th) {
            Log::error('Wallet - logWallet -> Error: ' . $th);
            abort(403, "Ocurrio un error, contacte con el administrador");
        }
    }

    public function logNetwork()
    {
        try {
            return view('logs.network');
        } catch (\Throwable $th) {
            Log::error('Wallet - logNetwork -> Error: ' . $th);
            abort(403, "Ocurrio un error, contacte con el administrador");
        }
    }

    public function logHistoryPoints()
    {
        try {
            return view('logs.PointsHistory');
        } catch (\Throwable $th) {
            Log::error('Wallet - logHistoryPoints -> Error: ' . $th);
            abort(403, "Ocurrio un error, contacte con el administrador");
        }
    }

    public function adminWallet()
    {
        return view('wallet.adminWallet');
    }

    public function adminWallets(Request $request)
    {
        $user = User::find($request->iduser);

        if (empty($user)) {

            return redirect()->back()->with('msj-danger', 'Este usuario no existe');
        } else {

            $wallets = $user->getWallet->where('tipo_transaction', 0)->sortByDesc('id');

            $saldoDisponible = $wallets->where('status', 0)->sum('monto');

            return view('wallet.index', compact('wallets', 'saldoDisponible'));
        }
    }
}
