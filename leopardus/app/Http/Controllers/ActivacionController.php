<?php

namespace App\Http\Controllers;
use App\User;

use Carbon\Carbon;
use App\Http\Controllers\IndexController;


class ActivacionController extends Controller
{

    /**
     * Verifica que es estado de los usuarios 
     * 
     * @access public 
     * @param int $userid - id del usuarios a verificar
     * @return string
     */
    public function activarUsuarios($userid)
    {
        $funciones = new IndexController;
        $user = User::find($userid);
        $fechaActual = Carbon::now();
        $paquete = [];
        if (true) {
            $compras = $funciones->getInforShopping($user->ID);
            $fechaNueva = null;
            $activo = false;
            $paqueteUser = null;
            if ($user->paquete != null) {
                $paqueteUser = json_decode($user->paquete);
            }
            foreach ($compras as $compra) {
                if ($paqueteUser == null || $user->status == 0) {
                    $fechaTmp = new Carbon($compra['fecha']);
                    $fechaNueva = $fechaTmp->addDay(365);
                    if ($fechaNueva > $fechaActual) {
                        $activo = true;
                        foreach ($compra['productos'] as $producto) {
                            $paquete = $producto;
                        }
                    }else{
                        $activo = false;
                    }
                }elseif($paqueteUser != null && $user->status == 1){
                    $producto = null;
                    foreach ($compra['productos'] as $product) {
                        if ($product['idproducto'] > $paqueteUser->idproducto) {
                            $producto = $product;
                        }   
                    }
                    if ($producto != null) {
                        $fechaTmp = new Carbon($compra['fecha']);
                        $fechaNueva = $fechaTmp->addDay(365);
                        if ($fechaNueva > $fechaActual) {
                            $activo = true;
                            $paquete = $producto;
                        }else{
                            $activo = false;
                        }
                    }
                }
            }
            if ($activo) {
                $user->paquete = json_encode($paquete);
                $user->status = 1;
                $user->fecha_activacion = $fechaNueva;
                $user->save();
            }elseif(!$this->statusActivacion($user)){
                $user->paquete = null;
                $user->status = 0;
                $user->save();
            }
            // $user->save();
        }
    }

    /**
     * Permite verificar el estado del usuario
     *
     * @param object $user
     * @return bool
     */
    private function statusActivacion($user): bool
    {
        $result = true;
        $fechaActual = Carbon::now();
        if (empty($user->fecha_activacion)) {
            $result = false;
        }else{
            $fechatmp = new Carbon($user->fecha_activacion);
            if ($fechaActual > $fechatmp) {
                $result = false;
            }
        }
        return $result;
    }

    /**
     * Permite arreglar los paquetes activos 
     *
     * @return void
     */
    public function arreglarPaqueteActivado()
    {
        $users = User::where([
            ['status', '=', 1],
            ['ID', '!=', 1]
        ])->select('ID')->get();

        foreach ($users as $user) {
            $this->activarUsuarios($user->ID);
        }
    }

}
