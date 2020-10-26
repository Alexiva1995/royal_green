<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use App\Sidebar; use App\MenuAction; use App\Commission; use App\User; use App\Transfer; use App\Notification;
use Auth; use DB; use Date; use Carbon\Carbon; use App\Liquidacion;
use Session;use Mail; use App\Bono;

class LiquidacionesController extends Controller
{
	function __construct()
	{
        // TITLE
		view()->share('title', 'Liquidaciones');
	}

	// Liquidaciones por meses actual
	public function index(){
	    // DO MENU
        view()->share('do', collect(['name' => 'inicio', 'text' => 'Inicio']));
        //
	    $fecha = new Carbon();
	    $inicio = $fecha->now()->format('Y-m').'-01';
	    $fin = $fecha->now()->format('Y-m').'-31';
	    if (Auth::user()->ID == 1){
	        $liquidaciones = Liquidacion::where('estado', 0)->get();
	        $total = Liquidacion::where('estado', 0)->get()->sum('comision');
	    }else{
	        $liquidaciones = Liquidacion::where('user_id', Auth::user()->ID)->whereIn('fecha', [$inicio, $fin])->get();
	    }

	    return view('dashboard.liquidacionesRecords')->with(compact('liquidaciones', 'total'));
	}

        // Liquidaciones por meses actual
	public function liquidacionHistory(){
	    // DO MENU
        view()->share('do', collect(['name' => 'Hitorial', 'text' => 'Historial']));
        //
	    $fecha = new Carbon();
	    $inicio = $fecha->now()->format('Y-m').'-01';
	    $fin = $fecha->now()->format('Y-m').'-31';
	    $liquidaciones = Liquidacion::where('estado', '<>', 0)->get();

	    return view('dashboard.liquidacionesHistory')->with(compact('liquidaciones'));
	}

        // Procesa las Liquidaciones pendientes
	public function procesarLiquidacion(){
	    $bonos = Commission::where(['status' => 1, 'edo_liquidacion' => 0])->get();
	    $cont = 0;
        foreach ($bonos as $bono){
            $cont++;
            $liquidacion = Liquidacion::where('user_id', $bono->iduser)->get();
            $fecha = new Carbon;
            if(!empty($liquidacion->toArray())) {
                if($liquidacion[0]['estado'] == 0){
			Liquidacion::where('user_id', $liquidacion[0]['user_id'])->update([
				'comision' => $liquidacion[0]['comision'] + $bono->monto,
				'fecha' => $fecha->now(),
			]);
                }else{
                    $this->saveLiquidacion($bono);
                }
            }else{
                $this->saveLiquidacion($bono);
            }
            Bono::where('id', $bono->id)->update(['edo_liquidacion' => 1]);
        }

        if ($cont > 0){
            Session::flash('flash_message', 'Se ha registrado las liquidaciones con exito');
        }else{
            Session::flash('flash_message', 'No hay nuevos registros o modificaciones de liquidaciones');
        }

        return redirect('mioficina/admin/liquidaciones');
	}

        // Verifica a que estado se va a poner la Liquidacion
        public function estado(Request $datos)
        {
                if ($datos->estado == '2') {
                        $this->Rechazar((int)$datos->ID);
                } else if($datos->estado == '1'){
                        $this->Aprobar((int)$datos->ID);
                }
        }

        // Permite liquidar aprobar todas las liquidaciones
        public function liquidar_todo()
        {
                $liquidaciones = Liquidacion::where('estado', 0)->get();
                foreach ($liquidaciones as $liquidacion) {
                        $this->Aprobar($liquidacion->ID);
                }
        }

        // Aprueba una sola liquidacion
        private function Aprobar($idliqui)
        {
                $liquidacion = Liquidacion::find($idliqui);
                Liquidacion::where('id', $idliqui)->update(['estado' => 1]);
                $user = User::find($liquidacion->user_id);
                User::where('ID', $liquidacion->user_id)->update([
                        'wallet_amount' => $user->wallet_amount - $liquidacion->comision,
                        'bank_amount' => $user->bank_amount + $liquidacion->comision,
                ]);
        }

        // Rechaza la liquidacion
        private function Rechazar($idliqui)
        {
                $liquidacion = Liquidacion::find($idliqui);
                Liquidacion::where('id', $idliqui)->update(['estado' => 2]);
                $user = User::find($liquidacion->user_id);
                User::where('ID', $liquidacion->user_id)->update([
                        'wallet_amount' => $user->wallet_amount - $liquidacion->comision,
                ]);
        }

        // Guarda las liquidaciones procesadas
	private function saveLiquidacion($bono){
	    $fecha = new Carbon;
            Liquidacion::create([
                    'user_id' => $bono->iduser,
                    'username' => $bono->nameuser,
                    'fecha' => $fecha->now(),
                    'comision' => $bono->monto,
                    'estado' => 0,
            ]);
	}
}
