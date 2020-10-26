<?php

namespace App\Http\Controllers;

use App\Settings;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\SettingsEstructura;
use App\Commission;
use App\Contenido;
use App\Monedas;
use CoinbaseCommerce\ApiClient;
use CoinbaseCommerce\Resources\Charge;
use App\Http\Controllers\TiendaController;
use App\Http\Controllers\ComisionesController;


class IndexController extends Controller
{
    /**
     * Función que devuelve los patrocinados de un determinado usuario
     * 
     * @access private
     * @param int $id - id del usuario 
     * @return array
     */
    private function getSponsor($user_id){
        $tmp = User::select('ID', 'user_email', 'status', 'display_name', 'puntos', 'created_at', 'ladomatrix', 'rol_id', 'avatar')->where('position_id', $user_id)->get()->toArray();
		return $tmp;
    }
    /**
     * Función que devuelve los referidos de un determinado usuario
     * 
     * @access public
     * @param int $user_id - id del usuario
     * @return array - listado de los referidos del usuario
     */
    public function getReferreds($user_id){
        $referidos = User::select('ID', 'user_email', 'status', 'display_name', 'puntos', 'created_at', 'ladomatrix', 'rol_id', 'avatar')->where('referred_id', $user_id)->get()->toArray();
		return $referidos;
	}
    
    /**
     * Obtienen a todo los usuarios referidos de un usuario determinado
     * 
     * @access public
     * @param array $arregloUser - listado de usuario, int $niveles - niveles a recorrer,
     * int $para - nivel a detenerse, array $allUser - todos los usuario referidos
     * @return array - listado de todos los usuario
     */
	public function getReferredsAll($arregloUser, $niveles, $para, $allUser, $tipoestructura)
    {
        if ($niveles <= $para) {
            $llaves =  array_keys($arregloUser);
            $finFor = end($llaves);
            $cont = 0;
            $tmparry = [];
            foreach ($arregloUser as $user) {
                $allUser [] = [
                    'ID' => $user['ID'],
                    'email' => $user['user_email'],
                    'nombre' => $user['display_name'],
                    'status' => $user['status'],
                    'puntos' => $user['puntos'],
                    'nivel' => $niveles,
                    'rol' => $user['rol_id'],
                    'avatar' => $user['avatar'],
                    'fecha' => $user['created_at'],
                    'lado' => $user['ladomatrix'],
                ];
                if ($tipoestructura == 'arbol') {
                    if (!empty($this->getReferreds($user['ID']))) {
                        $tmparry [] = $this->getReferreds($user['ID']);
                    }
                }else{
                    if (!empty($this->getSponsor($user['ID']))) {
                        $tmparry [] = $this->getSponsor($user['ID']);
                    }
                }
                if ($finFor == $cont) {
                    if (!empty($tmparry)) {
                        $tmp2 = $tmparry[0];
                        for($i = 1; $i < count($tmparry); $i++){
                            $tmp2 = array_merge($tmp2,$tmparry[$i]);
                        }
                        $this->getReferredsAll($tmp2, ($niveles+1), $para, $allUser, $tipoestructura);
                    }else{
                        $GLOBALS['allUsers'] = $allUser;
                    }
                }else{
                    $cont++;
                }
          }
        }else{
            $GLOBALS['allUsers'] = $allUser;
        }
    }

    /**
     * Devuelve el tipo de estructura con que se esta trabajando en el sistema
     * 
     * @access public
     * @return string
     */
    public function obtenerEstructura()
    {
        $settingEstructura = SettingsEstructura::find(1);
        $estructura = "";
        if ($settingEstructura->tipoestructura == 'arbol') {
            $estructura = "arbol";
        } elseif ($settingEstructura->tipoestructura == 'matriz') {
            $estructura = "matriz";
        }else{
            if ($settingEstructura->estructuraprincipal == 1) {
                if ($settingEstructura->usuarioprincipal == 1) {
                    $estructura = "arbol";
                } else {
                    $estructura = "matriz";
                }
            } else {
                if ($settingEstructura->usuarioprincipal == 1) {
                    $estructura = "arbol";
                } else {
                    $estructura = "matriz";
                }
            }
        }
        return  $estructura;
    }


