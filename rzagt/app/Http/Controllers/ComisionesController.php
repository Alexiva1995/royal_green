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
use phpDocumentor\Reflection\Types\Boolean;

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
        $compras = $this->funciones->getAllCompras();
        $this->puntosBinarios($compras);
    }

    /**
     * Permite pagar las compras de los bonos directos he indirectos una sola vez
     */
    public function payBono($iduser, $idcompra)
    {
        $compra = $this->funciones->getInfoPurchase($idcompra, $iduser);
        if (!empty($compra)) {
            $compra = $compra[0];
            $this->bonoDirecto($compra);
            $this->bonoIndirecto($compra);
        }
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
                ['referred_email', '=', $referred_email]
            ])->first();

            // dump($iduser, $idcompra, $checkComision);

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
    public function bonoDirecto($compra)
    {   
        try {
            if (!empty($compra)) {
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
        } catch (\Throwable $th) {
            dd($th);
        }
    }

    /**
     * Permite Pagar el Bono Indirecto
     *
     * @return void
     */
    public function bonoIndirecto($compra)
    {
        try {
            if (!empty($compra)) {
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
                                $totalCompra = ($compra['total'] - $this->getValueSub($compra['idusuario']));
                                $pagar = ($totalCompra * $porcentaje);
                                $concepto = 'N° '.$compra['idcompra'].' - '.$userReferido->display_name;
                                if ($pagar > 0) {
                                    $this->guardarComision($sponsor->ID, $idcomision, $pagar, $userReferido->user_email, 1, $concepto, 'Bono Indirecto');
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
            $users = User::where([
                ['status', '=', 1],
                ['puntos->binario_izq', '>', 0],
                ['puntos->binario_der', '>', 0]
            ])->get();
            foreach ($users as $user) {
                // if ($user->ID == 6084) {
                //     $this->verificarBinarioActivo($user->ID);
                // }
                if ($this->verificarBinarioActivo($user->ID) == 1) {
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
                    // dump($pagar, $porcentaje);
                    if ($pagar != 0 && $porcentaje != 0) {
                        // dd('entre');
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
    public function puntosBinarios($compras)
    {
        try {
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
                                    // if ($totalCompra == 0) { 
                                    //     // dump($compra['idcompra'], $this->getValueSub($compra['idusuario'], $compra['total']));
                                    //     $totalCompra = $compra['total'];
                                    // }
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

        $rentabilidad = DB::table('log_rentabilidad')->where([
            ['iduser', '=', $iduser],
            ['progreso', '<', 100]
        ])->first();

        if ($rentabilidad != null) {
            $resta = $rentabilidad->precio_anterior;
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
     * @param string $fechaAnterior
     * @return void
     */
    public function bonoConstrucion(int $iduser, float $bonobinario, $fechaAnterior = null)
    {
        try {
            $fecha = Carbon::now();
            $sponsors = $this->funciones->getSponsor($iduser, [], 0, 'ID', 'referred_id');
            if (!empty($sponsors)) {
                foreach ($sponsors as $sponsor) {
                    if ($sponsor->nivel > 0 && $sponsor->nivel <= 11) {
                        for ($i=1; $i <= ($sponsor->rol_id-1); $i++) { 
                            $porcentaje = $this->porceNivelRango($sponsor->rol_id, $i);
                            if ($sponsor->ID == 1220 && $i == 1) {
                                $porcentaje = 0.05;
                            }
                            if ($porcentaje > 0) {
                                $userReferido = User::find($iduser);
                                $concepto = 'Bono Contrucion, Obtenido del usuario '.$userReferido->display_name;
                                $idcomision = '40'.$fecha->format('Ymd').$i;
                                if ($fechaAnterior != null) {
                                    $idcomision = '40'.$fechaAnterior.$i;
                                }
                                $totalcomision = ($bonobinario * $porcentaje);
                                // dump($sponsor->ID.' - '.$idcomision.' - '.$totalcomision.' - '.$userReferido->user_email.' - '.$i.' - '.$concepto.' - '.'Bono Construcion');
                                $this->guardarComision($sponsor->ID, $idcomision, $totalcomision, $userReferido->user_email, $i, $concepto, 'Bono Construcion');
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
            2 => 0.02, 3 => 0.02, 4 => 0.02,
            5 => 0.02, 6 => 0.02, 7 => 0.02,
            8 => 0.02, 9 => 0.02, 10 => 0.02,
            11 => 0.02, 12 => 0.02
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
                $ordenes = DB::table('log_rentabilidad')->where('progreso', '<', 100)->get();
                foreach ($ordenes as $orden) {
                    if ($this->filtrarUserRentabilidad($orden->iduser)) {
                        $user = User::find($orden->iduser);
                        if (!empty($user)) {
                            $tmp = json_decode($orden->detalles_producto);
                            $producto = [
                                'idproducto' => $orden->idproducto,
                                'nombre' => $tmp->nombre,
                                'precio' => $orden->precio
                            ];
                             $this->saveRentabilidad($orden->idcompra, $orden->iduser, $producto, $porcentaje, $orden->nivel_minimo_cobro);
                        }
                    }
                }
                return redirect()->route('admin.index')->with('msj', 'Rentabilidad pagada con exito');
            }
        } catch (\Throwable $th) {
            \Log::error('Pagar Rentabilidad ->'. $th);
            return redirect()->route('admin.index')->with('msj', 'Ocurrio un error, por favor contacte al administrador');
        }
    }

    /**
     * Permite filtrar a los usuarios que no van a cobrar rentabilidad
     *
     * @param integer $iduser
     * @return boolean
     */
    public function filtrarUserRentabilidad($iduser): bool
    {
        $result = true;
        // $arrayExcluidos = [481, 484, 770, 2364, 581, 2109];
        // if (in_array($iduser, $arrayExcluidos)) {
        //     $result = false;
        // }
        $user = User::find($iduser);
        if ($user->pay_rentabilidad == 0) {
            $result = false;
        }

        return $result;
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
            ['progreso', '<', 100]
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
                // if ($iduser == 1000 || $iduser == 959 || $iduser == 709) {
                //     dump($finalizado);
                // }
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
            // dump($iduser, $orden);
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
                        'precio_anterior'=> 0,
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
                            'precio_anterior'=> $checkRentabilidad1->precio,
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
     * @param array $usuarios
     * @return void
     */
    public function arreglarBilletera(array $usuarios)
    {
        try {
            // $usuarios =  $this->arregloUserArreglar();
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
            //     'correo' => 'sebasjaratabares@gmail.com',
            //     'accion' => 'sumar',
            //     'concepto' => 'Puntos por derrames',
            //     'total' => 100000,
            //     'side' => 'D'
            // ],
            // [
            //     'correo' => 'jessyeme02@gmail.com',
            //     'accion' => 'sumar',
            //     'concepto' => 'Puntos por derrames',
            //     'total' => 37790,
            //     'side' => 'R'
            // ],
            // [
            //     'correo' => 'silvana.saavedra96@gmail.com ',
            //     'accion' => 'restar',
            //     'concepto' => 'Saldo faltante para activacion',
            //     'total' => 15
            // ],
        ];
    }

    /**
     * permite arreglar las billeteras de algunos usuarios
     *
     * @return void
     */
    public function eliminarRegistros(int $idwallet)
    {
        try {
            $wallet = DB::table('walletlog')->where('id', $idwallet)->first();
            $bono = $wallet->debito;
            $user2 = User::where('ID', $wallet->iduser)->first();
            $iduser = $user2->ID;

            $checkRentabilidad = DB::table('log_rentabilidad')->where([
                ['iduser', '=', $iduser],
                ['progreso', '<', 100]
            ])->first();
    
                    if ($checkRentabilidad != null) {
                        $ganado = $bono;
                        $balance = $ganado;
                        $finalizado = 0;
    
                        $totalRetirado = 0;
                        $totalGanado = ($checkRentabilidad->ganado - $ganado);
                        $totalRetirado = ($checkRentabilidad->retirado + $ganado);
                        
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
                        $user->wallet_amount = ($user->wallet_amount - $ganado);
                        if ($finalizado == 0) {
                            $user->save();
                            DB::table('walletlog')->where('id', $idwallet)->delete();
                        }
                    }
        } catch (\Throwable $th) {
            dd($th);
        }
    }

    /**
     * Permite eliminar los puntos del sistema
     *
     * @param integer $idwallet
     * @return void
     */
    public function eliminarRegistrosPuntos(int $idwallet)
    {
        try {
            $wallet = DB::table('walletlog')->where('id', $idwallet)->first();
            $puntosD = $wallet->puntosD;
            $puntosI = $wallet->puntosI;
            $user2 = User::find($wallet->iduser);
            $jsond = json_decode($user2->puntos);
            $puntos = [
                'binario_izq' => ($jsond->binario_izq - $puntosI),
                'binario_der' => ($jsond->binario_der - $puntosD),
                'rank' => $jsond->rank,
            ];
            // $user2->puntos = json_encode($puntos);

            // $user2->save();
            DB::table('wp_users')->where('ID', $user2->ID)->update(['puntos' => json_encode($puntos)]);
            DB::table('walletlog')->where('id', $idwallet)->delete();
        } catch (\Throwable $th) {
            dd($th);
        }
    }

    /**
     * Recibe el correo del usuario referido que se le borraran los puntos 
     *
     * @param string $email
     * @param string $fecha
     * @return void
     */
    public function usuarioEliminarPuntos($email, $fecha = null)
    {
        $wallets = DB::table('walletlog')->where('email_referred', $email)
                    // ->whereDate('created_at', '=', '20210806')
                    ->groupBy('iduser')
                    ->get();
        foreach ($wallets as $wallet) {
            $this->eliminarRegistrosPuntos($wallet->id);
        }
    }

    /**
     * Permite recorrer las comisiones dupluicadas
     *
     * @return void
     */
    public function recorrerDuplicados()
    {
        $sql = "SELECT id, iduser, COUNT(iduser) tu, email_referred, COUNT(email_referred) te, debito FROM `walletlog` WHERE created_at >= '20210503' and debito > 0 GROUP BY email_referred, iduser, debito HAVING tu > 1 ORDER BY `walletlog`.`iduser` ASC";
        $duplicados = DB::select($sql);
        foreach ($duplicados as $dupli) {
            // $this->eliminarRegistros($dupli->id);
        }

        $sql = "SELECT id, iduser, COUNT(iduser) tu, email_referred, COUNT(email_referred) te, debito FROM `walletlog` WHERE created_at >= '20210503' and puntosD > 0 GROUP BY email_referred, iduser, debito HAVING tu > 1 ORDER BY `walletlog`.`iduser` ASC";
        $duplicados = DB::select($sql);
        foreach ($duplicados as $dupli) {
            DB::table('walletlog')->where('id', $dupli->id)->delete();
        }

        $sql = "SELECT id, iduser, COUNT(iduser) tu, email_referred, COUNT(email_referred) te, debito FROM `walletlog` WHERE created_at >= '20210503' and puntosI > 0 GROUP BY email_referred, iduser, debito HAVING tu > 1 ORDER BY `walletlog`.`iduser` ASC";
        $duplicados = DB::select($sql);
        foreach ($duplicados as $dupli) {
            DB::table('walletlog')->where('id', $dupli->id)->delete();
        }
    }

    /**
     * Lleva a la vista para remover las billeteras
     *
     * @return void
     */
    public function indexremover()
    {
        view()->share('title', 'Remover Billetera');
        $billetera = collect();
        $user = null;
        $total = 0;
        if (request()->iduser) {
            $user = User::find(request()->iduser);
            if ($user == null) {
                $user = 'Usuario no encontrado';
            }else{
                $billetera = Wallet::where([
                    ['debito', '>=', 0],
                    ['iduser', '=', $user->ID]
                ])->get();
                $total = $billetera->sum('debito');
            }
        }

        return view('admin.remover')->with(compact('billetera', 'user', 'total'));    
    }

    /**
     * Permite remover las transaciones de un usuario
     *
     * @param Request $request
     * @return void
     */
    public function remover(Request $request)
    {
        $validate = $request->validate([
            'idtransacion' => 'required'
        ]);

        try {
            if ($validate) {
                $idtrans = $request->idtransacion;
                $this->eliminarRegistros($idtrans);
                return redirect()->back()->with('msj', 'La Transacion: '.$idtrans.' fue eliminada exitosamente ');
            }
        } catch (\Throwable $th) {
            //throw $th;
        }   
    }

    public function indexeditWallet()
    {
        view()->share('title', 'Editar Billetera');
        $billetera = collect();
        $user = null;
        $total = 0;
        if (request()->iduser) {
            $user = User::find(request()->iduser);
            if ($user == null) {
                $user = 'Usuario no encontrado';
            }else{
                $billetera = Wallet::where([
                    ['debito', '>=', 0],
                    ['iduser', '=', $user->ID]
                ])->get();
                $total = $billetera->sum('debito');
            }
        }

        return view('admin.editWallet')->with(compact('billetera', 'user', 'total'));  
    }

    /**
     * Permite agregar transaciones positivas o negativas en las wallets
     *
     * @param Request $request
     * @return void
     */
    public function editWallet(Request $request)
    {
        $validate = $request->validate([
            'accion' => 'required',
            'iduser' => 'required',
            'concepto' => 'required',
            'monto' => 'required|numeric'
        ]);
        try {
            if($validate){
                $user = User::find($request->iduser);
                $usuarios[] = [
                        'correo' => $user->user_email,
                        'accion' => $request->accion,
                        'concepto' => $request->concepto,
                        'total' => $request->monto
                ];
                $this->arreglarBilletera($usuarios);
                $msj = 'La Transacion de '.$request->accion.' fue exitosa';
                return redirect()->back()->with('msj', $msj);
            }
        } catch (\Throwable $th) {
            
        }
    }

    /**
     * Permite pagar los bonos de construcion que no se pagaron bien
     *
     * @return void
     */
    public function payBonoConstrucion()
    {
        $binarios = Commission::where([
            ['tipo_comision', '=', 'Bono Binario'],
            // ['referred_email', '=', 'sindyemely223@outlook.com']
        ])
        ->whereBetween('date', ['20210729', '20210731'])
        // ->whereDate('date', '>', '20210701')
        ->get();
        foreach ($binarios as $binario) {
            $fechaTemp = new Carbon($binario->date);
            // dump($binario->user_id, $binario->total, $fechaTemp);
            $this->bonoConstrucion($binario->user_id, $binario->total, $fechaTemp->format('Ymd'));
        }

    }  
} 