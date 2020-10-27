<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Carbon\Carbon;
use App\User; 
use App\Settings;
use App\Commission; 
use App\Notification;
use App\SettingsComision;
use App\Http\Controllers\IndexController;
use App\Http\Controllers\WalletController;


class ComisionesController extends Controller
{
    public $funciones;
    public $wallet;

    public function __construct()
    {
        $this->funciones = new IndexController;
        $this->wallet = new WalletController;
    }

    /**
     * Permite guardar la comisiones ganadas
     *
     * @param integer $iduser - id del usuario
     * @param integer $idcompra - id de la compra
     * @param float $totalComision - total a pagar
     * @param string $referred_email - correo del referido
     * @param integer $referred_level - nivel del referido
     * @param string $concepto - concepto de la comision
     * @param string $tipo_comision - tipo de comision
     * @return void
     */
    public function guardarComision($iduser, $idcompra, $totalComision, $referred_email, $referred_level, $concepto, $tipo_comision)
    {
        try {
            $checkComision = Commission::where([
                ['user_id', '=', $iduser],
                ['compra_id', '=', $idcompra],
            ])->first();

            if ($checkComision == null) {
                $comision = Commission::create([
                    'user_id' => $iduser,
                    'compra_id' => $idcompra,
                    'date' => Carbon::now(),
                    'total' => $totalComision,
                    'concepto' => $concepto,
                    'tipo_comision' => $tipo_comision,
                    'referred_email' => $referred_email,
                    'referred_level' => $referred_level,
                    'status' => true,
                ]);
        
                if ($concepto != 'sin comision') {
                    $user = User::find($iduser);
                    $datos = [
                        'iduser' => $iduser,
                        'usuario' => $user->display_name,
                        'descripcion' => $concepto,
                        'puntos' => 0,
                        'puntosI' => 0,
                        'puntosD' => 0,
                        'descuento' => 0,
                        'debito' => 0,
                        'credito' => 0,
                        'balance' => $user->wallet_amount,
                        'tipotransacion' => 2
                    ];
                    $this->wallet->saveWallet($datos);
                }
            }
        } catch (\Throwable $th) {
            dd($th);
        }
    }

    /**
     * Permite pagar el bono directo 
     *
     * @return void
     */
    public function bonoDirecto()
    {   
        try {
            $compras = $this->funciones->getAllCompras();
            if (!empty($compras)) {
                foreach ($compras as $compra) {
                    $sponsors = $this->funciones->getSponsor($compra['idusuario'], [], 0, 'ID', 'referred_id');
                    if (!empty($sponsors)) {
                        foreach ($sponsors as $sponsor) {
                            if ($sponsor->nivel == 1) {
                                $userReferido = User::find($compra['idusuario']);
                                $pagar = ($compra->total * 0.10);
                                $concepto = 'Bono Directo, del usuario '.$userReferido->display_name.', por la compra '.$compra['idcompra'];
                                $this->guardarComision($sponsor->ID, $compra['idcompra'], $pagar, $userReferido->email_user, 1, $concepto, 'Bono Directo');
                            }
                        }
                    }
                }
            }
        } catch (\Throwable $th) {
            dd($th);
        }
    }

    /**
     * Permite Pagar el Bono Indirecto
     *
     * @return void
     */
    public function bonoIndirecto()
    {
        try {
            $compras = $this->funciones->getAllCompras();
            if (!empty($compras)) {
                foreach ($compras as $compra) {
                    $sponsors = $this->funciones->getSponsor($compra['idusuario'], [], 0, 'ID', 'referred_id');
                    if (!empty($sponsors)) {
                        foreach ($sponsors as $sponsor) {
                            $paquete = json_decode($sponsor->paquete);
                            $nivel = -1;
                            $porcentaje = 0;
                            if ($paquete->idproducto >= 5653 && $paquete->idproducto < 5655) {
                                $nivel = 2;
                                $porcentaje = 0.03;
                            }
                            if ($paquete->idproducto >= 5655 && $paquete->idproducto <= 5658) {
                                $nivel = 3;
                                $porcentaje = 0.02;
                            }
                            if ($sponsor->nivel == $nivel) {
                                $userReferido = User::find($compra['idusuario']);
                                $pagar = ($compra->total * $porcentaje);
                                $concepto = 'Bono Indirecto, del usuario '.$userReferido->display_name.', por la compra '.$compra['idcompra'];
                                $this->guardarComision($sponsor->ID, $compra['idcompra'], $pagar, $userReferido->email_user, 1, $concepto, 'Bono Directo');
                            }
                        }
                    }
                }
            }
        } catch (\Throwable $th) {
            dd($th);
        }   
    }

