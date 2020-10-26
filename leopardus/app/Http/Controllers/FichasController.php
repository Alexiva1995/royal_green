<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Fichas; use App\User;
use App\Compras; use App\Settings;
use App\SettingsEstructura;
use DB; use App\FichasInactiva; 
use Illuminate\Support\Facades\View;
use Auth;
use App\Http\Controllers\ComisionesController;

class FichasController extends Controller
{

    public function VerificarCompraFicha($iduser)
    {
        $settings = Settings::first();
        $compras = $this->getShopping($iduser);
        $user = User::find($iduser);
        foreach ($compras as $compra) {
            $check = DB::table('compras')
                        ->select('id')
                        ->where('iduser', '=', $iduser)
                        ->where('idcompra', '=', $compra->post_id)
                        ->first();
    
            if ($check == null) {
              //Se obtienen los datos de cada compra
              $datosCompra = $this->getShoppingDetails($compra->post_id);
                if ($datosCompra->post_status == 'wc-completed') {
                    $totalProductos = $this->getProductos($compra->post_id);
                    $paquete = '';
                    $requisito = 0;
                    $arbol = '';
                    foreach ($totalProductos as $producto) {
                        $productoID = $this->getIdProductos($producto->order_item_id);
                        switch ($productoID) {
                            case 39:
                                $arbol = 'A';
                                $paquete = 'Paquete 1';
                                $requisito = 500;
                                $this->insertFichas($iduser, 1, 0);
                                break;

                            case 40:
                                $arbol = 'B';
                                $paquete = 'Paquete 2';
                                $requisito = 1500;
                                $this->insertFichas($iduser, 1, 0);
                                $this->insertFichas($iduser, 1, 1);
                                break;

                            case 41:
                                $arbol = 'B';
                                $paquete = 'Paquete 3';
                                $requisito = 4500;
                                $this->insertFichas($iduser, 3, 0);
                                $this->insertFichas($iduser, 3, 1);
                                break;

                            case 42:
                                $arbol = 'B';
                                $paquete = 'Paquete 4';
                                $requisito = 6500;
                                $this->insertFichas($iduser, 7, 0);
                                $this->insertFichas($iduser, 3, 1);
                                break;

                            case 43:
                                $arbol = 'C';
                                $paquete = 'Paquete 5';
                                $requisito = 18000;
                                $this->insertFichas($iduser, 7, 0);
                                $this->insertFichas($iduser, 7, 1);
                                $this->insertFichas($iduser, 3, 2);
                                break;

                            case 44:
                                $arbol = 'E';
                                $paquete = 'Paquete 6';
                                $requisito = 33000;
                                $this->insertFichas($iduser, 7, 0);
                                $this->insertFichas($iduser, 7, 1);
                                $this->insertFichas($iduser, 3, 2);
                                $this->insertFichas($iduser, 1, 3);
                                $this->insertFichas($iduser, 1, 4);
                                break;

                            case 45:
                                $arbol = 'E';
                                $paquete = 'Paquete 7';
                                $requisito = 63000;
                                $this->insertFichas($iduser, 7, 0);
                                $this->insertFichas($iduser, 7, 1);
                                $this->insertFichas($iduser, 3, 2);
                                $this->insertFichas($iduser, 3, 3);
                                $this->insertFichas($iduser, 3, 4);
                                break;

                            default:
                                
                                break;
                        }
                    }
                    if ($user->paquete == 'Sin Paquete') {
                        $user->paquete = $paquete;
                        $user->requisito = $requisito;
                        $user->save();
                    }
                    if (($user->llevorequisito) >= $user->requisito) {
                        $user->llevorequisito = 0;
                        $user->wallettmp = 0;
                        $user->puntostmp = 0;
                        $user->paquete = $paquete;
                        $user->requisito = $requisito;
                        $user->save();
                    }
                    Compras::create([
                        'idcompra' => $compra->post_id,
                        'iduser' => $iduser,
                        'arbol' => $arbol
                    ]);
                }
            }
        }
    }
    /**
     * Guarda las fichas para que el usuario pueda procesarla o el sistema despues de haber pasado 24 horas
     *
     * @param integer $iduser
     * @param integer $fichas
     * @param integer $positionArbol
     * @return void
     */
    public function insertFichas(int $iduser, int $fichas, int $positionArbol)
    {
        $letras = ['A', 'B', 'C', 'D', 'E', 'F'];
        for ($i=0; $i < $fichas; $i++) { 
            FichasInactiva::Create([
                'iduser' => $iduser,
                'arbol' => $letras[$positionArbol],
            ]);
        }
    }