    /**
     * Genera el Arreglo de los usuarios referidos
     * 
     * @access public
     * @param $iduser - id del usuario 
     * @return array
     */
    public function generarArregloUsuario($iduser)
    {
        $settingEstructura = SettingsEstructura::find(1);
        $GLOBALS['allUsers'] = [];
        // if ($this->obtenerEstructura() == 'arbol') {
            $referidosDirectos = $this->getReferreds($iduser);
            $this->getReferredsAll($referidosDirectos, 1, $settingEstructura->cantnivel, [], 'arbol');
        // } else {
        //     $referidosDirectos = $this->getSponsor($iduser);
            
        //     $this->getReferredsAll($referidosDirectos, 1, $settingEstructura->cantnivel, [], 'matriz');
        // }

        return $GLOBALS['allUsers'];
    }

    /**
     * Obtiene lo nuevos miembros de un usuario
     */
    public function newMembers($iduser)
    {
        $TodosUsuarios = $this->generarArregloUsuario($iduser);
        $TodosUsuarios = $this->ordenarArreglosMultiDimensiones($TodosUsuarios, 'ID', 'numero');
        return array_slice($TodosUsuarios, 0, 7);
    }

    /**
     * Obtiene la información de las ventas
     * 
     * calcula la cantidad de ventas mensuales
     * 
     * @access public
     * @return array
     */
    public function chartVentas()
    {
        $settings = Settings::first();
        // $sql = "SELECT month(wp.post_date) as mes, SUM(wpm.meta_value) as total FROM $settings->prefijo_wp.postmeta as wpm INNER JOIN $settings->prefijo_wp.posts as wp on (wp.ID = wpm.post_id) WHERE wp.post_status = 'wc-completed' AND wpm.meta_key = '_order_total' AND YEAR(wp.post_date) = year(now()) GROUP BY month(wp.post_date)";
        if (Auth::user()->rol_id != 0) {
            $allUser = $this->generarArregloUsuario(Auth::user()->ID);
            
            $ventas = [];
            for ($i=1; $i < 13; $i++) {
                array_push($ventas, [
                    'mes' => $i,
                    'total' => 0,
                    'comision' => 0,
                ]);
            }
            foreach ($allUser as $user) {
                $ventas2 = DB::table($settings->prefijo_wp.'postmeta')
                        ->join($settings->prefijo_wp.'posts', $settings->prefijo_wp.'postmeta.post_id', '=', $settings->prefijo_wp.'posts.ID')
                        ->join($settings->prefijo_wp.'postmeta as wpm', $settings->prefijo_wp.'postmeta.post_id', '=', 'wpm.post_id')
                        ->select(DB::raw('month('.$settings->prefijo_wp.'posts.post_date) as mes'), DB::raw('SUM('.$settings->prefijo_wp.'postmeta.meta_value) as total'), DB::raw('month('.$settings->prefijo_wp.'posts.post_date) as comision'))
                        ->where([
                            [$settings->prefijo_wp.'posts.post_status', '=', 'wc-completed'],
                            [$settings->prefijo_wp.'postmeta.meta_key', '=', '_order_total'],
                            ['wpm.meta_key', '=', '_customer_user'],
                            ['wpm.meta_value', '=', $user['ID']],
                            [DB::raw('YEAR('.$settings->prefijo_wp.'posts.post_date)'), '=', DB::raw('year(now())')],
                        ])->groupBy(DB::raw('month('.$settings->prefijo_wp.'posts.post_date)'))->get();

                if (!empty($ventas2)) {
                    foreach ($ventas2 as $venta) {
                        $ventas[($venta->mes -1)]['total'] = ($ventas[($venta->mes -1)]['total'] + $venta->total);
                    }
                }
            }
            $wallets = DB::table('walletlog')->select(DB::raw('month(created_at) as mes'), DB::raw('SUM(debito) as comision'))->where([
                ['iduser', '=', Auth::user()->ID],
                [DB::raw('YEAR(created_at)'), '=', DB::raw('year(now())')],
            ])->groupBy(DB::raw('month(created_at)'))->get();
            
        } else {
            $ventas = DB::table($settings->prefijo_wp.'postmeta')
                        ->join($settings->prefijo_wp.'posts', $settings->prefijo_wp.'postmeta.post_id', '=', $settings->prefijo_wp.'posts.ID')
                        ->select(DB::raw('month('.$settings->prefijo_wp.'posts.post_date) as mes'), DB::raw('SUM('.$settings->prefijo_wp.'postmeta.meta_value) as total'), DB::raw('month('.$settings->prefijo_wp.'posts.post_date) as comision'))
                        ->where([
                            [$settings->prefijo_wp.'posts.post_status', '=', 'wc-completed'],
                            [$settings->prefijo_wp.'postmeta.meta_key', '=', '_order_total'],
                            [DB::raw('YEAR('.$settings->prefijo_wp.'posts.post_date)'), '=', DB::raw('year(now())')],
                        ])->groupBy(DB::raw('month('.$settings->prefijo_wp.'posts.post_date)'))->get();


            $wallets = DB::table('walletlog')->select(DB::raw('month(created_at) as mes'), DB::raw('SUM(debito) as comision'))->where([
                [DB::raw('YEAR(created_at)'), '=', DB::raw('year(now())')],
            ])->groupBy(DB::raw('month(created_at)'))->get();
        }
        
        $tmp = [];
        for ($i=1; $i < 13; $i++) {
            array_push($tmp, [
                'mes' => $i,
                'total' => 0,
                'comision' => 0,
            ]);
        }
        if (!empty($ventas)) {
            foreach ($ventas as $venta) {
                if (Auth::user()->rol_id != 0) {
                    $tmp[($venta['mes'] -1)]['mes'] = $venta['mes'];
                    $tmp[($venta['mes'] -1)]['total'] = $venta['total'];
                } else {
                    $tmp[($venta->mes -1)]['mes'] = $venta->mes;
                    $tmp[($venta->mes -1)]['total'] = $venta->total;
                }
            }
        }
        if (!empty($wallets)) {
            foreach ($wallets as $wallet) {
                $tmp[($wallet->mes -1)]['comision'] = $wallet->comision;
            }
        }

        return json_encode($tmp);
    }

