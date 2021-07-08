<?php

namespace App\Http\Controllers;

use App\Auditoria;
use App\User;
use Illuminate\Http\Request;

class AuditoriaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        view()->share('title', 'Log de Auditoria');
        $auditorias = Auditoria::all()->where('code_used', 1);
        foreach ($auditorias as $audi) {
            $audi->nombre = User::find($audi->iduser)->display_name;
        }
        return view('admin.auditoria', compact('auditorias'));
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
    
    /**
     * Permite guardar la informacion de las auditoria
     *
     * @param array $data
     * @return void
     */
    public function saveAuditoria($data)
    {
        Auditoria::create($data);
    }

    /**
     * Permite actualizar al auditoria
     *
     * @param array $data
     * @param integer $iduser
     * @param string $code
     * @return void
     */
    public function updateAuditoria($data, $iduser, $code)
    {
        Auditoria::where([
            ['code', '=', $code],
            ['iduser', '=', $iduser]
        ])->update($data);
    }

    /**
     * permite verificar el codigo enviado es correcto
     *
     * @param string $code
     * @return boolean
     */
    public function checkCode($code): bool
    {
        $check = Auditoria::where([
            ['code', '=', $code],
            ['code_used', '=', 0]
        ])->first();
        $result = false;
        if ($check != null) {
            $result = true;
        }
        return $result;
    }
}
