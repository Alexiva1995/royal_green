<?php

namespace App\Http\Controllers;

use Datatables;
use App\Models\User;
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
            $data = WalletBinary::latest()->where('iduser', $request->id)->get();
            return Datatables::of($data)
                ->addColumn('id', function($data){
                    return $data->id;
                })
                ->addColumn('usuario', function($data){
                    return $data->getUserBinary->email;
                })
                ->addColumn('referido', function($data){
                    return $data->referred_id->email;
                })
                ->addColumn('puntos_derecha', function($data){
                    return $data->puntos_d;
                })
                ->addColumn('puntos_izquierda', function($data){
                    return $data->puntos_i;
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
                ->rawColumns(['action'])
                ->make(true);
        }
    }
}