    /**
     * Permite obtener la cantida de pagos
     *
     * @return void
     */
    public function charPagos()
    {
        if (Auth::user()->rol_id == 0) {
            $pagos = [
                'pagado' => DB::table('pagos')->where('estado', 1)->get()->count('id'),
                'pendiente' => DB::table('pagos')->where('estado', 0)->get()->count('id'),
                'cancelado' => DB::table('pagos')->where('estado', 2)->get()->count('id'),
            ];
        } else {
            $pagos = [
                'pagado' => DB::table('pagos')->where([['iduser', '=', Auth::user()->ID],['estado', '=', 1]])->get()->count('id'),
                'pendiente' => DB::table('pagos')->where([['iduser', '=', Auth::user()->ID], ['estado', '=', 0]])->get()->count('id'),
                'cancelado' => DB::table('pagos')->where([['iduser', '=', Auth::user()->ID], ['estado', '=', 2]])->get()->count('id'),
            ];
        }

        return json_encode($pagos);
    }

    /**
     * Obtiene la información de los rangos
     * 
     * calcula la cantidad de rangos que estan en el sistema
     * 
     * @access public
     * @return array
     */
    public function chartRangos()
    {
        $settings = Settings::first();
        // $sql = "SELECT r.name, COUNT(wu.rol_id) FROM roles as r LEFT JOIN $settings->prefijo_wp.users as wu on (r.id = wu.rol_id) GROUP BY r.name";
        if (Auth::user()->ID != 1) {
            if ($this->obtenerEstructura() == 'arbol') {
                $roles = DB::table('roles')
                    ->leftjoin($settings->prefijo_wp.'users', 'roles.id', '=', $settings->prefijo_wp.'users.rol_id')
                    ->select('roles.name', DB::raw('COUNT('.$settings->prefijo_wp.'users.rol_id) as cantidad'))
                    ->where($settings->prefijo_wp.'users.position_id', Auth::user()->ID)
                    ->orderBy('roles.id', 'asc')
                    ->groupBy('roles.name', 'roles.id')->get();
            } else {
                $roles = DB::table('roles')
                    ->leftjoin($settings->prefijo_wp.'users', 'roles.id', '=', $settings->prefijo_wp.'users.rol_id')
                    ->select('roles.name', DB::raw('COUNT('.$settings->prefijo_wp.'users.rol_id) as cantidad'))
                    ->where($settings->prefijo_wp.'users.position_id', Auth::user()->ID)
                    ->orderBy('roles.id', 'asc')
                    ->groupBy('roles.name', 'roles.id')->get();
            }
        } else {
            $roles = DB::table('roles')
                    ->join($settings->prefijo_wp.'users', 'roles.id', '=', $settings->prefijo_wp.'users.rol_id')
                    ->select('roles.name', DB::raw('COUNT('.$settings->prefijo_wp.'users.rol_id) as cantidad'))
                    ->orderBy('roles.id', 'asc')
                    ->groupBy('roles.name', 'roles.id')->get();
        }
        
        return json_encode($roles);
    }

