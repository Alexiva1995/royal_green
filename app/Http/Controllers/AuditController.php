<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Wallet;
use App\Models\Inversion;
use App\Models\RankRecords;
use App\Models\WalletBinary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Auth;


class AuditController extends Controller
{

    /**
     * Muestra la vista de Rangos
     *
     * @return \Illuminate\Http\Response
     */
    public function rangos()
    {
        try {
             return view('audit.rangos');
         } catch (\Throwable $th) {
             Log::error('AuditController - index -> Error: '.$th);
             abort(403, "Ocurrio un error, contacte con el administrador");
         }
    }

    /**
     * Datatable dinámico (ServerSide) que se muestra en audit.rangos 
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function dataRangos(Request $request)
    {
        if ($request->ajax()) {
            $data = RankRecords::latest()->get();
            return Datatables::of($data)
                ->addColumn('id', function($data){
                    return $data->id;
                })
                ->addColumn('usuario', function($data){
                    return $data->getUserRank->email;
                })
                ->addColumn('rango', function($data){
                    return $data->getRank->name;
                })
                ->addColumn('fecha', function($data){
                    return $data->fecha_inicio;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
    }

    public function puntosBinarios()
    {
        $puntos = WalletBinary::orderBy('id', 'desc')->get();
        $users = User::where('status', '1')->select(['id', 'username'])->orderBy('id', 'desc')->get();
         
        try {
             return view('audit.puntos', compact('users', 'puntos'));
         } catch (\Throwable $th) {
             Log::error('AuditController - index -> Error: '.$th);
             abort(403, "Ocurrio un error, contacte con el administrador");
         }
    }

    /**
     * Datatable dinámico (ServerSide) que se muestra en audit.datos 
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function dataPuntos(Request $request)
    {
        if ($request->ajax()) {
      
            $data = WalletBinary::where('iduser', $request->id)->orderBy('id', 'desc')->get();

            return Datatables::of($data)
                ->addColumn('id', function($data){
                    return $data->id;
                })
                ->addColumn('referido', function($data){
                    return $data->getReferredBinary->fullname;
                })
                ->addColumn('puntos', function($data){
                    return $data->puntos_reales;
                                       
                })
                ->addColumn('lado', function($data){
                    if($data->side == 'I'){
                        return 'Izquierda';
                    }else{
                        return 'Derecha';
                    }
                })
                ->addColumn('estado', function($data){
                    if($data->status == 0){
                        return 'En espera';
                    }elseif($data->status == 1){
                        return 'Pagado';
                    }elseif($data->status == 2){
                        return 'Cancelado';
                    }
                })
                ->make(true);
        }
    
    }

    public function modificarComisiones()
    {
        $users = User::where('status', '1')->select(['id', 'username'])->orderBy('id', 'desc')->get();
        return view('audit.modificarcomisiones', compact('users'));
    }

    /**
     * Petición asincrona para la obtención de comisiones mediante el id de usuario
     *
     * @param integer $id
     * @return void
     */
    public function dataComisiones(Request $request)
    {
        if ($request->ajax()) {
            $data = Wallet::where('iduser', $request->id)->where('status', 0)->get();
            // dd($data);
            return Datatables::of($data)
                ->addColumn('id', function($data){
                    return $data->id;
                })
                ->addColumn('email', function($data){
                    return $data->getWalletUser->email;
                })
                ->addColumn('descripcion', function($data){
                    return $data->descripcion;
                })
                ->addColumn('monto', function($data){
                    return $data->monto;
                })
                ->addColumn('creacion', function($data){
                    return $data->created_at->format('Y-m-d');
                })
                ->rawColumns(['action'])
                ->make(true);
        }
    }
/**
 * Establece la Comision con staatus 2
 *
 * @param Request $request
 * @return void
 */
    public function eliminarComision(Request $request)
    {
        try{
            $comision = Wallet::find($request->id);
            $comision->update([
                'status' => 2,
            ]);
            $inversion = Inversion::where('iduser', $comision->iduser)->first();
            $restoGanancia = $inversion->ganacia - $comision->monto;
            $inversion->update([
                'ganacia' => $restoGanancia,
            ]);
            return response('success');
        }catch (\Throwable $th) {
            Log::error('AuditController - eliminarComision -> Error: '.$th);
            abort(403, "Ocurrio un error, contacte con el administrador");
        }

    }


    /**
     * Vista para ver el arbol binario del usuario por medio del id
     *
     * @return void
     */
    public function verBinario()
    {
        $users = User::where('status', '1')->select(['id', 'username'])->orderBy('id', 'desc')->get();
        return view('audit.verBinario', compact('users'));
    }
}
