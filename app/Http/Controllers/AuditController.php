<?php

namespace App\Http\Controllers;

use Datatables;
use App\Models\User;
use App\Models\Wallet;
use App\Models\Inversion;
use App\Models\RankRecords;
use App\Models\WalletBinary;
use Illuminate\Http\Request;


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
        try {
             return view('audit.puntos');
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
    public function dataPuntos(Request $request)
    {
        if ($request->ajax()) {
            $data = WalletBinary::latest()->get();
            return Datatables::of($data)
                ->addColumn('id', function($data){
                    return $data->id;
                })
                ->addColumn('usuario', function($data){
                    return $data->getUserBinary->email;
                })
                ->addColumn('referido', function($data){
                    return User::find($data->getUserBinary->referred_id)->email;
                })
                ->addColumn('puntos_derecha', function($data){
                    return $data->puntos_d;
                })
                ->addColumn('puntos_izquierda', function($data){
                    return $data->puntos_i;
                })
                ->rawColumns(['action'])
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
 * Establece la Comision con staatus 1
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
}
