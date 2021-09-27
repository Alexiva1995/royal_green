<?php

namespace App\Http\Controllers;

use App\Models\Liquidaction;
use App\Models\User;
use App\Models\Wallet;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use App\Http\Controllers\WalletController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class LiquidactionController extends Controller
{

    public $walletController;

    function __construct()
    {
        $this->walletController = new WalletController();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $comisiones = $this->getTotalComisiones([], null);
            return view('settlement.index', compact('comisiones'));
        } catch (\Throwable $th) {
            Log::error('Liquidaction - index -> Error: '.$th);
            abort(403, "Ocurrio un error, contacte con el administrador");
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexPendientes()
    {
        try {
            $liquidaciones = Liquidaction::where('status', 0)->get();
            foreach ($liquidaciones as $liqui) {
                $liqui->fullname = $liqui->getUserLiquidation->fullname;
            }
            return view('settlement.pending', compact('liquidaciones'));
        } catch (\Throwable $th) {
            Log::error('Liquidaction - indexPendientes -> Error: '.$th);
            abort(403, "Ocurrio un error, contacte con el administrador");
        }
    }

    /**
     * LLeva a la vistas de las liquidaciones reservadas o aprobadas
     *
     * @param string $status
     * @return void
     */
    public function indexHistory()
    {
        try {

           $liquidaciones = Liquidaction::all();
            foreach ($liquidaciones as $liqui) {
                $liqui->fullname = $liqui->getUserLiquidation->fullname;
            }
            return view('settlement.history', compact('liquidaciones'));

        } catch (\Throwable $th) {
            Log::error('Liquidaction - indexHistory -> Error: '.$th);
            abort(403, "Ocurrio un error, contacte con el administrador");
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if ($request->tipo == 'detallada') {
            $validate = $request->validate([
                'listComisiones' => ['required', 'array'],
                'iduser' => ['required']
            ]);
        }else{
            $validate = $request->validate([
                'listUsers' => ['required', 'array']
            ]);
        }

        try {
            if ($validate) {
                $mensaje = 'Liquidaciones Generada Exitoxamente';
                $tipo = 'msj-success';
                $msj = 0;
                if ($request->tipo == 'detallada') {
                    $msj = $this->generarLiquidation($request->iduser, $request->listComisiones);
                }else{
                    foreach ($request->listUsers as $iduser) {
                        $msj = $this->generarLiquidation($iduser, []);
                    }
                }
                if ($msj == 0) {
                    $mensaje = 'El monto a retirar esta por debajo del limite permitido que es 50$';
                    $tipo = 'msj-warning';
                }
                return redirect()->back()->with($tipo, $mensaje);
            }
        } catch (\Throwable $th) {
            Log::error('Liquidaction - store -> Error: '.$th);
            abort(403, "Ocurrio un error, contacte con el administrador");
        }
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $comiciones = Wallet::where([
                ['status', '=', 0],
                ['liquidation_id', '=', null],
                ['tipo_transaction', '=', 0],
                ['iduser', '=', $id]
            ])->get();
    
            foreach ($comiciones as $comi) {
                $fecha = new Carbon($comi->created_at);
                $comi->fecha = $fecha->format('Y-m-d');
                $referido = User::find($comi->referred_id);
                $comi->referido = ($referido != null) ? $referido->only('fullname') : 'Usuario no Disponible';
            }
            
            $user = User::find($id);
    
            $detalles = [
                'iduser' => $id,
                'fullname' => $user->fullname,
                'comisiones' => $comiciones,
                'total' => number_format($comiciones->sum('monto'), 2, ',', '.')
            ];
    
            return json_encode($detalles);  
        } catch (\Throwable $th) {
            Log::error('Liquidaction - show -> Error: '.$th);
            abort(403, "Ocurrio un error, contacte con el administrador");
        }   
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Liquidaction  $liquidaction
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
       try {
            $comiciones = Wallet::where([
                ['liquidation_id', '=', $id],
            ])->get();

            foreach ($comiciones as $comi) {
                $fecha = new Carbon($comi->created_at);
                $comi->fecha = $fecha->format('Y-m-d'); 
                $referido = User::find($comi->referred_id);
                $comi->referido = ($referido != null) ? $referido->only('fullname') : 'Usuario no Disponible';
            }
            
            $user = User::find($comiciones->pluck('iduser')[0]);

            $detalles = [
                'idliquidaction' => $id,
                'iduser' => $user->id,
                'fullname' => $user->fullname,
                'comisiones' => $comiciones,
                'total' => number_format($comiciones->sum('monto'), 2, ',', '.')
            ];

            return json_encode($detalles);
        } catch (\Throwable $th) {
            Log::error('Liquidaction - edit -> Error: '.$th);
            abort(403, "Ocurrio un error, contacte con el administrador");
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Liquidaction  $liquidaction
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Liquidaction $liquidaction)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Liquidaction  $liquidaction
     * @return \Illuminate\Http\Response
     */
    public function destroy(Liquidaction $liquidaction)
    {
        //
    }

    /**
     * Permite Obtener la informacion de las comisiones y el total disponible
     *
     * @param array $filtros - filtro para mejorar la vistas
     * @param integer $iduser - si es para un usuario especifico
     * @return array
     */
    public function getTotalComisiones(array $filtros, int $iduser = null): array
    {
        try {
            $comisiones = [];
            if ($iduser != null && $iduser != 1) {
                $comisionestmp = Wallet::where([
                    ['status', '=', 0],
                    ['liquidation_id', '=', null],
                    ['tipo_transaction', '=', 0],
                    ['iduser', '=', $iduser]
                ])->select(
                    DB::raw('sum(monto) as total'), 'iduser'
                )->groupBy('iduser')->get();
            }else{
                $comisionestmp = Wallet::where([
                    ['status', '=', 0],
                    ['liquidation_id', '=', null],
                    ['tipo_transaction', '=', 0],
                ])->select(
                    DB::raw('sum(monto) as total'), 'iduser'
                )->groupBy('iduser')->get();
            }

            foreach ($comisionestmp as $comision) {
                $comision->getWalletUser;
                if ($comision->getWalletUser != null) {
                    if ($filtros == []) {
                        $comisiones[] = $comision;
                    }else{
                        if (!empty($filtros['activo'])) {
                            if ($comision->status == 1) {
                                if (!empty($filtros['mayorque'])) {
                                    if ($comision->total >= $filtros['mayorque']) {
                                        $comisiones[] = $comision;
                                    }
                                } else {
                                    $comisiones[] = $comision;
                                }
                            }
                        }else{
                            if (!empty($filtros['mayorque'])) {
                                if ($comision->total >= $filtros['mayorque']) {
                                    $comisiones[] = $comision;
                                }
                            } else {
                                $comisiones[] = $comision;
                            }
                        }
                    }
                }
            }
            return $comisiones;
        } catch (\Throwable $th) {
            Log::error('Liquidaction - getTotalComisiones -> Error: '.$th);
            abort(403, "Ocurrio un error, contacte con el administrador");
        }
    }

    /**
     * Permite procesar las liquidaciones
     *
     * @param integer $iduser -  id del usuario
     * @param array $listComision - comisiones a procesar si son selecionada
     * @return integer
     */
    public function generarLiquidation(int $iduser, array $listComision): int
    {
        try {
            $user = User::find($iduser);
            $comisiones = collect();

            if ($listComision == []) {
                $comisiones = Wallet::where([
                    ['iduser', '=', $iduser],
                    ['status', '=', 0],
                    ['tipo_transaction', '=', 0],
                ])->get();
            }else {
                $comisiones = Wallet::whereIn('id', $listComision)->get();
            }

            $bruto = $comisiones->sum('monto');
            if ($bruto < 50) {
                return 0; // Esta por debajo del limite diario
            }
            $feed = ($bruto * 0.06);
            $total = ($bruto - $feed);

            $arrayLiquidation = [
                'iduser' => $iduser,
                'total' => $total,
                'monto_bruto' => $bruto,
                'feed' => $feed,
                'hash',
                'wallet_used' => $user->type_wallet.' - '.$user->wallet_address,
                'status' => 0,
            ];
            $idLiquidation = $this->saveLiquidation($arrayLiquidation);

            // $concepto = 'Liquidacion del usuario '.$user->fullname.' por un monto de '.$bruto;
            // $arrayWallet =[
            //     'iduser' => $user->id,
            //     'referred_id' => $user->id,
            //     // 'credito' => $bruto,
            //     'monto' => $bruto,
            //     'descripcion' => $concepto,
            //     'status' => 0,
            //     'tipo_transaction' => 1,
            // ];

            // $this->walletController->saveWallet($arrayWallet);
            
            if (!empty($idLiquidation)) {
                $listComi = $comisiones->pluck('id');
                Wallet::whereIn('id', $listComi)->update([
                    'status' => 1,
                    'liquidation_id' => $idLiquidation
                ]);
            }
            return 1; // Liquidacion exitosa
        } catch (\Throwable $th) {
            Log::error('Liquidaction - generarLiquidation -> Error: '.$th);
            abort(403, "Ocurrio un error, contacte con el administrador");
        }
    }

    /**
     * Permite guardar las liquidaciones y devuelve el id de la misma
     *
     * @param array $data
     * @return integer
     */
    public function saveLiquidation(array $data): int
    {
        $liquidacion = Liquidaction::create($data);
        return $liquidacion->id;
    }

    /**
     * Permite elegir que opcion hacer con las liquidaciones
     *
     * @param Request $request
     * @return void
     */
    public function procesarLiquidacion(Request $request)
    {
        if ($request->action == 'aproved') {
            $validate = $request->validate([
                'google_code' => ['required', 'numeric'],
                'correo_code' => ['required'], 
                'wallet' => ['required']
            ]);
        }else{
            $validate = $request->validate([
                'comentario' => ['required'],
            ]);
        }
        try {
            if ($validate) {

                $idliquidation = $request->idliquidation;
                $liquidation = Liquidaction::find($idliquidation);
                $accion = 'No Procesada';

                if ($this->reversarRetiro30Min()) {
                    return redirect()->back()->with('msj-danger', 'El tiempo limite fue excedido');
                }

                if (session()->has('intentos_fallidos')) {

                    if (session('intentos_fallidos') >= 3) {
                        session()->forget('intentos_fallidos');
                        $request->comentario = 'Demasiados Intento Fallido con los codigo';
                        $accion = 'Reversada';
                        $this->reversarLiquidacion($idliquidation, $request->comentario);
                    }

                    //Verifica si los codigo esta bien
                    if (!$this->doubleAuthController->checkCode($liquidation->iduser, $request->google_code) && $liquidation->code_correo != $request->correo_code && session()->has('intentos_fallidos')) {
                        session(['intentos_fallidos' => (session('intentos_fallidos') + 1)]);
                        return redirect()->back()->with('msj-danger', 'La Liquidacion fue '.$accion.' con exito, Codigos incorrectos');
                    }

                    $fullname = $request->fullname;
                    $iduser = $request->iduser;
                    $total = str_replace(',','.',str_replace('.','',$request->total));
                    $total = round($total, 2);
                    // dd($total);
                    // dd("ID Liquidacion " . $idliquidation, "Fulll Name " . $fullname, "ID Usuario " . $iduser, "Total " . $total);
                    $accion = 'No Procesada';
                    if ($request->action == 'reverse') {
                        $accion = 'Reversada';
                        $this->reversarLiquidacion($idliquidation, $request->comentario);
                    }elseif ($request->action == 'aproved') {
                        $accion = 'Aprobada';
                        $this->aprovarLiquidacion($idliquidation, $request->hash);
                    }
                }

                if ($accion != 'No Procesada') {
                    $arrayLog = [
                        'idliquidation' => $idliquidation,
                        'comentario' => $request->comentario,
                        'accion' => $accion
                    ];
                    DB::table('log_liquidations')->insert($arrayLog);
                }

                $concepto = 'Liquidacion del usuario '.$fullname.' por un monto de '.$total;
                $referred_id = User::find($iduser)->referred_id;
                $arrayWallet =[
                    'iduser' => $iduser,
                    'referred_id' => $referred_id,
                    'monto' =>  $total,
                    'descripcion' => $concepto,
                    'status' => 0,
                    'tipo_transaction' => 1,
                ];
                // dd($arrayWallet);
                $this->walletController->saveWallet($arrayWallet);
                
                return redirect()->back()->with('msj-success', 'La Liquidacion fue '.$accion.' con exito');
            }
        } catch (\Throwable $th) {
            Log::error('Liquidaction - saveLiquidation -> Error: '.$th);
            abort(403, "Ocurrio un error, contacte con el administrador");
        }
    }

    /**
     * Permite aprobar las liquidaciones
     *
     * @param integer $idliquidation
     * @param string $hash
     * @return void
     */
    public function aprovarLiquidacion($idliquidation, $hash)
    {
        Liquidaction::where('id', $idliquidation)->update([
            'status' => 1,
            'hash' => $hash
        ]);

        Wallet::where('liquidation_id', $idliquidation)->update(['liquidado' => 1]);
    }

    /**
     * Permite procesar reversiones del sistema
     *
     * @param integer $idliquidation
     * @param string $comentario
     * @return void
     */
    public function reversarLiquidacion($idliquidation, $comentario)
    {
        $liquidacion = Liquidaction::find($idliquidation);
        
        Wallet::where('liquidation_id', $idliquidation)->update([
            'status' => 0,
            'liquidation_id' => null,
        ]);

        // $concepto = 'Liquidacion Reservada - Motivo: '.$comentario;
        // $arrayWallet =[
        //     'iduser' => $liquidacion->iduser,
        //     'orden_purchases_id' => null,
        //     'referred_id' => $liquidacion->iduser,
        //     'monto' => $liquidacion->monto_bruto,
        //     'descripcion' => $concepto,
        //     'status' => 3,
        //     'tipo_transaction' => 0,
        // ];

        // $this->walletController->saveWallet($arrayWallet);

        $liquidacion->status = 2;
        $liquidacion->save();
    }

    public function retirarSaldo(Request $request)
    {
        try {  
            $user = Auth::user();
    
            $comisiones = Wallet::where([
                ['iduser', '=', $user->id],
                ['status', '=', 0],
                ['tipo_transaction', '=', 0],
            ])->get();

            $bruto = $comisiones->sum('monto');
            if ($bruto < 25) {
                return redirect()->back()->with('msj-danger', 'El monto minimo de retirar es 25 Usd');
            }
            
            if($bruto < 250){
                $feed = ($bruto * 0.085);
            }else{
                $feed = ($bruto * 0.045);
            }
            $total = ($bruto - $feed);


            $arrayLiquidation = [
                'iduser' => $user->id,
                'total' => $total,
                'monto_bruto' => $bruto,
                'feed' => $feed,
                'hash',
                'wallet_used' => $user->type_wallet.' - '.$user->wallet_address,
                'status' => 0,
            ];
            $idLiquidation = $this->saveLiquidation($arrayLiquidation);
            
            if (!empty($idLiquidation)) {
                $listComi = $comisiones->pluck('id');
                Wallet::whereIn('id', $listComi)->update([
                    'status' => 1,
                    'liquidation_id' => $idLiquidation
                ]);
            }

            return redirect()->back()->with('msj-success', 'Saldo retirado con exito');
        } catch (\Throwable $th) {
            Log::error('Liquidaction - generarLiquidation -> Error: '.$th);
            abort(403, "Ocurrio un error, contacte con el administrador");
        }
    }

        /**
     * LLeva a la vistas de las liquidaciones reservadas o aprobadas a los Users
     *
     * @param string $status
     * @return void
     */
    public function retiroHistory()
    {
        try {
            
            $id = Auth::id();
            $liquidaciones = Liquidaction::where('iduser', $id)->get();
            foreach ($liquidaciones as $liqui) {
                $liqui->fullname = $liqui->getUserLiquidation->fullname;
            }
            return view('settlement.retiros', compact('liquidaciones'));
        } catch (\Throwable $th) {
            Log::error('Liquidaction - retiroHistory -> Error: '.$th);
            abort(403, "Ocurrio un error, contacte con el administrador");
        }
    }

    public function withdraw()
    {
        $this->reversarRetiro30Min();
        return view('settlement.withdraw');
    }

    public function sendCodeEmail($wallet): int
    {
        try {
            $this->reversarRetiro30Min();  
            if (!session()->has('intentos_fallidos')) {
                session(['intentos_fallidos' => 1]);
            }
            $liquidation = Liquidaction::where([
                ['iduser', '=', Auth::id()],
                ['status', '=', 0],
            ])->first();
            if ($liquidation != null) {
                return $liquidation->id;
            }

            $user = Auth::user();
        
            $comisiones = Wallet::where([
                ['iduser', '=', $user->id],
                ['status', '=', 0],
                ['liquidado','=', 0],
                ['tipo_transaction', '=', 0],
            ])->get();

            $bruto = $comisiones->sum('monto');
            /*
            if ($bruto < 50) {
                return 0;
            }
            */
            $feed = ($bruto * 0.06);
            $total = ($bruto - $feed);
        
            $arrayLiquidation = [
                'iduser' => $user->id,
                'total' => $total,
                'monto_bruto' => $bruto,
                'feed' => $feed,
                'hash',
                'wallet_used' => $wallet,
                'status' => 0,
                'code_correo' => Str::random(10),
                'fecha_code' => Carbon::now()
            ];
            $idLiquidation = $this->saveLiquidation($arrayLiquidation);

            $dataEmail = [
                'billetera' => $wallet,
                'total' => $total,
                'user' => $user->fullname,
                'code' => $arrayLiquidation['code_correo']
            ];

            Mail::send('mail.SendCodeRetiro', $dataEmail, function ($msj) use ($user)
            {
                $msj->subject('Codigo Retiro');
                $msj->to($user->email);
            });
            
            if (!empty($idLiquidation)) {
                $listComi = $comisiones->pluck('id');
                Wallet::whereIn('id', $listComi)->update([
                    'status' => 1,
                    'liquidation_id' => $idLiquidation
                ]);
            }
            return $idLiquidation;
            
        } catch (\Throwable $th) {
            Log::error('Liquidaction - sendCodeEmail -> Error: '.$th);
            abort(403, "Ocurrio un error, contacte con el administrador");
        } 
    }

    /**
     * Permite reversar los retiros que tienen mas de 30 min activos
     *
     * @return bool
     */
    public function reversarRetiro30Min():bool
    {
        $liquidation = Liquidaction::where([
            ['iduser', '=', Auth::id()],
            ['status', '=', 0]
        ])->first();
        $result = false;
        if ($liquidation != null) {
            $fechaActual = Carbon::now();
            $fechaCodeCorreo = new Carbon($liquidation->fecha_code);
            if ($fechaCodeCorreo->diffInMinutes($fechaActual) >= 30) {
                $this->reversarLiquidacion($liquidation->id, 'Tiempo limite de codigo sobrepasado');
                $result = true;
            }
        }
        return $result;
    }
}
