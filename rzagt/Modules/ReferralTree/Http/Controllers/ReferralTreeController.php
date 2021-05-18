<?php

namespace Modules\ReferralTree\Http\Controllers;

use App\Http\Controllers\IndexController;
use Illuminate\Routing\Controller;
use App\SettingsEstructura;
use Illuminate\Support\Facades\Auth;
use App\User;

class ReferralTreeController extends Controller
{

    public $indexController;
    function __construct()
    {
        // TITLE
        view()->share('title', 'Arbol de Referidos');

        $this->indexController = new IndexController;
    }

    /**
     * Muestra el Inicio de Arbol 
     * 
     * 
     */
    public function index($type){
       // DO MENU
        view()->share('do', collect(['name' => 'inicio', 'text' => 'Inicio']));
        

        $check = count($this->indexController->getShopping(Auth::user()->ID));
        if ($check == 0) {
            return redirect()->route('tienda-index')->with('msj', 'Por favor realice una compra primero');
        }

        $puntos = json_decode(Auth::user()->puntos);

        $trees = $this->indexController->getDataEstructura(Auth::user()->ID, $type);
        $sideFinal = [];
        if (Auth::user()->ID != 1) {
            $sideFinal = $this->getLastBinary(Auth::user()->ID, $type);
        }
        // $type = ucfirst($type);
        $base = Auth::user();
        $base->children = User::where('position_id', '=', $base->ID)->get();
        $base->avatar = asset('avatar/'.$base->avatar);
        return view('referraltree::matriz')->with(compact('base', 'trees', 'type', 'puntos', 'sideFinal'));
        // return view('genealogy.tree', compact('trees', 'base'));

    }

    /**
     * Permite obtener los ultimos referido en cada lado
     *
     * @param integer $iduser
     * @return array
     */
    public function getLastBinary($iduser, $type): array
    {
        $data = [];
        if ($iduser != 1) {
            $trees = collect($this->indexController->getChidrens2($iduser, [], 1, 'position_id', 0));

            if ($trees->isNotEmpty()) {
                $izq = (!empty($trees->where('ladomatrix', 'I')->last())) ? $trees->where('ladomatrix', 'I')->last()->ID : 0;
                $der = (!empty($trees->where('ladomatrix', 'D')->last())) ? $trees->where('ladomatrix', 'D')->last()->ID : 0;
                if ($type == 'matriz') {
                    if ($izq > 0 && $der > 0) {
                        $data = [
                            'izq' => base64_encode($izq),
                            'der' => base64_encode($der)
                        ];
                    }
                }
            }
        }

        return $data;
    }


    /**
     * Lleva a la vista de arbol o matriz de un usuario hijo
     *
     * @param string $type
     * @param string $id
     * @return void
     */
    public function moretree($type, $id)
    {

        $id = base64_decode($id);
        $trees = $this->indexController->getDataEstructura($id, $type);
        // $type = ucfirst($type);
        $puntos = json_decode(Auth::user()->puntos);
        $base = User::find($id);
        if (Auth::user()->ID == 1) {
            $puntos = json_decode($base->puntos);
        }
        $sideFinal = [];
        $base->children = User::where('position_id', '=', $base->ID)->get();
        $base->avatar = asset('avatar/'.$base->avatar);
        return view('referraltree::matriz')->with(compact('base', 'trees', 'type', 'puntos', 'sideFinal'));
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
