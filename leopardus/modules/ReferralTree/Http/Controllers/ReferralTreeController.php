<?php

namespace Modules\ReferralTree\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Fichas;
use App\SettingsEstructura;
use Illuminate\Support\Facades\Auth;
use App\User;
use App\Rol;

class ReferralTreeController extends Controller
{
    function __construct()
    {
        // TITLE
        view()->share('title', 'Arbol de Referidos');
    }

    /**
     * Muestra el Inicio de Arbol 
     * 
     * 
     */
    public function index(){
       // DO MENU
        view()->share('do', collect(['name' => 'inicio', 'text' => 'Inicio']));
        //
        
        $settingEstructura = SettingsEstructura::find(1);
        $cantnivel = $settingEstructura->cantnivel;
        $principal = 'SI';
        $GLOBALS['cliente'] = (!empty(request()->user)) ? request()->user : 'Normal';
        if ($settingEstructura->tipoestructura == 'arbol') {
            $datos = $this->arbol(Auth::user()->ID, $cantnivel);
            $referidoBase = $datos['referidoBase'];
            $referidosAll = $datos['referidosAll'];
            return view('referraltree::arbol')->with(compact('referidoBase', 'referidosAll', 'principal'));
        } elseif ($settingEstructura->tipoestructura == 'matriz') {
            $datos = $this->matriz(Auth::user()->ID, $cantnivel);
            $referidoBase = $datos['referidoBase'];
            $referidosAll = $datos['referidosAll'];
            return view('referraltree::matriz')->with(compact('referidoBase', 'referidosAll', 'principal'));
        }else{
            if ($settingEstructura->estructuraprincipal == 1) {
                if ($settingEstructura->usuarioprincipal == 1) {
                    $datos = $this->arbol(Auth::user()->ID, $cantnivel);
                    $referidoBase = $datos['referidoBase'];
                    $referidosAll = $datos['referidosAll'];
                    return view('referraltree::arbol')->with(compact('referidoBase', 'referidosAll', 'principal'));
                } else {
                    $datos = $this->matriz(Auth::user()->ID, $cantnivel);
                    $referidoBase = $datos['referidoBase'];
                    $referidosAll = $datos['referidosAll'];
                    return view('referraltree::matriz')->with(compact('referidoBase', 'referidosAll', 'principal'));
                }
            } else {
                if ($settingEstructura->usuarioprincipal == 1) {
                    $datos = $this->arbol(Auth::user()->ID, $cantnivel);
                    $referidoBase = $datos['referidoBase'];
                    $referidosAll = $datos['referidosAll'];
                    return view('referraltree::arbol')->with(compact('referidoBase', 'referidosAll', 'principal'));
                } else {
                    $datos = $this->matriz(Auth::user()->ID, $cantnivel);
                    $referidoBase = $datos['referidoBase'];
                    $referidosAll = $datos['referidosAll'];
                    return view('referraltree::matriz')->with(compact('referidoBase', 'referidosAll', 'principal'));
                }
            }
        }
    }

    /**
     * Permite ver mis tableros de fichas
     *
     * @param string $tablero
     * @return void
     */
    public function indexTablero($tablero)
    {
        // DO MENU
        view()->share('do', collect(['name' => 'Tablero '.$tablero, 'text' => 'Tablero '.$tablero]));
        //
        $settingEstructura = SettingsEstructura::find(1);
        $padre = Fichas::where([
            ['idoriginal', '=', Auth::user()->ID],
            ['arbol', '=', $tablero]
        ])->first();
        $idpadre = 1;
        if(!empty($padre)) {
           $idpadre =  $padre->ID ;
        }else {
            return redirect('mioficina/admin')->with('msj2', 'No Existe Registro en el Tablero Selecionado con su Usuario');
        }
        $principal = 'SI';
        $GLOBALS['allUsers'] = [];
        $cantnivel = $settingEstructura->cantnivel;
        $referidoBase = Fichas::select('id', 'display_name', 'status', 'gender', 'created_at', 'paquete')
                            ->where([
                                    ['id', '=', $idpadre],
                                    ['arbol', '=', $tablero]
                                ])
                            ->first();
        
        $referidoBase = [
            'ID' => $referidoBase->id,
            'nombre' => $referidoBase->display_name,
            'picture' => $this->imgArbol($referidoBase->status, $referidoBase->gender),
            'avatar' => 'avatar.png',
            'rol' => 'Padre',
            'fechaingreso' => $referidoBase->created_at
        ];
        $directo = $this->getSponsorFicha($idpadre, $tablero);
        $this->getReferredsAllFicha($directo, 1, $cantnivel, [], 'matriz', $tablero);
        $referidosAll = $this->ordenarArreglosMultiDimensiones($GLOBALS['allUsers'], 'ID', 'numero');

        return view('referraltree::tablero')->with(compact('referidoBase', 'referidosAll', 'principal', 'tablero'));
    }