    /**
     * Obtiene la información de los usuarios
     * 
     * calcula la cantidad de usuario que tiene el sistema, tambien calcula la cantidad activos e inactivos
     * 
     * @access public
     * @return array
     */
    public function chartUsuarios()
    {
        $totalMesN1 = [];
        $totalMesN2 = [];
        $totalMesN3 = [];
        $totalMesN4 = [];
        $allUser = $this->generarArregloUsuario(Auth::user()->ID);
        $Ano_Actual = Carbon::now()->format('Y');
        $totalN1 = 0;
        $totalN2 = 0;
        $totalN3 = 0;
        $totalN4 = 0;
        for ($i=1; $i < 13; $i++) {
            $totalN1Mtmp = 0;
            $totalN2Mtmp = 0;
            $totalN3Mtmp = 0;
            $totalN4Mtmp = 0;
            foreach ($allUser as $user) {
                $fecha_register = new Carbon($user['fecha']);
                if ($Ano_Actual == $fecha_register->format('Y')) {
                    if ($user['nivel'] == 1) {
                        if ($fecha_register->format('m') == $i) {
                            $totalN1Mtmp++;
                        }
                    } elseif ($user['nivel'] == 2) {
                        if ($fecha_register->format('m') == $i) {
                            $totalN2Mtmp++;
                        }
                    } elseif ($user['nivel'] == 3) {
                        if ($fecha_register->format('m') == $i) {
                            $totalN3Mtmp++;
                        }
                    } elseif ($user['nivel'] == 4) {
                        if ($fecha_register->format('m') == $i) {
                            $totalN4Mtmp++;
                        }
                    }
                }
            }
            $totalMesN1 [] = $totalN1Mtmp;
            $totalMesN2 [] = $totalN2Mtmp;
            $totalMesN3 [] = $totalN3Mtmp;
            $totalMesN4 [] = $totalN4Mtmp;
        }

        foreach ($allUser as $user) {
            if ($user['nivel'] == 1) {
                    $totalN1++;
            } elseif ($user['nivel'] == 2) {
                    $totalN2++;
            } elseif ($user['nivel'] == 3) {
                    $totalN3++;
            } elseif ($user['nivel'] == 4) {
                    $totalN4++;
            }
        }
        
        if (Auth::user()->rol_id != 0) {
            if (!empty(Auth::user()->paquete)) {
                $paquete = json_decode(Auth::user()->paquete);
                $datos = [
                    'totalN1' => ($paquete->nivel >= 1) ? $totalN1 : 0,
                    'totalN2' => ($paquete->nivel >= 2) ? $totalN2 : 0,
                    'totalN3' => ($paquete->nivel >= 3) ? $totalN3 : 0,
                    'totalN4' => ($paquete->nivel == 4) ? $totalN4 : 0,
                    'totalMesN1' => ($paquete->nivel >= 1) ? $totalMesN1 : 0,
                    'totalMesN2' => ($paquete->nivel >= 2) ? $totalMesN2 : 0,
                    'totalMesN3' => ($paquete->nivel >= 3) ? $totalMesN3 : 0,
                    'totalMesN4' => ($paquete->nivel == 4) ? $totalMesN4 : 0,
                ];
            }else{
                $datos = [
                    'totalN1' => 0,
                    'totalN2' => 0,
                    'totalN3' => 0,
                    'totalN4' => 0,
                    'totalMesN1' => 0,
                    'totalMesN2' => 0,
                    'totalMesN3' => 0,
                    'totalMesN4' => 0,
                ];
            }
        } else {
            $datos = [
                'totalN1' => $totalN1,
                'totalN2' => $totalN2,
                'totalN3' => $totalN3,
                'totalN4' => $totalN4,
                'totalMesN1' => $totalMesN1,
                'totalMesN2' => $totalMesN2,
                'totalMesN3' => $totalMesN3,
                'totalMesN4' => $totalMesN4,
            ];
        }
        
        
        return json_encode($datos);
    }