    /**
     * Cuenta la cantidad de fichas que contiene un arbol
     *
     * @param integer $iduser
     * @param string $arbol
     * @return boolean
     */
    public function cantidadFichas($iduser, $arbol)
    {
        $resul = true;
        $padre = Fichas::where([
            ['idoriginal', '=', $iduser],
            ['arbol', '=', $arbol]
        ])->first();
        $idpadre = (!empty($padre)) ? $padre->ID : 0;
        $referidos = $this->getSponsor($idpadre, $arbol);
        if (!empty($referidos)) {
            $cantReferidoIzq = (!empty($referidos[0])) ? (count($this->generarArregloUsuario($referidos[0]['ID'], $arbol)) + 1) : 0;
            $cantReferidoDer = (!empty($referidos[1])) ? (count($this->generarArregloUsuario($referidos[1]['ID'], $arbol)) + 1) : 0;
            $suma = ($cantReferidoIzq + $cantReferidoDer);
            if ($suma >= 50) {
                $resul = false;
            }
        }
        return $resul;
    }

    /**
     * Permite verificar si en un arbol esta completo para insertar
     *  si el arbol esta lleno, este se pasa a otro arbo mas que este vacio
     *
     * @param integer $iduser
     * @param integer $fichas
     * @param integer $positionArbol
     * @param object $user
     * @param integer $idposicion
     * @return void
     */
    public function tableros(int $iduser, int $fichas, int $positionArbol, object $user, $idposicion)
    {   
        if ($idposicion != null) {
            $ficha = Fichas::find($idposicion);
            $iduser = $ficha->idoriginal;
        }
        $letras = ['A', 'B', 'C', 'D', 'E', 'F'];
        if ($positionArbol < 5) {
            if ($this->cantidadFichas($iduser, $letras[$positionArbol])) {
                for ($i=0; $i < $fichas; $i++) { 
                    $this->AgregarFichas($user, $letras[$positionArbol], $iduser, $idposicion);
                }
            }else{
                $this->tableros($iduser, $fichas, ($positionArbol+1), $user, null);
            }
        }else{
            for ($i=0; $i < $fichas; $i++) { 
                $this->AgregarFichas($user, $letras[$positionArbol], $iduser, $idposicion);
            }
        }
    }