    /**
     * Permite pagar los puntos binarios
     *
     * @return void
     */
    public function bonoBinario()
    {
        try {
            $fecha = Carbon::now();
            $users = User::all()->where('status', '=', 1);
            foreach ($users as $user) {
                $puntos = json_decode($user->puntos);
                $paquete = json_decode($user->paquete);
                $pagar = 0;
                $porcentaje = 0;
                if ($puntos->binario_izq >= $puntos->binario_der) {
                    $pagar = $puntos->binario_izq;
                }else{
                    $pagar = $puntos->binario_der;
                }

                if (!empty($paquete)) {
                    $porcentaje = $paquete->porc_binario;
                }
                
                if ($pagar != 0 && $porcentaje != 0) {
                    $puntos->binario_izq = ($puntos->binario_izq - (float)$pagar);
                    $puntos->binario_der = ($puntos->binario_der - (float)$pagar);

                    $user->puntos = json_encode($puntos);

                    
                    $totalcomision = ((float)$pagar * $porcentaje);
                    $idcomision = '20'.$fecha->format('Ymd');
                    $this->guardarComision($user->ID, $idcomision, $totalcomision, $user->user_email, 0, 'Bonos Binario', 'Bono Binario');
                    $this->bonoConstrucion($user->ID, $totalcomision);
                    $user->save();
                }
            }
        } catch (\Throwable $th) {
            dd($th);
        }
    }

    /**
     * Permite pagar los puntos de rango
     *
     * @return void
     */
    public function puntosRangos()
    {
        try {
            $compras = $this->funciones->getAllCompras();
            if (!empty($compras)) {
                foreach ($compras as $compra) {
                    $sponsors = $this->funciones->getSponsor($compra['idusuario'], [], 0, 'ID', 'referred_id');
                    if (!empty($sponsors)) {
                        $userReferido = User::find($compra['idusuario']);
                        $side = $userReferido->ladomatrix;
                        $concepto = 'Puntos Rango, Obtenido por el usuario '.$userReferido->display_name.', por la compra'.$compra['id'];
                        foreach ($sponsors as $sponsor) {
                            if ($sponsor->nivel > 0) {
                                $this->savePoints($compra['total'], $sponsor->ID, $concepto, 'R', $compra['idcompra'], $sponsor->nivel, $userReferido->user_email);
                            }
                        }
                    }
                }
            }
        } catch (\Throwable $th) {
            dd($th);
        }
    }

    /**
     * Permite pagar los puntos Binarios
     *
     * @return void
     */
    public function puntosBinarios()
    {
        try {
            $compras = $this->funciones->getAllCompras();
            if (!empty($compras)) {
                foreach ($compras as $compra) {
                    $sponsors = $this->funciones->getSponsor($compra['idusuario'], [], 0, 'ID', 'position_id');
                    if (!empty($sponsors)) {
                        $userReferido = User::find($compra['idusuario']);
                        $side = $userReferido->ladomatrix;
                        $concepto = 'Puntos Binarios, Obtenido por el usuario '.$userReferido->display_name.', por la compra'.$compra['id'];
                        foreach ($sponsors as $sponsor) {
                            if ($sponsor->nivel > 0) {
                                $this->savePoints($compra['total'], $sponsor->ID, $concepto, $side, $compra['idcompra'], $sponsor->nivel, $userReferido->user_email);
                                $side = $sponsor->ladomatrix;
                            }
                        }
                    }
                }
            }
        } catch (\Throwable $th) {
            dd($th);
        }
    }

    /**
     * Permite guardar la informacion de los puntos
     *
     * @param integer $puntos - puntos a asignar
     * @param integer $iduser - usuario que recibira los puntos
     * @param string $concepto - concepto 
     * @param string $side - lado a donde pertenencen los puntos
     * @param integer $idcompra - id de la compra realizada
     * @param integer $referred_level - Nivel del Usuario a pagar
     * @param string $referred_email - Email del referido a pagar
     * @return void
     */
    public function savePoints(int $puntos, int $iduser, string $concepto, string $side, int $idcompra, int $referred_level, string $referred_email)
    {
        try {
            $user = User::find($iduser);
            $puntosUser = json_decode($user->puntos);
            $punto_izq = 0; $punto_der = 0; $punto_rank = 0;
            $idcomision = $idcompra.'20';
            $tipo_comision = 'Puntos Binarios';
            if ($side == 'I') {
                $punto_izq = $puntos;
                $puntosUser->binario_izq = ($puntosUser->binario_izq + $puntos);
            }elseif ($side == 'D') {
                $punto_der = $puntos;
                $puntosUser->binario_der = ($puntosUser->binario_der + $puntos);
            }else{
                $tipo_comision = 'Puntos Rango';
                $punto_rank = $puntos;
                $puntosUser->rank = ($puntosUser->rank + $puntos);
                $idcomision = $idcompra.'30';
            }

            $user->puntos = json_encode($puntos);

            $checkComision = Commission::where([
                ['user_id', '=', $iduser],
                ['compra_id', '=', $idcomision],
            ])->first();

            if ($checkComision == null) {
                $comision = Commission::create([
                    'user_id' => $iduser,
                    'compra_id' => $idcomision,
                    'date' => Carbon::now(),
                    'total' => $puntos,
                    'concepto' => $concepto,
                    'tipo_comision' => $tipo_comision,
                    'referred_email' => $referred_email,
                    'referred_level' => $referred_level,
                    'status' => true,
                ]);

                $datos = [
                    'iduser' => $iduser,
                    'usuario' => $user->display_name,
                    'descripcion' => $concepto,
                    'puntos' => $punto_rank,
                    'puntosI' => $punto_izq,
                    'puntosD' => $punto_der,
                    'descuento' => 0,
                    'debito' => 0,
                    'credito' => 0,
                    'balance' => 0,
                    'tipotransacion' => 2
                ];
                $this->wallet->saveWallet($datos);
                $user->save();
            }
        } catch (\Throwable $th) {
            dd($th);
        }
        
    }