    /**
     * Realiza un rankig de los 5 usuarios com mas comisiones
     * 
     * @access public
     * @return array
     */
    public function rankingComisiones()
    {
        $ranking = [];
        $clave = "";
        if (Auth::user()->ID != 1) {
            $users = $this->generarArregloUsuario(Auth::user()->ID);
            $clave = 'nombre';
        } else {
            $users = User::all()->toArray();
            $clave = 'display_name';
        }
        foreach ($users as $user) {
            $ranking [] = [
                'usuario' => $user[$clave],
                'totalcomision' => Commission::where('user_id', $user['ID'])->get()->sum('total'),
            ];
        }

        $ranking = $this->ordenarArreglosMultiDimensiones($ranking, 'totalcomision', 'numero');
        
        return array_slice($ranking, 0, 5);;
    }
    
    
    public function noticias()
    {
        $contenido=Contenido::orderBy('id','DESC')->paginate(3);
        return $contenido;
    }

    /**
     * Realiza un rankig de los 5 usuarios com mas Compras
     * 
     * @access public
     * @return array
     */
    public function rankingVentas()
    {
        $settings = Settings::first();
        $moneda = Monedas::where('principal', 1)->get()->first();
        $ranking = [];
        $clave = "";
        if (Auth::user()->ID != 1) {
            $users = $this->generarArregloUsuario(Auth::user()->ID);
            $clave = "nombre";
        } else {
            $users = User::all()->toArray();
            $clave = "display_name";
        }
        foreach ($users as $user) {
            $cantVentasMont = 0;
            $ordenes = DB::table($settings->prefijo_wp.'postmeta')
                            ->select('post_id')
                            ->where('meta_key', '=', '_customer_user')
                            ->where('meta_value', '=', $user['ID'])
                            ->orderBy('post_id', 'DESC')
                            ->get();

            foreach ($ordenes as $orden){
                $totalOrden = DB::table($settings->prefijo_wp.'postmeta')
                        ->select('meta_value')
                        ->where('post_id', '=', $orden->post_id)
                        ->where('meta_key', '=', '_order_total')
                        ->first();
                        $valor = trim(str_replace($moneda->simbolo, ' ', $totalOrden->meta_value));
                        $cantVentasMont = ((int)$valor + $cantVentasMont);
            }
            $ranking [] = [
                'usuario' => $user[$clave],
                'totalventas' => $cantVentasMont,
            ];
        }

        $ranking = $this->ordenarArreglosMultiDimensiones($ranking, 'totalventas', 'numero');
        
        return array_slice($ranking, 0, 5);
    }