    /**
     * Permite agregar fichas a los diferentes arboles comprados
     *
     * @param object $data
     * @param string $arbol
     * @param integer $iduser
     * @param integer $idposicion
     * @return void
     */
    public function AgregarFichas(object $data, string $arbol, $iduser, $idposicion)
    {
        if ($idposicion == null) {
            $idusertmp = $iduser;
            $Tablerolleno = Fichas::all()->count('ID');
            // verifico si hay algun registro en el sistema
            if ($Tablerolleno != 0) {
                $tengoRegistro = Fichas::where([
                    ['idoriginal', '=', $iduser],
                    ['arbol', '=', $arbol]
                ])->get()->count('ID');
                // verifico si yo poseo registro en el sistema
                if ($tengoRegistro == 0) {
                    $miReferidoTieneRegistro = Fichas::where([
                        ['idoriginal', '=', $data->referred_id],
                        ['arbol', '=', $arbol]
                    ])->get()->count('ID');
                    // verifico si me referido tiene registro en el sistema
                    if ($miReferidoTieneRegistro != 0) {
                        $idusertmp = $data->referred_id;
                    }
                }
            }
            $padre = Fichas::where([
                ['idoriginal', '=', $idusertmp],
                ['arbol', '=', $arbol]
            ])->first();
            $idpadre = (!empty($padre)) ? $padre->ID : 0;
            $position = $this->getPosition($idpadre, $arbol);
        }else{
            $idpadre = $idposicion;
            $position = $idposicion;
        }
        $ficha = Fichas::create([
            'idoriginal' => $data->ID,
            'user_email' => $data['user_email'],
            'user_status' => '0',
            'user_login' => $data['user_login'],
            'user_nicename' => $data['user_nicename'],
            'display_name' => $data['display_name'],
            'gender' => $data['genero'],
            'birthdate' => $data['edad'],
            'user_pass' => $data['user_pass'],
            'password' => $data['password'],
            'clave' => $data['clave'],
            'referred_id' => $idpadre,
            'position_id' => $position,
            'tipouser' => $data['tipouser'],
            'arbol' => $arbol,
            'status' => '1',
        ]);
    }


    /**
   * Obtiene un ID de Posicionamiento Valido 
   *
   * @param integer $id - primer id a verificar
   * @param string $arbol
   * @param bool $posiciones - verifica si va a traerme una sola posicion o varias
   * @return int
   */
  public function getPosition(int $id, $arbol)
  {
        $resul = 0;
        $cantPosiciones = Fichas::where([
            ['position_id', '=', $id],
            ['arbol', '=', $arbol]
        ])->get()->count('ID');
        if (2 > $cantPosiciones) {
            $resul = $id;
        } else {
            $ids = $this->getIDs($id, $arbol);
            $GLOBALS['idposicionamiento'] = 0;
            $this->verificarOtraPosition($ids, 2, $arbol);
                $resul = $GLOBALS['idposicionamiento'];
        }
        
        return $resul;
        
  }

  /**
   * Buscar Alternativas al los ID Posicionamiento validos
   *
   * @param array $arregloID - arreglos de ID a Verificar
   * @param string $arbol
   * @param int $limitePosicion - Cantdad de posiciones disponibles
   */
  public function verificarOtraPosition($arregloID, $limitePosicion, $arbol)
  {
    $tmparry = [];
    $bandera = false;
    $llaves =  array_keys($arregloID);
    $finFor = end($llaves);
    $cont = 0;
    foreach ($arregloID as $item) {
        $cantPosiciones = Fichas::where([
            ['position_id', '=', $item['ID']],
            ['arbol', '=', $arbol]
        ])->get()->count('ID');
        if ($limitePosicion > $cantPosiciones) {
            $GLOBALS['idposicionamiento'] = $item['ID'];
            break;
        } else {
            $tmparry [] = $this->getIDs($item['ID'], $arbol);
            if ($finFor == $cont) {
                if (!empty($tmparry)) {
                    $tmp2 = $tmparry[0];
                    for($i = 1; $i < count($tmparry); $i++){
                        $tmp2 = array_merge($tmp2,$tmparry[$i]);
                    }
                    $this->verificarOtraPosition($tmp2, $limitePosicion, $arbol);
                }
            }else{
                $cont++;
            }
        }
    }
  }
  /**
   * Obtiene los id que seran verificados en el posicionamiento
   *
   * @param integer $id
   * @param string $arbol
   * @return array
   */
  public function getIDs(int $id, $arbol)
  {
      return Fichas::where([
          ['position_id', '=', $id],
          ['arbol', '=', $arbol]
      ])->select('ID')->orderBy('ID')->get()->toArray();
  }
  /**
     * Función que devuelve los patrocinados de un determinado usuario
     * 
     * @access private
     * @param int $id - id del usuario 
     * @param string $arbol
     * @return array
     */
    private function getSponsor($user_id, $arbol){
        $tmp = Fichas::select('ID', 'user_email', 'status', 'display_name', 'created_at')->where([
            ['position_id', '=', $user_id],
            ['arbol', '=', $arbol]
        ])->get()->toArray();
		return $tmp;
    }
    /**
     * Función que devuelve los referidos de un determinado usuario
     * 
     * @access public
     * @param int $user_id - id del usuario
     * @param string $arbol
     * @return array - listado de los referidos del usuario
     */
    public function getReferreds($user_id, $arbol){
        $referidos = Fichas::select('ID', 'user_email', 'status', 'display_name', 'created_at')->where([
            ['position_id', '=', $user_id],
            ['arbol', '=', $arbol]
        ])->get()->toArray();
		return $referidos;
	}
    
