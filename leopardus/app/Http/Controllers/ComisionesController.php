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
     * Llama a la funciones para el pago de las mismas
     *
     * @return void
     */
    public function payBonus()
    {
        $this->bonoDirecto();
        $this->bonoIndirecto();
        $this->puntosBinarios();
        // $this->puntosRangos();
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

                $rentabilidadActiva = $this->checkstatusRentabilidad($iduser);
                // if ($concepto != 'sin comision') {
                if ($rentabilidadActiva == 1) {
                    $user = User::find($iduser);
                    $user->wallet_amount = ($user->wallet_amount + $totalComision);
                    $user->save();
                    $datos = [
                        'iduser' => $iduser,
                        'usuario' => $user->display_name,
                        'descripcion' => $concepto,
                        'puntos' => 0,
                        'puntosI' => 0,
                        'puntosD' => 0,
                        'email_referred' => $referred_email,
                        'descuento' => 0,
                        'debito' => $totalComision,
                        'credito' => 0,
                        'balance' => $user->wallet_amount,
                        'tipotransacion' => 2
                    ];
                    $this->wallet->saveWallet($datos);
                    if ($tipo_comision == 'Bonos Binario' && $tipo_comision == 'Bono Directo') {
                        $this->saveRentabilidaBono($iduser, $totalComision, $tipo_comision);
                    }
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
                            if ($compra['idusuario'] != $sponsor->ID) {
                                if ($sponsor->nivel == 1) {
                                    $userReferido = User::find($compra['idusuario']);
                                    $pagar = ($compra['total'] * 0.10);
                                    $concepto = 'Bono Directo, del usuario '.$userReferido->display_name.', por la compra '.$compra['idcompra'];
                                    if ($pagar > 0) {
                                        $this->guardarComision($sponsor->ID, $compra['idcompra'], $pagar, $userReferido->user_email, 1, $concepto, 'Bono Directo');
                                    }
                                }
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
                            $nivel = 0;
                            $porcentaje = 0;
                            if (!empty($paquete)) {
                                if (!empty($paquete->idproducto)) {
                                    if ($paquete->idproducto >= 5653) {
                                        $nivel = 2;
                                        $porcentaje = 0.03;
                                    }
                                    if ($paquete->idproducto >= 5655 && $paquete->idproducto <= 5658) {
                                        $nivel = 3;
                                        $porcentaje = 0.02;
                                        if ($sponsor->nivel == 2) {
                                            $nivel = 2;
                                            $porcentaje = 0.03;
                                        }
                                    }
                                }
                            }
                            if ($nivel > 0) {
                                if ($sponsor->nivel == $nivel) {
                                    $userReferido = User::find($compra['idusuario']);
                                    $idcomision = $compra['idcompra'].'10';
                                    $pagar = ($compra['total'] * $porcentaje);
                                    $concepto = 'Bono Indirecto, del usuario '.$userReferido->display_name.', por la compra '.$compra['idcompra'];
                                    if ($pagar) {
                                        $this->guardarComision($sponsor->ID, $idcomision, $pagar, $userReferido->user_email, 1, $concepto, 'Bono Directo');
                                    }
                                }
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
                if ($puntos->binario_izq <= $puntos->binario_der) {
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
                    $concepto = 'Puntos Rango, Obtenido por el pago del Bono Binario del dia'.$fecha->format('Y-m-d');
                    $this->savePoints($totalcomision, $user->ID, $concepto, 'R', $idcomision, 1, $user->user_email);
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
                        $concepto = 'Puntos Rango, Obtenido por el usuario '.$userReferido->display_name.', por la compra'.$compra['idcompra'];
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
                        $concepto = 'Puntos Binarios, Obtenido por el usuario '.$userReferido->display_name.', por la compra'.$compra['idcompra'];
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
            $this->checkExictPoint($iduser);
            $user = User::find($iduser);
            $puntosUser = json_decode($user->puntos);
            $punto_izq = 0; $punto_der = 0; $punto_rank = 0;
            $idcomision = $idcompra.'20';
            $tipo_comision = 'Puntos Binarios';
            if ($side == 'I') {
                $punto_izq = $puntos;
                $puntosUser->binario_izq = ((float) $puntosUser->binario_izq + (float) $puntos);
            }elseif ($side == 'D') {
                $punto_der = $puntos;
                $puntosUser->binario_der = ((float) $puntosUser->binario_der + (float) $puntos);
            }else{
                $tipo_comision = 'Puntos Rango';
                $punto_rank = $puntos;
                $puntosUser->rank = ((float) $puntosUser->rank + (float) $puntos);
                $idcomision = $idcompra.'30';
            }

            $user->puntos = json_encode($puntosUser);

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

                $rentabilidadActiva = $this->checkstatusRentabilidad($iduser);

                if ($rentabilidadActiva == 1) {
                    $datos = [
                        'iduser' => $iduser,
                        'usuario' => $user->display_name,
                        'descripcion' => $concepto,
                        'puntos' => $punto_rank,
                        'puntosI' => $punto_izq,
                        'puntosD' => $punto_der,
                        'email_referred' => $referred_email,
                        'descuento' => 0,
                        'debito' => 0,
                        'credito' => 0,
                        'balance' => 0,
                        'tipotransacion' => 2
                    ];
                    $this->wallet->saveWallet($datos);
                    $user->save();
                }
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
            $user = User::find($iduser);
            $puntos = [
                'binario_izq' => 0,
                'binario_der' => 0,
                'rank' => 0,
            ];
            if (empty($user->puntos)) {
                User::where('ID', $iduser)->update(['puntos' => json_encode($puntos)]);
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

    /**
     * Permite procesar las rentabilida obtenida por los usuarios
     *
     * @param Request $request
     * @return void
     */
    public function process_rentabilidad(Request $request)
    {
        $validate = $request->validate([
            'porcentage' => 'required'
        ]);

        try {
            if($validate){
                $porcentaje = $request->porcentage;
                $ordenes = $this->funciones->getAllComprasRentabilidad();
                foreach ($ordenes as $orden) {
                    $user = User::find($orden['idusuario']);
                    if (!empty($user)) {
                        foreach ($orden['productos'] as $producto) {
                            $this->saveRentabilidad($orden['idcompra'], $orden['idusuario'], $producto, $porcentaje, $orden['tipo_activacion']);
                        }
                    }
                }
                return redirect()->route('index')->with('msj', 'Rentabilidad pagada con exito');
            }
        } catch (\Throwable $th) {
            dd($th);
        }
    }

    
    /**
     * Permite actualizar las rentabilidades
     *
     * @param integer $idorden
     * @param integer $iduser
     * @param array $paquete
     * @param double $porcentaje
     * @param string $tipo_cobro
     * @return void
     */
    public function saveRentabilidad(int $idorden, int $iduser, array $paquete, $porcentaje, string $tipo_cobro)
    {
        $checkRentabilidad = DB::table('log_rentabilidad')->where([
            ['iduser', '=', $iduser],
            ['idcompra', '=', $idorden],
            ['idproducto', '=', $paquete['idproducto']]
        ])->first();

        $porc = ($porcentaje / 100);
        $ganado = ($paquete['precio'] * $porc);
        $balance = $ganado;
        $idRentabilidad = 0;
        $finalizado = 0;

        if ($checkRentabilidad == null) {
            $detallaPaquete = [
                'nombre' => $paquete['nombre'],
                'img' => $paquete['img2']
            ];
            $limite = ($paquete['precio'] * 2);
            $progreso = (($ganado * 100) / $limite);

            $dataRentabilidad = [
                'iduser' => $iduser,
                'idcompra' => $idorden,
                'idproducto' => $paquete['idproducto'],
                'detalles_producto' => json_encode($detallaPaquete),
                'precio' => $paquete['precio'],
                'limite' => $limite,
                'ganado' => $ganado,
                'progreso' => $progreso,
                'nivel_minimo_cobro' => ($tipo_cobro == 'Manual') ? 7 : 0,
            ];

            $idRentabilidad = DB::table('log_rentabilidad')->insertGetId($dataRentabilidad);
        }else{
            $totalGanado = ($checkRentabilidad->ganado + $ganado);
            $finalizacion = 0;
            if ($totalGanado >= $checkRentabilidad->limite) {
                if ($checkRentabilidad->ganado < $checkRentabilidad->limite) {
                    $totalGanado = $checkRentabilidad->limite;
                    $ganado = ($totalGanado - $checkRentabilidad->ganado);
                }else{
                    $finalizacion = 1;
                    $finalizado = 1;
                }
            }
            if ($finalizacion == 0) {    
                $progreso = (($totalGanado / $checkRentabilidad->limite) * 100);
                $balance = ($totalGanado - $checkRentabilidad->retirado);
                $dataRentabilidad = [
                    'ganado' => $totalGanado,
                    'progreso' => $progreso,
                    'nivel_minimo_cobro' => ($tipo_cobro == 'Manual') ? 7 : 0,
                    'balance' => $balance
                ];
                DB::table('log_rentabilidad')->where('id', $checkRentabilidad->id)->update($dataRentabilidad);
                $idRentabilidad = $checkRentabilidad->id;
            }
        }

        $user = User::find($iduser);

        $dataLogRentabilidadPay = [
            'iduser' => $iduser,
            'id_log_renta' => $idRentabilidad,
            'porcentaje' => $porcentaje,
            'debito' => $ganado,
            'balance' => $balance,
            'fecha_pago' => Carbon::now(),
            'concepto' => 'Rentabilidad pagada de la compra '.$idorden.', del producto '.$paquete['nombre'].', al usuario '.$user->display_name
        ];

        if ($finalizado == 0) {
            DB::table('log_rentabilidad_pay')->insert($dataLogRentabilidadPay);
        }
    }

    /**
     * Permite pagar la rentabilida por medio del bono binario
     *
     * @param integer $iduser
     * @param float $bono
     * @return void
     */
    public function saveRentabilidaBono($iduser, $bono, $concepto)
    {
        $checkRentabilidad = DB::table('log_rentabilidad')->where([
            ['iduser', '=', $iduser],
            ['progreso', '<', 100]
        ])->first();
        if ($checkRentabilidad != null) {
            $ganado = $bono;
            $balance = $ganado;
            $idRentabilidad = $checkRentabilidad->id;
            $finalizado = 0;

            $totalGanado = ($checkRentabilidad->ganado + $ganado);
            $finalizacion = 0;
            if ($totalGanado >= $checkRentabilidad->limite) {
                if ($checkRentabilidad->ganado < $checkRentabilidad->limite) {
                    $totalGanado = $checkRentabilidad->limite;
                    $ganado = ($totalGanado - $checkRentabilidad->ganado);
                }else{
                    $finalizacion = 1;
                    $finalizado = 1;
                }
            }
            if ($finalizacion == 0) {    
                $progreso = (($totalGanado / $checkRentabilidad->limite) * 100);
                $balance = ($totalGanado - $checkRentabilidad->retirado);
                $dataRentabilidad = [
                    'ganado' => $totalGanado,
                    'progreso' => $progreso,
                    'balance' => $balance
                ];
                DB::table('log_rentabilidad')->where('id', $checkRentabilidad->id)->update($dataRentabilidad);
            }

            $user = User::find($iduser);

            $dataLogRentabilidadPay = [
                'iduser' => $iduser,
                'id_log_renta' => $idRentabilidad,
                'porcentaje' => 0,
                'debito' => $ganado,
                'balance' => $balance,
                'fecha_pago' => Carbon::now(),
                'concepto' => 'Rentabilidad pagada por medio del '.$concepto.' , al usuario '.$user->display_name
            ];

            if ($finalizado == 0) {
                DB::table('log_rentabilidad_pay')->insert($dataLogRentabilidadPay);
            }
        }
    }

    /**
     * Permite registrar las paquetes comprados en la tabla de rentabilidad
     *
     * @param integer $iduser
     * @return void
     */
    public function registePackageToRentabilizar($iduser)
    {
        $ordenes = $this->funciones->getInforShopping($iduser);
        foreach ($ordenes as $orden) {
            foreach ($orden['productos'] as $paquete) {

                $checkRentabilidad = DB::table('log_rentabilidad')->where([
                    ['iduser', '=', $iduser],
                    ['idcompra', '=', $orden['idcompra']],
                    ['idproducto', '=', $paquete['idproducto']]
                ])->first();

                if ($checkRentabilidad == null) {
                    $detallaPaquete = [
                        'nombre' => $paquete['nombre'],
                        'img' => $paquete['img2']
                    ];
                    $limite = ($paquete['precio'] * 2);
        
                    $dataRentabilidad = [
                        'iduser' => $iduser,
                        'idcompra' => $orden['idcompra'],
                        'idproducto' => $paquete['idproducto'],
                        'detalles_producto' => json_encode($detallaPaquete),
                        'precio' => $paquete['precio'],
                        'limite' => $limite,
                        'ganado' => 0,
                        'progreso' => 0,
                        'nivel_minimo_cobro' => ($orden['tipo_activacion'] == 'Manual') ? 7 : 0,
                    ];
    
                    DB::table('log_rentabilidad')->insert($dataRentabilidad);
                }
            }
        }
    }

    /**
     * Permite verificar si el usuario tiene un paquete activo o ya cerrado
     *
     * @param integer $iduser
     * @return integer
     */
    public function checkstatusRentabilidad($iduser): int
    {
        $result = 0;
        $check = DB::table('log_rentabilidad')->where([
            ['iduser', '=', $iduser],
            ['progreso', '<', 100]
        ])->first();
        if ($check != null) {
            $result = 1;
        }
        return $result;
    }

    /**
     * Permite reiniciar los bonos binarios cada mes
     *
     * @param integer $id
     * @return integer
     * @return void
     */
    public function cronjobBinario()
    {
        try {
            $users = DB::table('wp_users')->where('status', '=', 1)->get();
            foreach ($users as $p) {
                $jsond = json_decode($p->puntos);
                $puntos = [
                    'binario_izq' => 0,
                    'binario_der' => 0,
                    'rank' => $jsond->rank,
                ];
                $dataid = $p->ID;
                DB::table('wp_users')->where('ID', $dataid)->update(['puntos' => json_encode($puntos)]);
            }

        } catch (\Throwable $th) {
            dd($th);
        }
    }

}