    /**
     * Permite verificar si el usuario ya tiene el campo de puntos creado, sino los crea
     *
     * @param integer $iduser
     * @return void
     */
    public function checkExictPoint(int $iduser)
    {
        try {
            $user = User::find($iduser)->only('puntos');
            $puntos = [
                'binario_izq' => 0,
                'binario_der' => 0,
                'rank' => 0,
            ];
            if (empty($user->puntos)) {
                $user->puntos = json_encode($puntos);
                $user->save();
            }
        } catch (\Throwable $th) {
            dd($th);
        }
    }

    /**
     * Permite pagar el bono de construcion
     *
     * @param integer $iduser
     * @param float $bonobinario
     * @return void
     */
    public function bonoConstrucion(int $iduser, float $bonobinario)
    {
        try {
            $fecha = Carbon::now();
            $sponsors = $this->funciones->getSponsor($iduser, [], 0, 'ID', 'referred_id');
            if (!empty($sponsors)) {
                foreach ($sponsors as $sponsor) {
                    if ($sponsor->nivel > 0 && $sponsor->nivel <= 11) {
                        $porcentaje = $this->porceNivelRango($sponsor->rol_id, $sponsor->nivel);
                        if ($porcentaje > 0) {
                            $userReferido = User::find($iduser);
                            $concepto = 'Bono Contrucion, Obtenido del usuario '.$userReferido->display_name;
                            $idcomision = '40'.$fecha->format('Ymd');
                            $totalcomision = ($bonobinario * $porcentaje);
                            $this->guardarComision($sponsor->ID, $idcomision, $totalcomision, $userReferido->user_email, $sponsor->nivel, $concepto, 'Bono Construcion');
                        }
                    }
                }
            }
        } catch (\Throwable $th) {
            dd($th);
        }
    }

    /**
     * Permite obtener el porcentaje del pago
     *
     * @param integer $rol
     * @param integer $nivel
     * @return float
     */
    public function porceNivelRango(int $rol,  int $nivel): float
    {
        $valor = 0;
        $arrayRango = [
            2 => 0.05, 3 => 0.04, 4 => 0.03,
            5 => 0.02, 6 => 0.02, 7 => 0.02,
            8 => 0.02, 9 => 0.02, 10 => 0.02,
            11 => 0.03, 12 => 0.05
        ];

        if ($nivel >= 1 && $rol >= 2) {
            $valor = $arrayRango[$rol];
        }

        if ($nivel >= 2 && $rol >= 3) {
            $valor = $arrayRango[$rol];
        }

        if ($nivel >= 3 && $rol >= 4) {
            $valor = $arrayRango[$rol];
        }

        if ($nivel >= 4 && $rol >= 5) {
            $valor = $arrayRango[$rol];
        }

        if ($nivel >= 5 && $rol >= 6) {
            $valor = $arrayRango[$rol];
        }

        if ($nivel >= 6 && $rol >= 7) {
            $valor = $arrayRango[$rol];
        }

        if ($nivel >= 7 && $rol >= 8) {
            $valor = $arrayRango[$rol];
        }

        if ($nivel >= 8 && $rol >= 9) {
            $valor = $arrayRango[$rol];
        }

        if ($nivel >= 9 && $rol >= 10) {
            $valor = $arrayRango[$rol];
        }
        
        if ($nivel >= 10 && $rol >= 11) {
            $valor = $arrayRango[$rol];
        }

        if ($nivel >= 11 && $rol >= 12) {
            $valor = $arrayRango[$rol];
        }
        
        return $valor;
    }
}

