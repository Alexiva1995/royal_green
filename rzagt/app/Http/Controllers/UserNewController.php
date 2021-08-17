<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\IndexController;
use App\Http\Controllers\RangoController;

class UserNewController extends Controller
{
    /**
     * Uso global de los controladores del sistema
     *
     * @var Controller
     */
    public $indexController;
    public $rangoController;


    public function __construct()
    {
        $this->indexController = new IndexController;
        $this->rangoController = new RangoController;
    }

    /**
     * Lleva a la vista principal
     *
     * @return void
     */
    public function index()
    {
        view()->share('title', 'Resumen');
        $data = $this->getDataDashboard(Auth::user()->ID);
        $principal = 1;
        return view('dashboard.index', compact('data', 'principal'));
    }

    /**
     * Permite obtener la informacion que se muestra en el dashboard
     *
     * @param integer $iduser
     * @return void
     */
    public function getDataDashboard($iduser)
    {
        // obtiene la informacion de la ultima rentabilidad agregada
        $paquete = DB::table('log_rentabilidad')->where('iduser', $iduser)->orderBy('id', 'desc')->first();
        if (!empty($paquete)) {
            $paquete->img = asset('assets/paquetes/rg'.$paquete->precio.'.png');
            $paquete->detalles_producto = json_decode($paquete->detalles_producto);
        }

        // obtiene la informacion del bono de bienvenida
        $bienvenida = $this->indexController->bonoBienvenida($iduser);

        $data = [
            'paquetes' => $paquete,
            'rangospoints' => $this->rangoController->getPointRango($iduser),
            'bienvenida' => $bienvenida
        ];

        return $data;
    }


}