    /**
     * Permite mostrar el arbol de ficha en especial
     *
     * @param integer $id
     * @param string $tablero
     * @return void
     */
    public function moreTablero($id, $tablero)
    {
        // DO MENU
        view()->share('do', collect(['name' => 'Tablero '.$tablero, 'text' => 'Tablero '.$tablero]));
        //
        $settingEstructura = SettingsEstructura::find(1);
        $idpadre = $id;
        $principal = 'NO';
        $GLOBALS['allUsers'] = [];
        $cantnivel = $settingEstructura->cantnivel;
        $referidoBase = Fichas::select('id', 'display_name', 'status', 'gender', 'created_at')
                            ->where([
                                    ['id', '=', $idpadre],
                                    ['arbol', '=', $tablero]
                                ])
                            ->first();

        $referidoBase = [
            'ID' => $referidoBase->id,
            'nombre' => $referidoBase->display_name,
            'picture' => $this->imgArbol($referidoBase->status, $referidoBase->gender),
            'avatar' => 'avatar.png',
            'rol' => 'Padre',
            'fechaingreso' => $referidoBase->created_at
        ];
        $directo = $this->getSponsorFicha($idpadre, $tablero);
        $this->getReferredsAllFicha($directo, 1, $cantnivel, [], 'matriz', $tablero);
        $referidosAll = $this->ordenarArreglosMultiDimensiones($GLOBALS['allUsers'], 'ID', 'numero');

        return view('referraltree::tablero')->with(compact('referidoBase', 'referidosAll', 'principal', 'tablero'));
    }



    /**
     * Muesta la vista del cuando la estructura es arbol
     * 
     * @access private
     * @param int $id - id usuario, int $cantnivel - la cantidad de niveles de la estructura
     * @return array
     */
    private function arbol($id, $cantnivel)
    {
            $GLOBALS['allUsers'] = [];
            $referidoBase = User::select('id', 'display_name', 'status', 'gender', 'rol_id', 'created_at', 'avatar', 'paquete', 'icono_paquete')
                            ->where('id', '=', $id)
                            ->first();
                            
            $rol = Rol::find($referidoBase->rol_id);
            $logo = asset('assets/img/logo-light.png');
            $img_paquete = (!empty($referidoBase->icono_paquete)) ? asset('assets/'.$referidoBase->icono_paquete) : $logo;
             
            $referidoBase = [
                'ID' => $referidoBase->id,
                'imagenPaquete' => $img_paquete,
                'nombre' => $referidoBase->display_name,
                'picture' => $this->imgArbol($referidoBase->status, $referidoBase->gender),
                'avatar' => $referidoBase->avatar,
                'paquete' => $referidoBase->paquete,
                'rol' => $rol->name,
                'fechaingreso' => $referidoBase->created_at
            ];
            $directo = $this->getReferreds($id);
            $this->getReferredsAll($directo, 1, $cantnivel, [], 'arbol');
            // $referidosAll = $this->ordenarArreglosMultiDimensiones($GLOBALS['allUsers'], 'ID', 'numero');
            $referidosAll = $GLOBALS['allUsers'];
            $datos = [
                'referidoBase' => $referidoBase,
                'referidosAll' => $referidosAll,
            ];
            return $datos;
    }