    /**
     * Permite ordenar el arreglo primario con las claves de los arreglos segundarios
     * 
     * @access public
     * @param array $arreglo - el arreglo que se va a ordenar, string $clave - con que se hara la comparecion de ordenamiento,
     * stridn $forma - es si es cadena o numero
     * @return array
     */
    public function ordenarArreglosMultiDimensiones($arreglo, $clave, $forma)
    {
        usort($arreglo, $this->build_sorter($clave, $forma));
        return $arreglo;
    }

    /**
     * compara las clave del arreglo
     * 
     * @access private
     * @param string $clave - llave o clave del arreglo segundario a comparar
     * @return string
     */
    private function build_sorter($clave, $forma) {
        return function ($a, $b) use ($clave, $forma) {
            if ($forma == 'cadena') {
                return strnatcmp($a[$clave], $b[$clave]);
            } else {
                return $b[$clave] - $a[$clave] ;
            }
            
        };
    }

    /**
     * Obtiene todas las ventas de la red
     * 
     * @access public
     * @param int $iduser - id del usuario
     * @return int 
     */
    public function countOrderNetwork($iduser)
    {
        $TodosUsuarios = $this->generarArregloUsuario($iduser);
        $compras = array();
        $cantVentas = 0;
        $settings = Settings::first();
        foreach($TodosUsuarios as $user){
            $ordenes = DB::table($settings->prefijo_wp.'postmeta')
                            ->select('post_id')
                            ->where('meta_key', '=', '_customer_user')
                            ->where('meta_value', '=', $user['ID'])
                            ->orderBy('post_id', 'DESC')
                            ->get();

            foreach ($ordenes as $orden){
                $cantVentas++;
            }
        }
        return $cantVentas;
    }

    /**
     * Obtiene el monto de todas las ventas de la red
     * 
     * @access public
     * @param int $iduser - id del usuario
     * @return float
     */
    public function countOrderNetworkMont($iduser)
    {
        $TodosUsuarios = $this->generarArregloUsuario($iduser);
        $compras = array();
        $cantVentasMont = 0;
        $settings = Settings::first();
        foreach($TodosUsuarios as $user){
            $ordenes = DB::table($settings->prefijo_wp.'postmeta')
                            ->select('post_id')
                            ->where('meta_key', '=', '_customer_user')
                            ->where('meta_value', '=', $user['ID'])
                            ->orderBy('post_id', 'DESC')
                            ->get();

            foreach ($ordenes as $orden){
                $totalOrden = DB::table($settings->prefijo_wp.'postmeta')
                        ->select('meta_value')
                        ->where('post_id', '=', $orden->post_id)
                        ->where('meta_key', '=', '_order_total')
                        ->first();
                $cantVentasMont = ($totalOrden->meta_value + $cantVentasMont);
            }
        }
        return $cantVentasMont;
    }

    /**
     * Permite Saber cuales cuales compras se aprobaron por el wordpress 
     *  para poder descontar que se compro
     *
     * @return void
     */
    public function ordenesSistema()
    {
        $tienda = new TiendaController;
        $apiKey = env('COINBASE_API_KEY');
        ApiClient::init($apiKey);
        $solicitudes = $tienda->ArregloCompra();
        foreach ($solicitudes as $solicitud) {
            if (!empty($solicitud['code_coinbase']) && !empty($solicitud['id_coinbase']) && $solicitud['estado'] != 'Completado') {
                $retrievedCharge = Charge::retrieve($solicitud['id_coinbase']);
                if (count($retrievedCharge->timeline) > 0) {
                    foreach ($retrievedCharge->timeline as $item) {
                        if ($item['status'] == 'COMPLETED') {
                            $tienda->actualizarBD($solicitud['idcompra'], 'wc-completed');
                            $usuarios = User::select('ID', 'status', 'rol_id', 'display_name')->get();
                            $comiesiones = new ComisionesController;
                            foreach ($usuarios as $user) {
                                if ($user->rol_id != 0) {
                                // $comiesiones->generarComision($user->ID);
                                $comiesiones->bonoUnilevel($user->ID);
                                }
                            }
                        }
                    }
                    
                }
            }
        }
    }

}