    /**
     * Obtienen a todo los usuarios referidos de un usuario determinado
     * 
     * @access public
     * @param array $arregloUser - listado de usuario, 
     * @param int $niveles - niveles a recorrer,
     * @param int $para - nivel a detenerse, 
     * @param array $allUser - todos los usuario referidos
     * @param string $arbol
     * @return array - listado de todos los usuario
     */
	public function getReferredsAll($arregloUser, $niveles, $para, $allUser, $tipoestructura, $arbol)
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
                    'nivel' => $niveles,
                    'fecha' => $user['created_at'],
                ];
                if ($tipoestructura == 'arbol') {
                    if (!empty($this->getReferreds($user['ID'], $arbol))) {
                        $tmparry [] = $this->getReferreds($user['ID'], $arbol);
                    }
                }else{
                    if (!empty($this->getSponsor($user['ID'], $arbol))) {
                        $tmparry [] = $this->getSponsor($user['ID'], $arbol);
                    }
                }
                if ($finFor == $cont) {
                    if (!empty($tmparry)) {
                        $tmp2 = $tmparry[0];
                        for($i = 1; $i < count($tmparry); $i++){
                            $tmp2 = array_merge($tmp2,$tmparry[$i]);
                        }
                        $this->getReferredsAll($tmp2, ($niveles+1), $para, $allUser, $tipoestructura, $arbol);
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
     * Genera el Arreglo de los usuarios referidos
     * 
     * @access public
     * @param integer $iduser - id del usuario 
     * @param string $arbol
     * @return array
     */
    public function generarArregloUsuario($iduser, $arbol)
    {
        $settingEstructura = SettingsEstructura::find(1);
        $referidosDirectos = $this->getSponsor($iduser, $arbol);
        $GLOBALS['allUsers'] = [];
        $this->getReferredsAll($referidosDirectos, 1, $settingEstructura->cantnivel, [], 'matriz', $arbol);
        return $GLOBALS['allUsers'];
    }

    //Función que devuelve el ID de las compras de un determinado usuario
	//que no hayan sido procesadas en una comisión anterior.
	public function getShopping($user_id){
        $settings = Settings::first();
        $comprasID = DB::table($settings->prefijo_wp.'postmeta')
                    ->select('post_id')
                    ->where('meta_key', '=', '_customer_user')
                    ->where('meta_value', '=', $user_id)
                    ->get();

        return $comprasID;
	}

	//Función que devuelve los datos de una compra determinada
	public function getShoppingDetails($shop_id){
        $settings = Settings::first();
		$datosCompra = DB::table($settings->prefijo_wp.'posts')
                        ->select('post_date', 'post_status')
                        ->where('ID', '=', $shop_id)
                        ->first();

        return $datosCompra;
	}

	// funcion para encontrar obtener los productos de la factura
	public function getProductos($shop_id)
	{
        $settings = Settings::first();
		$totalProductos = DB::table($settings->prefijo_wp.'woocommerce_order_items')
													->select('order_item_id')
													->where('order_id', '=', $shop_id)
													->get();

		return $totalProductos;
	}

	// funcion para obtener el id de los productos comprados
	public function getIdProductos($id_item)
	{
        $settings = Settings::first();
        $valor = 0;
		$IdProducto = DB::table($settings->prefijo_wp.'woocommerce_order_itemmeta')
													->select('meta_value')
													->where('order_item_id', '=', $id_item)
													->where('meta_key', '=', '_product_id')
													->first();

        if (!empty($IdProducto)) {
            $valor = $IdProducto->meta_value;
        }
		return $valor;
    }
    
    /**
     * Permite pagar el bono que por los tableros completados
     *
     * @param integer $iduser
     * @return void
     */
    public function pagarBono(int $iduser)
    {
        $combinacion = [
            0 => [25, 50, 120, 160, 320],
            1 => [40, 80, 200, 300, 600],
            2 => [120, 240, 350, 750, 1540],
            3 => [200, 400, 700, 1500, 3200],
            4 => [400, 800, 2000, 3000, 6000],
            5 => [1500, 2500, 4000, 8000, 16000]
        ];
        for ($a=0; $a < 6; $a++) { 
            $this->cantidadFichasPagar($iduser, $a, (3), $combinacion[$a][0], '3x3');
            $this->cantidadFichasPagar($iduser, $a, (6), $combinacion[$a][1], '6x6');
            $this->cantidadFichasPagar($iduser, $a, (12), $combinacion[$a][2], '12x12');
            $this->cantidadFichasPagar($iduser, $a, (18), $combinacion[$a][3], '18x18');
            $this->cantidadFichasPagar($iduser, $a, (25), $combinacion[$a][4], '25x25');
        }
    }

    /**
     * Cuenta la cantidad de fichas que contiene un arbol
     *
     * @param integer $iduser
     * @param string $arbol - el arbol va a verificar
     * @param integer $combinacion - numero para validar
     * @param integer $bono - bono a pagar
     * @return boolean
     */
    public function cantidadFichasPagar($iduser, $arbol, $combinacion, $bono, $concepto)
    {
        $letras = ['A', 'B', 'C', 'D', 'E', 'F'];
        $resul = false;
        $user = User::find($iduser);
        $comisiones = new ComisionesController;
        $padre = Fichas::where([
            ['idoriginal', '=', $iduser],
            ['arbol', '=', $letras[$arbol]]
        ])->first();
        if (!empty($padre)) {
            $idpadre = $padre->ID;
            $referidos = $this->getSponsor($idpadre, $letras[$arbol]);
            if (!empty($referidos)) {
                $referidosDirecto = User::where('referred_id', $iduser)->get()->count('ID');
                $cantReferidoIzq = (!empty($referidos[0])) ? (count($this->generarArregloUsuario($referidos[0]['ID'], $letras[$arbol])) + 1) : 0;
                $cantReferidoDer = (!empty($referidos[1])) ? (count($this->generarArregloUsuario($referidos[1]['ID'], $letras[$arbol])) + 1) : 0;
                $idcompra = $iduser.$arbol.$combinacion;
                $check = DB::table('commissions')
                            ->select('id')
                            ->where('user_id', '=', $iduser)
                            ->where('compra_id', '=', $idcompra)
                            ->first();

                if ($cantReferidoDer >= $combinacion && $cantReferidoIzq >= $combinacion && $referidosDirecto > 0 && $check == null) {
                    $concepto = 'Bono Tablero '.$letras[$arbol].' Combinacion '.$concepto;
                    $comisiones->guardarComision($iduser, $idcompra, $bono, $user->user_email, 0, $concepto, 'bono');
                }
            }
        }
        return $resul;
    }

    /**
     * Permite verificar cuales son las fichas diponibles y las posiciones disponibles para las fichas
     *
     * @return void
     */
    public function fichasAsignar()
    {
        View::share('title', 'Fichas Pendientes');
        $fichaspendientes = FichasInactiva::where('iduser', Auth::user()->ID)->get();
        $fichas = [];
        foreach ($fichaspendientes as $fichap) {
            $user = User::where('ID', $fichap->iduser)->select('display_name')->get()[0];
            $fichas [] = [
                'id' => $fichap->id,
                'usuario' => $user->display_name,
                'arbol' => $fichap->arbol,
                'debajo' => $this->posicionesDisponibles($fichap->iduser, $fichap->arbol)
            ];
        }
        return view('dashboard.fichasPendientes')->with(compact('fichas'));
    }

    /**
     * Permite verificar cuales son las posiciones disponibles que tiene el usuario para inserta las fichas
     *
     * @param integer $iduser
     * @param string $arbol
     * @return void
     */
    public function posicionesDisponibles($iduser, $arbol)
    {

        $padre = Fichas::where([
            ['idoriginal', '=', $iduser],
            ['arbol', '=', $arbol]
        ])->first();
        $posiciones = [];
        if (!empty($padre)) {
            $cantPosiciones = Fichas::where([
                ['position_id', '=', $padre->ID],
                ['arbol', '=', $arbol]
            ])->get()->count('ID');
            if (2 > $cantPosiciones) {
                $posiciones = [
                    [
                        'ID' => $padre->ID,
                        'email' => $padre->user_email,
                        'nombre' => $padre->display_name,
                        'status' => $padre->status,
                        'nivel' => 0,
                        'fecha' => $padre->created_at,
                    ]
                ]; 
            } else {
                $referidos = $this->generarArregloUsuario($padre->ID, $arbol);
                foreach ($referidos as $refe) {
                    $cantPosiciones = Fichas::where([
                        ['position_id', '=', $refe['ID']],
                        ['arbol', '=', $arbol]
                    ])->get()->count('ID');
                    if (2 > $cantPosiciones) {
                        $posiciones [] = $refe;
                    }
                }
            }
            
        }
        return $posiciones;
    }
    /**
     * Permite insertar la ficha base para el registro de las fichas
     *
     * @param integer $id
     * @return view
     */
    public function insertarBase($id)
    {
        $ficha = FichasInactiva::find($id);
        $user = User::find($ficha->iduser);
        $this->AgregarFichas($user, $ficha->arbol, $ficha->iduser, null);
        $ficha->delete();
        return redirect()->back()->with('msj', 'Ficha Insertada Exitosamente');
    }
    /**
     * Permite insertar una ficha en una posicion espefica
     *
     * @param integer $idposicion
     * @param integer $idficha
     * @return view
     */
    public function insertarFicha($idposicion, $idficha)
    {
        $ficha = FichasInactiva::find($idficha);
        $arbol = ['A'=> 0, 'B' => 1, 'C' => 2, 'D' => 3, 'E' => 4, 'F' => 5];
        $user = User::find($ficha->iduser);
        $this->tableros($ficha->iduser, 1, $arbol[$ficha->arbol], $user, $idposicion);
        $ficha->delete();
        return redirect()->back()->with('msj', 'Ficha Insertada Exitosamente');
    }
    /**
     * Permite insertar todas las fichas automaticamente
     *
     * @param integer $iduser
     * @return view
     */
    public function insetarAutomaticamente($iduser)
    {
        $fichas = FichasInactiva::where('iduser', $iduser)->get();
        $arbol = ['A'=> 0, 'B' => 1, 'C' => 2, 'D' => 3, 'E' => 4, 'F' => 5];
        $user = User::find($iduser);
        foreach ($fichas as $ficha) {
            $this->tableros($iduser, 1, $arbol[$ficha->arbol], $user, null);
        }
        FichasInactiva::where('iduser', $iduser)->delete();
        return redirect()->back()->with('msj', 'Fichas Insertadas Exitosamente');
    }
}