    /**
     * Muesta la vista del cuando la estructura es matriz
     * 
     * @access private
     * @param int $id - id usuario, int $cantnivel - la cantidad de niveles de la estructura
     * @return array
     */
    private function matriz($id, $cantnivel)
    {
            $GLOBALS['allUsers'] = [];
            $referidoBase = User::select('id', 'display_name', 'status', 'gender', 'rol_id', 'created_at', 'avatar', 'paquete', 'icono_paquete', 'puntosder', 'puntosizq')
                            ->where('id', '=', $id)
                            ->first();
                            
            $rol = Rol::find($referidoBase->rol_id);
            $directo = $this->getSponsor($id);
            $logo = asset('assets/img/logo-light.png');
            $img_paquete = (!empty($referidoBase->icono_paquete)) ? asset('assets/'.$referidoBase->icono_paquete) : $logo;
            
            $referidoBase = [
                'ID' => $referidoBase->id,
                'imagenPaquete' => $img_paquete,
                'nombre' => $referidoBase->display_name,
                'picture' => $this->imgArbol($referidoBase->status, $referidoBase->gender),
                'avatar' => asset('avatar/'.$referidoBase->avatar),
                'paquete' => $referidoBase->paquete,
                'rol' => $rol->name,
                'auspiciador' => '',
                'fechaingreso' => $referidoBase->created_at,
                'cantsubreferido' => count($directo),
                'derecha' => $referidoBase->puntosder,
                'izquierda' => $referidoBase->puntosizq
            ];
            $this->getReferredsAll($directo, 1, $cantnivel, [], 'matriz');
            // $referidosAll = $this->ordenarArreglosMultiDimensiones(, 'ID', 'numero');
            $referidosAll = $GLOBALS['allUsers'];
            $datos = [
                'referidoBase' => $referidoBase,
                'referidosAll' => $referidosAll,
            ];
            return $datos;
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
                return $a[$clave] - $b[$clave] ;
            }
            
        };
    }


    /**
     * Permite revisar las estructa de los demas usuarios
     * 
     * @access public
     * @param int $id - id del usuario a revisar
     */
    public function moretree($id){
        $GLOBALS['cliente'] = (!empty(request()->user)) ? request()->user : 'Normal';
        $settingEstructura = SettingsEstructura::find(1);
        $cantnivel = $settingEstructura->cantnivel;
        $principal = 'NO';
        if ($settingEstructura->tipoestructura == 'arbol') {
            $datos = $this->arbol($id, $cantnivel);
            $referidoBase = $datos['referidoBase'];
            $referidosAll = $datos['referidosAll'];
            return view('referraltree::arbol')->with(compact('referidoBase', 'referidosAll', 'principal'));
        } elseif ($settingEstructura->tipoestructura == 'matriz') {
            $datos = $this->matriz($id, $cantnivel);
            $referidoBase = $datos['referidoBase'];
            $referidosAll = $datos['referidosAll'];
            return view('referraltree::matriz')->with(compact('referidoBase', 'referidosAll', 'principal'));
        }else{
            if ($settingEstructura->estructuraprincipal == 1) {
                if ($settingEstructura->usuarioprincipal == 1) {
                    $datos = $this->arbol($id, $cantnivel);
                    $referidoBase = $datos['referidoBase'];
                    $referidosAll = $datos['referidosAll'];
                    return view('referraltree::arbol')->with(compact('referidoBase', 'referidosAll', 'principal'));
                } else {
                    $datos = $this->matriz($id, $cantnivel);
                    $referidoBase = $datos['referidoBase'];
                    $referidosAll = $datos['referidosAll'];
                    return view('referraltree::matriz')->with(compact('referidoBase', 'referidosAll', 'principal'));
                }
            } else {
                if ($settingEstructura->usuarioprincipal == 1) {
                    $datos = $this->arbol($id, $cantnivel);
                    $referidoBase = $datos['referidoBase'];
                    $referidosAll = $datos['referidosAll'];
                    return view('referraltree::arbol')->with(compact('referidoBase', 'referidosAll', 'principal'));
                } else {
                    $datos = $this->matriz($id, $cantnivel);
                    $referidoBase = $datos['referidoBase'];
                    $referidosAll = $datos['referidosAll'];
                    return view('referraltree::matriz')->with(compact('referidoBase', 'referidosAll', 'principal'));
                }
            }
        }
    }

    /**
     * Permite revisar las estructa de los demas usuarios
     * 
     * @access public
     * @param request $datos - informacion del fomulario
     */
    public function moretree2(Request $datos){
        $validate = $datos->validate([
            'id' => 'required|numeric'
        ]);
        if ($validate) {
            $id = $datos->id;
            $verificar = User::find($id);
            if (empty($verificar)) {
                return redirect()->route('referraltree')->with('msj2', 'The Id: '.$id.' is not registered try with another id');
            }
            $GLOBALS['cliente'] = (!empty(request()->user)) ? request()->user : 'Normal';
            $directo = $this->getSponsor(Auth::user()->ID);
            $this->getReferredsAll($directo, 1, 10, [], 'matriz');
            $referidosAll = $GLOBALS['allUsers'];
            $check = false;
            if (!empty($referidosAll)) {
                foreach ($referidosAll as $item) {
                    if ($item['ID'] == $verificar->ID) {
                        $check = true;
                    }
                }
            }
            if (!$check) {
                return redirect()->back()->with('msj2', 'The Id: '.$id.' is not in your user network');;
            }
            
            $settingEstructura = SettingsEstructura::find(1);
            $cantnivel = $settingEstructura->cantnivel;
            $principal = 'NO';
            if ($settingEstructura->tipoestructura == 'arbol') {
                $datos = $this->arbol($id, $cantnivel);
                $referidoBase = $datos['referidoBase'];
                $referidosAll = $datos['referidosAll'];
                return view('referraltree::arbol')->with(compact('referidoBase', 'referidosAll', 'principal'));
            } elseif ($settingEstructura->tipoestructura == 'matriz') {
                $datos = $this->matriz($id, $cantnivel);
                $referidoBase = $datos['referidoBase'];
                $referidosAll = $datos['referidosAll'];
                return view('referraltree::matriz')->with(compact('referidoBase', 'referidosAll', 'principal'));
            }else{
                if ($settingEstructura->estructuraprincipal == 1) {
                    if ($settingEstructura->usuarioprincipal == 1) {
                        $datos = $this->arbol($id, $cantnivel);
                        $referidoBase = $datos['referidoBase'];
                        $referidosAll = $datos['referidosAll'];
                        return view('referraltree::arbol')->with(compact('referidoBase', 'referidosAll', 'principal'));
                    } else {
                        $datos = $this->matriz($id, $cantnivel);
                        $referidoBase = $datos['referidoBase'];
                        $referidosAll = $datos['referidosAll'];
                        return view('referraltree::matriz')->with(compact('referidoBase', 'referidosAll', 'principal'));
                    }
                } else {
                    if ($settingEstructura->usuarioprincipal == 1) {
                        $datos = $this->arbol($id, $cantnivel);
                        $referidoBase = $datos['referidoBase'];
                        $referidosAll = $datos['referidosAll'];
                        return view('referraltree::arbol')->with(compact('referidoBase', 'referidosAll', 'principal'));
                    } else {
                        $datos = $this->matriz($id, $cantnivel);
                        $referidoBase = $datos['referidoBase'];
                        $referidosAll = $datos['referidosAll'];
                        return view('referraltree::matriz')->with(compact('referidoBase', 'referidosAll', 'principal'));
                    }
                }
            }
        }
    }

    /**
     * Función que devuelve los patrocinados de un determinado usuario
     * 
     * @access private
     * @param int $id - id del usuario 
     * @return array
     */
    private function getSponsor($user_id){
        $tmp = User::select('*')
        ->where([['position_id', '=', $user_id], ['tipouser', '=', $GLOBALS['cliente']]])->orderBy('ladomatrix', 'DESC')->get()->toArray();
		return $tmp;
    }

    /**
     * Función que devuelve los patrocinados de un determinado usuario
     * 
     * @access private
     * @param int $id - id del usuario 
     * @param string $arbol
     * @return array
     */
    private function getSponsorFicha($user_id, $arbol){
        $tmp = Fichas::select('ID', 'user_email', 'status', 'display_name', 'gender', 'referred_id', 'created_at', 'position_id', 'tipouser')->where([
            ['position_id', '=', $user_id],
            ['arbol', '=', $arbol]
        ])->get()->toArray();
		return $tmp;
    }
    
    /**
     * Función que devuelve los referidos de un determinado usuario
     * 
     * @access private
     * @param int $id - id del usuario 
     * @return array
     */
    private function getReferreds($user_id){
        $tmp = User::select('*')
        ->where([['position_id', '=', $user_id], ['tipouser', '=', $GLOBALS['cliente']]])->orderBy('ladomatrix', 'DESC')->get()->toArray();
		return $tmp;
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
	public function getReferredsAllFicha($arregloUser, $niveles, $para, $allUser, $tipoestructura, $arbol)
    {
        if ($niveles <= $para) {
            $llaves =  array_keys($arregloUser);
            $finFor = end($llaves);
            $cont = 0;
            $tmparry = [];
            $subreferido = 0;
            foreach ($arregloUser as $user) {
                if (!empty($this->getSponsorFicha($user['ID'], $arbol))) {
                    $tmparry [] = $this->getSponsorFicha($user['ID'], $arbol);
                    $subreferido = 1;
                }
                $allUser [] = $this->llenarArreglo($user, $niveles, 'Ficha', $subreferido, 0);

                if ($finFor == $cont) {
                    if (!empty($tmparry)) {
                        $tmp2 = $tmparry[0];
                        for($i = 1; $i < count($tmparry); $i++){
                            $tmp2 = array_merge($tmp2,$tmparry[$i]);
                        }
                        $this->getReferredsAllFicha($tmp2, ($niveles+1), $para, $allUser, $tipoestructura, $arbol);
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
     * Obtienen a todo los usuarios referidos de un usuario determinado
     * 
     * @access private
     * @param array $arregloUser - listado de usuario, int $niveles - niveles a recorrer,
     * int $para - nivel a detenerse, array $allUser - todos los usuario referidos
     * @return array - listado de todos los usuario
     */
	private function getReferredsAll($arregloUser, $niveles, $para, $allUser, $tipoestructura)
    {
        $settingEstructura = SettingsEstructura::find(1);
        if ($niveles <= $para) {
            $llaves =  array_keys($arregloUser);
            $finFor = end($llaves);
            $cont = 0;
            $tmparry = [];
            $subreferido = 0;
            $cantReferido = 0;
            foreach ( $arregloUser as $user) {
                if ($tipoestructura == 'arbol') {
                    $referidosD = $this->getReferreds($user['ID']);
                    if (!empty($referidosD)) {
                        $tmparry [] = $referidosD;
                        $cantReferido = count($referidosD);
                        $subreferido = 1;
                    }
                }else{
                    $referidosD = $this->getSponsor($user['ID']);
                    if (!empty($referidosD)) {
                        $tmparry [] = $referidosD;
                        $cantReferido = count($referidosD);
                        $subreferido = 1;
                    }
                }
                $rol = Rol::find($user['rol_id']);
                $allUser [] = $this->llenarArreglo($user, $niveles, $rol->name, $subreferido, $cantReferido);
                // if ($tipoestructura == 'arbol') {
                // } else {
                //     if (($settingEstructura->cantfilas) >= $cont) {
                //         $allUser [] = $this->llenarArreglo($user, $niveles, $rol->name, $subreferido);
                //     }
                // }
                
                
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
     * Devuelva la informacion que va a en el arreglo
     * 
     * @access private
     * @param array $user - informacion del usuario, int $nivel - Nivel que esta el usuario, 
     *      string $rol - el rol del usuario, int $subreferido - si tiene subordinados
     * @return array
     */
    private function llenarArreglo($user, $nivel, $rol, $subreferido, $cantReferido)
    {
        $picturePersonalizada = null;
        $logo = asset('assets/img/logo-light.png');
        $img_paquete = (!empty($user['icono_paquete'])) ? asset('assets/'.$user['icono_paquete']) : $logo;
        $directo = User::find($user['referred_id']);
        $auspiciador = User::find($user['position_id']);
        return [
            'ID' => $user['ID'],
            'nombre' => $user['display_name'],
            'imagenPaquete' => $img_paquete,
            'picture' => (empty($picturePersonalizada)) ? $this->imgArbol($user['status'], $user['gender']) : $picturePersonalizada,
            'avatar' => asset('avatar/'.$user['avatar']),
            'subreferido' => $subreferido,
            'cantsubreferido' => $cantReferido,
            'nivel' => $nivel,
            'idpadre' => $user['position_id'],
            'idpatrocinador' => $user['position_id'],
            'directo' => $directo->display_name.' - ID: '.$directo->ID,
            'auspiciador' => $auspiciador->display_name,
            'rol' => $rol,
            'fechaingreso' => $user['created_at'],
            'paquete' => $user['paquete'],
            'ladomatrix' => $user['ladomatrix'],
            'derecha' => $user['puntosder'],
            'izquierda' => $user['puntosizq']
        ];
    }
	
	 

  private function imgArbol($estado, $genero)
  {
    $picture = '';
    if ($estado == '1'){
        if ($genero == 'F'){
            $picture = "avatares/Woman/N/1.png";
        }else{
            $picture = "avatares/Men/N/1.png";
        }
    }else{
        if ($genero == 'F'){
            $picture = "avatares/Woman/RC/1.png";
        }else{
            $picture = "avatares/Men/RC/1.png";
        }
    }
    return $picture;
  }

  /**
   * Obtiene un ID de Posicionamiento Valido 
   *
   * @param integer $id - primer id a verificar
   * @param string $lado - lado donde se insertara el referido
   * @return int
   */
  public function getPosition(int $id, string $lado)
  {
        $resul = 0;
        $settingEstructura = SettingsEstructura::find(1);
        $ids = $this->getIDs($id, $lado);
        if ($lado == 'I') {
            if (count($ids) == 0) {
                $resul = $id;
            }else{
                $this->verificarOtraPosition($ids, $settingEstructura->cantfilas, $lado);
                $resul = $GLOBALS['idposicionamiento'];
            }
        }elseif($lado == 'D'){
            if (count($ids) == 0) {
                $resul = $id;
            }else{
                $this->verificarOtraPosition($ids, $settingEstructura->cantfilas, $lado);
                $resul = $GLOBALS['idposicionamiento'];
            }
        }
        return $resul;
        
  }

  /**
   * Buscar Alternativas al los ID Posicionamiento validos
   *
   * @param array $arregloID - arreglos de ID a Verificar
   * @param int $limitePosicion - Cantdad de posiciones disponibles
   * @param string $lado - lado donde se insertara el referido
   */
  public function verificarOtraPosition($arregloID, $limitePosicion, $lado)
  {

    foreach ($arregloID as $item) {
        $ids = $this->getIDs($item['ID'], $lado);
        if ($lado == 'I') {
            if (count($ids) == 0) {
                $GLOBALS['idposicionamiento'] = $item['ID'];
                break;
            }else{
                $this->verificarOtraPosition($ids, $limitePosicion, $lado);
            }
        }elseif($lado == 'D'){
            if (count($ids) == 0) {
                $GLOBALS['idposicionamiento'] = $item['ID'];
                break;
            }else{
                $this->verificarOtraPosition($ids, $limitePosicion, $lado);
            }
        }
    }
  }
  /**
   * Obtiene los id que seran verificados en el posicionamiento
   *
   * @param integer $id
   * @param string $lado - lado donde se insertara el referido
   * @return array
   */
  public function getIDs(int $id, string $lado)
  {
      return User::where([
         ['position_id', '=',$id],
         ['ladomatrix', '=', $lado]
      ])->select('ID')->orderBy('ID')->get()->toArray();
  }

  /**
   * Obtiene un ID de Posicionamiento Valido 
   *
   * @param integer $id - primer id a verificar
   * @return int
   */
  public function getPosition2(int $id)
  {
        $resul = 0;
        $settingEstructura = SettingsEstructura::find(1);
        $cantPosiciones = User::where('position_id', $id)->get()->count('ID');
        if ($settingEstructura->cantfilas > $cantPosiciones) {
            $resul = $id;
        } else {
            $ids = $this->getIDs2($id);
            $GLOBALS['idposicionamiento'] = 0;
            $this->verificarOtraPosition2($ids, $settingEstructura->cantfilas);
                $resul = $GLOBALS['idposicionamiento'];
        }
        
        return $resul;
        
  }

  /**
   * Buscar Alternativas al los ID Posicionamiento validos
   *
   * @param array $arregloID - arreglos de ID a Verificar
   * @param int $limitePosicion - Cantdad de posiciones disponibles
   */
  public function verificarOtraPosition2($arregloID, $limitePosicion)
  {
    $tmparry = [];
    $llaves =  array_keys($arregloID);
    $finFor = end($llaves);
    $cont = 0;
    foreach ($arregloID as $item) {
        $cantPosiciones = User::where('position_id', $item['ID'])->get()->count('ID');
        if ($limitePosicion > $cantPosiciones) {
            if ($cantPosiciones == 0) {
                $GLOBALS['ladoposicionamiento'] = 'I';
            }elseif($cantPosiciones == 1){
                $GLOBALS['ladoposicionamiento'] = 'D';
            }
            $GLOBALS['idposicionamiento'] = $item['ID'];
            break;
        } else {
            $tmparry [] = $this->getIDs2($item['ID']);
            if ($finFor == $cont) {
                if (!empty($tmparry)) {
                    $tmp2 = $tmparry[0];
                    for($i = 1; $i < count($tmparry); $i++){
                        $tmp2 = array_merge($tmp2,$tmparry[$i]);
                    }
                    $this->verificarOtraPosition2($tmp2, $limitePosicion);
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
   * @return array
   */
  public function getIDs2(int $id)
  {
      return User::where('position_id', $id)->select('ID')->orderBy('ID')->get()->toArray();
  }

}