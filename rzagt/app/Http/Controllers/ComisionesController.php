<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\User; 
use App\Commission; 
use App\Http\Controllers\IndexController;
use App\Http\Controllers\WalletController;
use App\Wallet;

use function GuzzleHttp\json_decode;

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
                    // $user = User::find($iduser);
                    // $user->wallet_amount = ($user->wallet_amount + $totalComision);
                    // $user->save();
                    // $datos = [
                    //     'iduser' => $iduser,
                    //     'usuario' => $user->display_name,
                    //     'descripcion' => $concepto,
                    //     'puntos' => 0,
                    //     'puntosI' => 0,
                    //     'puntosD' => 0,
                    //     'email_referred' => $referred_email,
                    //     'descuento' => 0,
                    //     'debito' => $totalComision,
                    //     'credito' => 0,
                    //     'balance' => $user->wallet_amount,
                    //     'tipotransacion' => 2
                    // ];
                    // $this->wallet->saveWallet($datos);

                    $newConepto = $tipo_comision.' - '.$concepto;
                    
                    $this->saveRentabilidaBono($iduser, $totalComision, $newConepto, $referred_email);
                    
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
                    if ($compra['idcompra'] != 5964 && $compra['idcompra'] != 6571) {
                        $sponsors = $this->funciones->getSponsor($compra['idusuario'], [], 0, 'ID', 'referred_id');
                        if (!empty($sponsors)) {
                            foreach ($sponsors as $sponsor) {
                                if ($compra['idusuario'] != $sponsor->ID) {
                                    if ($sponsor->nivel == 1) {
                                        $userReferido = User::find($compra['idusuario']);

                                        $totalCompra = ($compra['total'] - $this->getValueSub($compra['idusuario']));
                                        $pagar = ($totalCompra * 0.10);
                                        // if ($compra['idcompra'] == 6187) {
                                        //     dd($totalCompra, $compra['total'], $this->getValueSub($compra['idusuario']), $pagar);
                                        // }
                                        
                                        $concepto = 'N° '.$compra['idcompra'].' - '.$userReferido->display_name;
                                        if ($pagar > 0) {
                                            $this->guardarComision($sponsor->ID, $compra['idcompra'], $pagar, $userReferido->user_email, 1, $concepto, 'Bono Directo');
                                        }
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
                    if ($compra['idcompra'] != 5964 && $compra['idcompra'] != 6571) {
                        $sponsors = $this->funciones->getSponsor($compra['idusuario'], [], 0, 'ID', 'referred_id');
                        if (!empty($sponsors)) {
                            foreach ($sponsors as $sponsor) {           
                                $paquete = null;
                                if ($sponsor->paquete != null) {
                                    $paquete = json_decode($sponsor->paquete);
                                }                     
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
                                        $concepto = 'N° '.$compra['idcompra'].' - '.$userReferido->display_name;
                                        if ($pagar > 0) {
                                            $this->guardarComision($sponsor->ID, $idcomision, $pagar, $userReferido->user_email, 1, $concepto, 'Bono Indirecto');
                                        }
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
                    $concepto = '('.$pagar.')';
                    $this->guardarComision($user->ID, $idcomision, $totalcomision, $user->user_email, 0, $concepto, 'Bono Binario');
                    $this->bonoConstrucion($user->ID, $totalcomision);
                    $concepto = 'Puntos Rango, Obtenido por el pago del Bono Binario del dia'.$fecha->format('Y-m-d');
                    $this->savePoints($pagar, $user->ID, $concepto, 'R', $idcomision, 1, $user->user_email);
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
                $totalCompra = 0;
                $cantiOrdenes = count($compras);
                foreach ($compras as $compra) {
                    if ($cantiOrdenes > 1) {
                        $totalCompra = $compras[($cantiOrdenes - 1)]['total'];
                    }
                    $totalCompra = ($compra['total'] - $totalCompra);
                    $sponsors = $this->funciones->getSponsor($compra['idusuario'], [], 0, 'ID', 'referred_id');
                    if (!empty($sponsors)) {
                        $userReferido = User::find($compra['idusuario']);
                        $side = $userReferido->ladomatrix;
                        $concepto = 'Puntos Rango, Obtenido por el usuario '.$userReferido->display_name.', por la compra'.$compra['idcompra'];
                        foreach ($sponsors as $sponsor) {
                            if ($sponsor->nivel > 0 && $totalCompra > 0) {
                                $this->savePoints($totalCompra, $sponsor->ID, $concepto, 'R', $compra['idcompra'], $sponsor->nivel, $userReferido->user_email);
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
                            // if ($this->verificarBinarioActivo($sponsor->ID) == 1) {
                                if ($sponsor->nivel > 0) {
                                    $totalCompra = ($compra['total'] - $this->getValueSub($compra['idusuario']));
                                    $this->savePoints($totalCompra, $sponsor->ID, $concepto, $side, $compra['idcompra'], $sponsor->nivel, $userReferido->user_email);
                                }
                            // }else{
                            //     $this->savePoints(0, $sponsor->ID, $concepto, $side, $compra['idcompra'], $sponsor->nivel, $userReferido->user_email);
                            // }
                            $side = $sponsor->ladomatriz;
                        }
                    }
                }
            }
        } catch (\Throwable $th) {
            dd($th);
        }
    }

    /**
     * Permite obtener el valor a restar
     *
     * @param integer $iduser
     * @return float
     */
    public function getValueSub(int $iduser): float
    {
        $resta = 0;

        $compras = $this->funciones->getInforShopping($iduser);
        $total = count($compras);
        // foreach ($compras as $compra ) {
        //     if ($compra['tipo_activacion'] != 'Manual') {
        //         $total++;
        //     }
        // }
        if ($total > 1) {
            $resta = $compras[$total-2]['total'];
        }

        return $resta;
    }

    /**
     * Permite verificar si el usuario puede cobrar puntos o no 
     *
     * @param integer $iduser
     * @return integer
     */
    public function verificarBinarioActivo(int $iduser): int
    {
        $result = 0;

        $binarioIzq = $binarioDer = 0;
        $directos = User::where('referred_id', $iduser)->get();
        foreach ($directos as $direct) {
            if ($direct->status == 1) {
                $ordenes = $this->funciones->getInforShopping($direct->ID);    
                // if ($iduser == 499) {
                //     dump($ordenes);
                // }
                foreach ($ordenes as $orden) {
                    if ($orden['tipo_activacion'] != 'Manual') {
                        if ($direct->ladomatrix == 'D') {
                            $binarioDer = 1;
                        }
                        if ($direct->ladomatrix == 'I') {
                            $binarioIzq = 1;
                        }
                    }
                }
            }
        }

        if ($binarioDer == 1 && $binarioIzq == 1) {
            $result = 1;   
        }

        return $result;
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

                if ($rentabilidadActiva == 1 && $puntos > 0) {
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
                        $this->registePackageToRentabilizar($orden['idusuario']);
                        foreach ($orden['productos'] as $producto) {
                            $this->saveRentabilidad($orden['idcompra'], $orden['idusuario'], $producto, $porcentaje, $orden['tipo_activacion']);
                        }
                    }
                }
                return redirect()->route('admin.index')->with('msj', 'Rentabilidad pagada con exito');
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
    public function saveRentabilidad(int $idorden, int $iduser, array $paquete, float $porcentaje, string $tipo_cobro)
    {
        $checkRentabilidad = DB::table('log_rentabilidad')->where([
            ['iduser', '=', $iduser],
            ['idcompra', '=', $idorden],
            ['idproducto', '=', $paquete['idproducto']],
            // ['nivel_minimo_cobro', '=', 0]
        ])->first();

        $porc = ($porcentaje / 100);
        $ganado = ($paquete['precio'] * $porc);
        $balance = $ganado;
        $idRentabilidad = 0;
        $finalizado = 0;

        // if ($checkRentabilidad == null) {
            // $detallaPaquete = [
            //     'nombre' => $paquete['nombre'],
            //     'img' => $paquete['img2']
            // ];
            // $limite = ($paquete['precio'] * 2);
            // $progreso = (($ganado * 100) / $limite);

            // $dataRentabilidad = [
            //     'iduser' => $iduser,
            //     'idcompra' => $idorden,
            //     'idproducto' => $paquete['idproducto'],
            //     'detalles_producto' => json_encode($detallaPaquete),
            //     'precio' => $paquete['precio'],
            //     'limite' => $limite,
            //     'ganado' => $ganado,
            //     'progreso' => $progreso,
            //     'nivel_minimo_cobro' => ($tipo_cobro == 'Manual') ? 7 : 0,
            // ];

            // $idRentabilidad = DB::table('log_rentabilidad')->insertGetId($dataRentabilidad);
        if($checkRentabilidad != null){

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

                $user = User::find($iduser);
                $user->wallet_amount = ($user->wallet_amount + $ganado);
                $user->save();
        
                $dataLogRentabilidadPay = [
                    'iduser' => $iduser,
                    'id_log_renta' => $idRentabilidad,
                    'porcentaje' => $porcentaje,
                    'debito' => $ganado,
                    'balance' => $user->wallet_amount,
                    'fecha_pago' => Carbon::now(),
                    'concepto' => 'Rentabilidad pagada de la compra '.$idorden.', del producto '.$paquete['nombre'].', al usuario '.$user->display_name
                ];
        
                $concepto = 'Utilidad ('.$porcentaje.'%)';
                $datosComisions = [
                    'iduser' => $iduser,
                    'usuario' => $user->display_name,
                    'descripcion' => $concepto,
                    'puntos' => 0,
                    'puntosI' => 0,
                    'puntosD' => 0,
                    'email_referred' => $user->user_email,
                    'descuento' => 0,
                    'debito' => $ganado,
                    'credito' => 0,
                    'balance' => $user->wallet_amount,
                    'tipotransacion' => 2
                ];
                if ($iduser == 1000 || $iduser == 959 || $iduser == 709) {
                    dump($finalizado);
                }
                if ($finalizado == 0) {
                    DB::table('log_rentabilidad_pay')->insert($dataLogRentabilidadPay);
                    $this->wallet->saveWallet($datosComisions);
                }
            }
        }
    }

    /**
     * Permite pagar la rentabilida por medio del bono binario
     *
     * @param integer $iduser
     * @param float $bono
     * @param string $concepto
     * @param string $referido
     * @return void
     */
    public function saveRentabilidaBono($iduser, $bono, $concepto, $referido)
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
            $user->wallet_amount = ($user->wallet_amount + $ganado);

            $dataLogRentabilidadPay = [
                'iduser' => $iduser,
                'id_log_renta' => $idRentabilidad,
                'porcentaje' => 0,
                'debito' => $ganado,
                'balance' => $balance,
                'fecha_pago' => Carbon::now(),
                'concepto' => 'Rentabilidad pagada por medio del '.$concepto.' , al usuario '.$user->display_name
            ];

            $datosComisions = [
                'iduser' => $iduser,
                'usuario' => $user->display_name,
                'descripcion' => $concepto,
                'puntos' => 0,
                'puntosI' => 0,
                'puntosD' => 0,
                'email_referred' => $referido,
                'descuento' => 0,
                'debito' => $ganado,
                'credito' => 0,
                'balance' => $user->wallet_amount,
                'tipotransacion' => 2
            ];

            if ($finalizado == 0) {
                $user->save();
                DB::table('log_rentabilidad_pay')->insert($dataLogRentabilidadPay);
                $this->wallet->saveWallet($datosComisions);
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

                $checkRentabilidad1 = DB::table('log_rentabilidad')->where([
                    ['iduser', '=', $iduser],
                    ['progreso', '=', 100]
                ])->orderBy('id', 'desc')->first();

                $checkRentabilidad = DB::table('log_rentabilidad')->where([
                    ['iduser', '=', $iduser],
                ])->first();
                
                $compraN = 0;
                if ($checkRentabilidad1 != null) {
                    if ($orden['idcompra'] > $checkRentabilidad1->idcompra) {
                        $compraN = 1;
                        $checkRentabilidad2 = DB::table('log_rentabilidad')->where([
                            ['iduser', '=', $iduser],
                            ['idcompra', '=', $orden['idcompra']],
                            ['progreso', '<', 100]
                        ])->orderBy('id', 'desc')->first();
                        if ($checkRentabilidad2 != null) {
                            $compraN = 0;
                        }
                    }
                }
                if ($checkRentabilidad == null) {
                    $compraN = 1;
                }

                if ($compraN == 1) {
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

                    DB::table('log_rentabilidad')->where([
                        ['iduser', '=', $iduser],
                    ])->update(['progreso' => 100]);
    
                    DB::table('log_rentabilidad')->insert($dataRentabilidad);
                }else{
                    $checkRentabilidad1 = DB::table('log_rentabilidad')->where([
                        ['iduser', '=', $iduser],
                        ['progreso', '<', 100]
                    ])->first();


                    $actualizar = 0;
                    if ($checkRentabilidad1 != null) {
                        if ($orden['idcompra'] > $checkRentabilidad1->idcompra) {
                            $actualizar = 1;
                        }
                    }
                    
                    if ($actualizar == 1) {
                        $detallaPaquete = [
                            'nombre' => $paquete['nombre'],
                            'img' => $paquete['img2']
                        ];
                        $limite = ($paquete['precio'] * 2);
                        $limite = ($limite == 0) ? 1 : $limite;
                        $progreso = (($checkRentabilidad1->ganado / $limite) * 100);
            
                        $dataRentabilidad = [
                            'idcompra' => $orden['idcompra'],
                            'idproducto' => $paquete['idproducto'],
                            'detalles_producto' => json_encode($detallaPaquete),
                            'precio' => $paquete['precio'],
                            'limite' => $limite,
                            'progreso' => $progreso,
                        ];

                        DB::table('log_rentabilidad')->where('id', $checkRentabilidad1->id)->update($dataRentabilidad);
                    }
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

    /**
     * permite arreglar las billeteras de algunos usuarios
     *
     * @return void
     */
    public function arreglarBilletera()
    {
        try {
            $usuarios =  $this->arregloUserArreglar();
            foreach ($usuarios as $usuario ) {
                $bono = $usuario['total'];
                $user2 = User::where('user_email', $usuario['correo'])->first();
                $iduser = $user2->ID;
                $concepto = $usuario['concepto'];

                if ($concepto == 'Puntos por derrames') {
                    $this->savePoints($bono, $iduser, $concepto, $usuario['side'], $iduser, 1, 'Sistema');
                    if ($usuario['side'] == 'R') {
                        $jsond = json_decode($user2->puntos);
                        $puntos = [
                            'binario_izq' => ($jsond->binario_izq - $bono),
                            'binario_der' => ($jsond->binario_der - $bono),
                            'rank' => $jsond->rank,
                        ];
                        // $user2->puntos = json_encode($puntos);

                        // $user2->save();
                        DB::table('wp_users')->where('ID', $user2->ID)->update(['puntos' => json_encode($puntos)]);
                    }
                }else{
                    $checkRentabilidad = DB::table('log_rentabilidad')->where([
                        ['iduser', '=', $iduser],
                        ['progreso', '<', 100]
                    ])->first();
    
                    if ($checkRentabilidad != null) {
                        $ganado = $bono;
                        $balance = $ganado;
                        $idRentabilidad = $checkRentabilidad->id;
                        $finalizado = 0;
    
                        $debito = 0;
                        $credito = 0;
                        $totalRetirado = 0;
            
                        if ($usuario['accion'] == 'sumar') {
                            $totalGanado = ($checkRentabilidad->ganado + $ganado);
                        } elseif($usuario['accion'] == 'restar') {
                            $totalGanado = ($checkRentabilidad->ganado - $ganado);
                            $totalRetirado = ($checkRentabilidad->retirado + $ganado);
                        }
                        
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
                                'retirado' => $totalRetirado,
                                'progreso' => $progreso,
                                'balance' => $balance
                            ];
                            DB::table('log_rentabilidad')->where('id', $checkRentabilidad->id)->update($dataRentabilidad);
                        }
            
                        $user = User::find($iduser);
                        // $user->wallet_amount = ($user->wallet_amount + $ganado);
                        if ($usuario['accion'] == 'sumar') {
                            $user->wallet_amount = ($user->wallet_amount + $ganado);
                            $debito = $ganado;
                        } elseif($usuario['accion'] == 'restar') {
                            $user->wallet_amount = ($user->wallet_amount - $ganado);
                            $credito = $ganado;
                        }
            
                        $dataLogRentabilidadPay = [
                            'iduser' => $iduser,
                            'id_log_renta' => $idRentabilidad,
                            'porcentaje' => 0,
                            'debito' => $debito,
                            'credito' => $credito,
                            'balance' => $balance,
                            'fecha_pago' => Carbon::now(),
                            'concepto' => 'Rentabilidad pagada por medio del '.$concepto.' , al usuario '.$user->display_name
                        ];
            
                        $datosComisions = [
                            'iduser' => $iduser,
                            'usuario' => $user->display_name,
                            'descripcion' => $concepto,
                            'puntos' => 0,
                            'puntosI' => 0,
                            'puntosD' => 0,
                            'email_referred' => 'Sistema',
                            'descuento' => 0,
                            'debito' => $debito,
                            'credito' => $credito,
                            'balance' => $user->wallet_amount,
                            'tipotransacion' => 2
                        ];
            
                        if ($finalizado == 0) {
                            $user->save();
                            DB::table('log_rentabilidad_pay')->insert($dataLogRentabilidadPay);
                            $this->wallet->saveWallet($datosComisions);
                        }
                    }
                }
            }
        } catch (\Throwable $th) {
            dd($th);
        }
    }

    /**
     * Los usuarios y las cosas que tienes que hacer
     *
     * @return array
     */
    public function arregloUserArreglar(): array
    {
        return [
            // [
            //     'correo' => 'entherfoundworld@gmail.com',
            //     'accion' => 'sumar',
            //     'concepto' => 'Retroactivo de rentabilidad',
            //     'total' => 23.16
            // ],
            // [
            //     'correo' => 'entherfoundworld@gmail.com',
            //     'accion' => 'restar',
            //     'concepto' => 'Reajuste de bono binario',
            //     'total' => 4
            // ],
            // [
            //     'correo' => 'multiclickworld@gmail.com',
            //     'accion' => 'sumar',
            //     'concepto' => 'Retroactivo de rentabilidad',
            //     'total' => 11.58
            // ],
            // [
            //     'correo' => 'equipo.mundial2018@gmail.com',
            //     'accion' => 'sumar',
            //     'concepto' => 'Retroactivo de rentabilidad',
            //     'total' => 11.58
            // ],
            // [
            //     'correo' => 'jtan17@hotmail.com',
            //     'accion' => 'restar',
            //     'concepto' => 'Reajuste de bono binario',
            //     'total' => 100
            // ],
            // [
            //     'correo' => 'Jessyeme02@gmail.com',
            //     'accion' => 'sumar',
            //     'concepto' => 'Retroactivo de bono binario',
            //     'total' => 576
            // ],
            // [
            //     'correo' => 'Juan.daniel0521@gmail.com',
            //     'accion' => 'restar',
            //     'concepto' => 'Reajuste de bono binario',
            //     'total' => 90
            // ],
            // [
            //     'correo' => 'Jogonzalezh0712@gmail.com',
            //     'accion' => 'sumar',
            //     'concepto' => 'Retroactivo de rentabilidad',
            //     'total' => 17.4
            // ],
            // [
            //     'correo' => 'nancyloaizandr@gmail.com',
            //     'accion' => 'sumar',
            //     'concepto' => 'Retroactivo de rentabilidad',
            //     'total' => 42
            // ],
            // [
            //     'correo' => 'quinino49@hotmail.com',
            //     'accion' => 'sumar',
            //     'concepto' => 'Bono Directo de Laura Luquin',
            //     'total' => 100
            // ],
            // [
            //     'correo' => 'Paulivanegas26@gmail.com',
            //     'accion' => 'sumar',
            //     'concepto' => 'Bono Binario',
            //     'total' => 522.5
            // ],
            // [
            //     'correo' => 'Paulivanegas26@gmail.com',
            //     'accion' => 'sumar',
            //     'concepto' => 'Bono Indirectos',
            //     'total' => 61
            // ],
            // [
            //     'correo' => 'Jessyeme02@gmail.com',
            //     'accion' => 'sumar',
            //     'concepto' => 'Ajuste de igualacion',
            //     'total' => 1405
            // ],
            // [
            //     'correo' => 'sebasjaratabares@gmail.com',
            //     'accion' => 'sumar',
            //     'concepto' => 'Ajuste de igualacion',
            //     'total' => 150
            // ],
            // [
            //     'correo' => 'Jessyeme02@gmail.com',
            //     'accion' => 'sumar',
            //     'concepto' => 'Ajuste de igualacion',
            //     'total' => 50
            // ],
            // [
            //     'correo' => 'leutarorodsman@gmail.com',
            //     'accion' => 'sumar',
            //     'concepto' => 'Ajuste de igualacion',
            //     'total' => 738
            // ],
            // [
            //     'correo' => 'nancyloaizandr@gmail.com',
            //     'accion' => 'sumar',
            //     'concepto' => 'Bono Directo',
            //     'total' => 50
            // ],
            // [
            //     'correo' => 'sebasjaratabares@gmail.com',
            //     'accion' => 'sumar',
            //     'concepto' => 'Puntos por derrames',
            //     'total' => 100000,
            //     'side' => 'D'
            // ],
            // [
            //     'correo' => 'master5@royalgreen.company',
            //     'accion' => 'restar',
            //     'concepto' => 'Binario mal pagado',
            //     'total' => 474
            // ],
            // [
            //     'correo' => 'master5@royalgreen.company',
            //     'accion' => 'restar',
            //     'concepto' => 'Indirecto mal pagado',
            //     'total' => 44.5
            // ],
            // [
            //     'correo' => 'jessyeme02@gmail.com',
            //     'accion' => 'sumar',
            //     'concepto' => 'Puntos por derrames',
            //     'total' => 37790,
            //     'side' => 'R'
            // ],
            // [
            //     'correo' => 'leutarorodsman@gmail.com',
            //     'accion' => 'sumar',
            //     'concepto' => 'Puntos por derrames',
            //     'total' => 25700,
            //     'side' => 'R'
            // ],
            // [
            //     'correo' => 'master5@royalgreen.company',
            //     'accion' => 'sumar',
            //     'concepto' => 'Puntos por derrames',
            //     'total' => 19600,
            //     'side' => 'R'
            // ],
            // [
            //     'correo' => 'info@depilacionfacil.com.co',
            //     'accion' => 'sumar',
            //     'concepto' => 'Puntos por derrames',
            //     'total' => 21400,
            //     'side' => 'R'
            // ],
            // [
            //     'correo' => 'leutarorodsman+13@gmail.com',
            //     'accion' => 'sumar',
            //     'concepto' => 'Puntos por derrames',
            //     'total' => 9150,
            //     'side' => 'R'
            // ],
            // [
            //     'correo' => 'Juan.daniel0521@gmail.com',
            //     'accion' => 'sumar',
            //     'concepto' => 'Puntos por derrames',
            //     'total' => 3560,
            //     'side' => 'R'
            // ],
            // [
            //     'correo' => 'sebasjaratabares@gmail.com',
            //     'accion' => 'sumar',
            //     'concepto' => 'Puntos por derrames',
            //     'total' => 1500,
            //     'side' => 'R'
            // ],
            // [
            //     'correo' => 'jtan17@hotmail.com',
            //     'accion' => 'sumar',
            //     'concepto' => 'Puntos por derrames',
            //     'total' => 3000,
            //     'side' => 'R'
            // ],
            // [
            //     'correo' => 'Paulivanegas26@gmail.com',
            //     'accion' => 'sumar',
            //     'concepto' => 'Puntos por derrames',
            //     'total' => 2115,
            //     'side' => 'R'
            // ],
            // [
            //     'correo' => 'valentinaescritora2020@gmail.com',
            //     'accion' => 'sumar',
            //     'concepto' => 'Puntos por derrames',
            //     'total' => 1700,
            //     'side' => 'R'
            // ],
            // [
            //     'correo' => 'farly1993z3@gmail.com',
            //     'accion' => 'sumar',
            //     'concepto' => 'Puntos por derrames',
            //     'total' => 1200,
            //     'side' => 'R'
            // ],
            // [
            //     'correo' => 'diosaexitosa2017@gmail.com',
            //     'accion' => 'sumar',
            //     'concepto' => 'Puntos por derrames',
            //     'total' => 1250,
            //     'side' => 'R'
            // ],
            // [
            //     'correo' => 'leutarorodsman+19@gmail.com',
            //     'accion' => 'sumar',
            //     'concepto' => 'Puntos por derrames',
            //     'total' => 1200,
            //     'side' => 'R'
            // ],
            // [
            //     'correo' => 'alejandraserranosanchez14@gmail.com',
            //     'accion' => 'sumar',
            //     'concepto' => 'Puntos por derrames',
            //     'total' => 1050,
            //     'side' => 'R'
            // ],
            // [
            //     'correo' => 'vicvillalba19@gmail.com',
            //     'accion' => 'sumar',
            //     'concepto' => 'Puntos por derrames',
            //     'total' => 700,
            //     'side' => 'R'
            // ],
            // [
            //     'correo' => 'royalgreenchile1@gmail.com',
            //     'accion' => 'sumar',
            //     'concepto' => 'Puntos por derrames',
            //     'total' => 500,
            //     'side' => 'R'
            // ],
            // [
            //     'correo' => 'Platiadrian@gmail.com',
            //     'accion' => 'sumar',
            //     'concepto' => 'Puntos por derrames',
            //     'total' => 300,
            //     'side' => 'R'
            // ],
            // [
            //     'correo' => 'doncarlosalfaro@gmail.com',
            //     'accion' => 'sumar',
            //     'concepto' => 'Puntos por derrames',
            //     'total' => 150,
            //     'side' => 'R'
            // ],
            // [
            //     'correo' => 'leutarorodsman+20@gmail.com',
            //     'accion' => 'sumar',
            //     'concepto' => 'Puntos por derrames',
            //     'total' => 100,
            //     'side' => 'R'
            // ],
            // [
            //     'correo' => 'adelito62@hotmail.com',
            //     'accion' => 'sumar',
            //     'concepto' => 'Puntos por derrames',
            //     'total' => 100,
            //     'side' => 'R'
            // ],
            // [
            //     'correo' => 'Platiadrian@gmail.com',
            //     'accion' => 'sumar',
            //     'concepto' => 'Puntos por derrames',
            //     'total' => 300,
            //     'side' => 'R'
            // ],
            // [
            //     'correo' => 'leutarorodsman+16@gmail.com',
            //     'accion' => 'sumar',
            //     'concepto' => 'Puntos por derrames',
            //     'total' => 45,
            //     'side' => 'R'
            // ],
            // [
            //     'correo' => 'Sharkprodigital@gmail.com',
            //     'accion' => 'sumar',
            //     'concepto' => 'Puntos por derrames',
            //     'total' => 50,
            //     'side' => 'R'
            // ],
            // [
            //     'correo' => 'Apariclub@outlook.com',
            //     'accion' => 'sumar',
            //     'concepto' => 'Puntos por derrames',
            //     'total' => 50,
            //     'side' => 'R'
            // ],
            [
                'correo' => 'Paulivanegas26@gmail.com',
                'accion' => 'sumar',
                'concepto' => 'Puntos por derrames',
                'total' => 12535,
                'side' => 'R'
            ],
        ];
    }

    // /**
    //  * Permite borrar todos los registros de puntos 
    //  *
    //  * @return void
    //  */
    // public function borrarPuntos()
    // {
    //     $compras = DB::table('wp_posts')
    //                 ->select('*')
    //                 ->where([
    //                     ['post_type', '=', 'shop_order'],
    //                     ['post_status', '=', 'wc-completed'],
    //                     ['to_ping', '=', 'Coinbase']
    //                 ])->orWhere([
    //                     ['post_type', '=', 'shop_order'],
    //                     ['post_status', '=', 'wc-completed'],
    //                     ['ID', '=', 5964]
    //                 ])
    //                 ->get();
    //     foreach ($compras as $compra) {
    //         $idcomision = $compra->ID.'20';
    //         $comision = Commission::where('compra_id', '=', $idcomision)->first();
    //         if ($comision != null) {
    //             Commission::where('compra_id', '=', $idcomision)->delete();
    //         }
    //     }

    //     $users = User::all();
    //     foreach ($users as $p) {
    //         $p = User::find($p->ID);
    //         $jsond = json_decode($p->puntos);
    //         $puntos = [
    //             'binario_izq' => 0,
    //             'binario_der' => 0,
    //             'rank' => $jsond->rank,
    //         ];
    //         // $p->puntos = json_encode($puntos);

    //         // $p->save();
    //         DB::table('wp_users')->where('ID', $p->ID)->update(['puntos' => json_encode($puntos)]);
    //     }
        

    //     Commission::where('tipo_comision', '=', 'Puntos Rango')->delete();
    //     Wallet::where('puntos', '>', 0)->delete();
    //     Wallet::where('puntosI', '>', 0)->delete();
    //     Wallet::where('puntosD', '>', 0)->delete();
    // }

    /**
     * Permite pagar los puntos nos pagados
     *
     * @return void
     */
    public function arreglarPuntosNoPagados()
    {
        try {
            $comisiones = Commission::where([
                ['tipo_comision', '=', 'Puntos Binarios'],
                ['total', '=', 0]
            ])->groupBy('compra_id')->get();
    
            foreach ($comisiones as $comision) {
                $idcompra = substr($comision->compra_id, 0, -2);
                $userReferido = User::where('user_email', '=', $comision->referred_email)->first();
                $sponsors = $this->funciones->getSponsor($userReferido->ID, [], 0, 'ID', 'position_id');
                $totalCompra = $this->funciones->getShoppingTotal($idcompra);
                if (!empty($sponsors)) {
                    $side = $userReferido->ladomatrix;
                    $concepto = 'Puntos Binarios, Obtenido por el usuario '.$userReferido->display_name.', por la compra'.$idcompra;
                    foreach ($sponsors as $sponsor) {
                        if ($sponsor->nivel > 0) {
                            $this->savePoints($totalCompra, $sponsor->ID, $concepto, $side, $idcompra.'1', $sponsor->nivel, $userReferido->user_email);
                        }
                        $side = $sponsor->ladomatriz;
                    }
                }
            }
        } catch (\Throwable $th) {
            dd($th);
        }
    }
}