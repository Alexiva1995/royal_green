<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Formulario;
use Illuminate\Contracts\Hashing\Hasher as HasherContract;
use Carbon\Carbon; 
use App\Settings;
include(app_path() .'/../public/PHPExcel/Classes/PHPExcel.php');
use PHPExcel;
use PHPExcel_IOFactory;
use Reflector;

use function GuzzleHttp\json_encode;

class HomeController extends Controller
{
    public function __construct(HasherContract $hasher)
    {
        $this->hasher = $hasher;
    }

    public function index()
    {
        if (Auth::guest()){
            return redirect('mioficina/login');
        }else{
            // if (empty(Auth::user()->verificar_correo)) {
                // return redirect('login')->with('msj3', 'Your Email has not been Validated, check the email that registered in the system');
            // }else{
                if (Auth::user()->check_token_google == 1) {
                    return redirect()->route('autenticacion.2fact');
                }else{
                    return redirect('mioficina/admin');
                }
            // }
            // $cliente = SettingCliente::find(1);
            // if ($cliente->permiso == 0 && Auth::user()->tipouser == 'Cliente') {
            //     return redirect('login')->with('msj3', 'Restringido el Acceso');
            // } else {
            //     return redirect('/admin');
            // }
            
        }

        //return view('welcome');
    }

    /**
     * LLeva a la vista para la activacion manual de los productos
     *
     * @return void
     */
    public function userActiveManual()
    {
        view()->share('title', 'Usuarios Inactivos');
        $settings = Settings::first();
        $product = DB::table($settings->prefijo_wp.'posts as wp')
                    ->where([
                        ['wp.post_type', '=', 'product'],
                    ])
                    ->select('wp.ID', 'wp.post_title')
                    ->get();
        $users = User::where([
            // ['rol_id', '!=', 0],
            ['status', '=', 0],
            ['activacion', '=', 0],
            ['fecha_activacion', '=', null]
        ])->get();
        
        foreach ($users as $user) {
            $campo_user = DB::table('user_campo')->where('ID', '=', $user->ID)->select('phone')->first();
            $patrocinador = User::find($user->referred_id);
            $user->nombre_referido = (Auth::user()->ID != 1) ? $patrocinador->display_name : 'Admin';
            $user->phone = $campo_user->phone;
        }

        return view('admin.userActiveManual')->with(compact('product', 'users'));
    }

    public function saveActiveManual(Request $request)
    {
        $validate = $request->validate([
            'paquete' => 'required'
        ]);
        if ($validate) {
            $settings = Settings::first();
            $product = DB::table($settings->prefijo_wp.'posts as wp')
                        ->join($settings->prefijo_wp.'postmeta as wpm', 'wp.ID', '=', 'wpm.post_id' )
                        ->where([
                            ['wpm.meta_key', '=', '_price'],
                            ['wp.post_type', '=', 'product'],
                            ['wp.ID', '=', $request->paquete]
                        ])
                        ->select('wp.ID', 'wp.post_title', 'wp.to_ping as porcentaje', 'wp.post_password as nivel_pago', 'wpm.meta_value',)
                        ->first();
            $paquete = [
                'nombre' => $product->post_title,
                'ID' => $product->ID,
                'monto' => $product->meta_value,
                'nivel' => $product->nivel_pago,
                'porcentaje' => ($product->porcentaje / 100)
            ];
            $icono_paquete = 'MASTER.png';
            $rentabilidad = ($product->meta_value * 2);
            $paquete = json_encode($paquete);
            $user = User::find($request->iduser);
            DB::table($settings->prefijo_wp.'users')
                        ->where('ID', '=', $request->iduser)
                        ->update([
                            'status' => true,
                            'fecha_activacion' =>  Carbon::now(),
                            'paquete' => $paquete,
                            'activacion' => 1,
                            'rentabilidad' => $rentabilidad,
                            'icono_paquete' => '/img/paquetes/'.$icono_paquete
                        ]);
            
            return redirect()->route('admin.userinactive')->with('msj', 'El usuario '.$user->display_name.' Fue activado con exito con el producto '.$product->post_title);
        }
        
    }

    public function guardar_clave(Request $request){
        $user = User::find(Auth::user()->ID);

        if (Hash::check($request->clave, $user->password)){
            $user->clave = encrypt($request->clave);
            $user->save();

            return redirect('mioficina/admin/tienda');
        }else{
            return redirect('mioficina/admin/tienda')->with('msj', 'Clave incorreta, intento nuevamente');
        }
    }

    public function user_records(){
         view()->share('title', 'Listado de Usuarios');

            // DO MENU
        view()->share('do',
                collect(['name' => 'usuarios', 'text' => 'Listado de Usuarios']));

        $datos = [];
        $settings = Settings::first();
        $usuarios = DB::table($settings->prefijo_wp.'users')
                        ->orderBy('display_name', 'ASC')
                        ->get();


        foreach ($usuarios as $llave) {
          $usuario = User::find($llave->referred_id);
          $masinfo = DB::table('user_campo')->where('ID', $llave->ID)->select('phone')->first();
          array_push($datos, [
            'ID' => $llave->ID,
            'display_name' => $llave->display_name,
            'user_email' => $llave->user_email,
            // 'country' => $llave->country,
            'rol_id' => $llave->rol_id,
            'status' => $llave->status,
            'nombre_referido' => $usuario['display_name'],
            'phone' => $masinfo->phone
          ]);
        }

        return view('admin.userRecords')->with(compact('datos'));
    }

    public function changePorcent()
    {
        view()->share('title', 'Confi Porcent');
        return view('setting.porcent');
    }

    public function changeSide(Request $request)
    {
        $validate = $request->validate([
            'ladoregistrar' => ['required']
        ]);
        if ($validate) {
            Auth::user()->update(['ladoregistrar' => $request->ladoregistrar]);
            return redirect()->back()->with('msj', 'Lado actualizado con exito');
        }
    }

    /**
     * Permite borrar a un usuario en especifico
     *
     * @return void
     */
    public function deleteProfile(Request $datos)
    {
        $validate = $datos->validate([
            'userdelete' => 'required',
            'clave' => 'required'
        ]);
        if ($validate) {
            $admin = User::find(1);
            if ($this->hasher->check($datos['clave'], $admin->password)) {
                $id = $datos->userdelete; 
                $usuarioBorrar = User::find($id);
                $usuariosreferidos = User::where('referred_id', $id)->get()->toArray();
                if (!empty($usuariosreferidos)) {
                    foreach ($usuariosreferidos as $key ) {
                    $usuario = User::find($key['ID']);
                    $usuario->referred_id = $usuarioBorrar->referred_id;
                    $usuario->save();
                    }
                }
                $nombreuser = $usuarioBorrar->display_name;
                DB::table('user_campo')->where('ID', $usuarioBorrar->ID)->delete();
                $usuarioBorrar->delete();
                return redirect('mioficina/admin/userrecords')->with('msj', 'El usuario '.$nombreuser.' ha sido eliminado corretamente');
            } else {
                return redirect('mioficina/admin/userrecords')->with('msj2', 'La clave del administrado es incorrecta');
            }
            
        }
    }

    
    /**
     * Registro de la licencia para el uso del sistema
     * 
     * @access public
     * @param request $datos - lincecia a registrar
     * @return view
     */
    public function saveLicencia(Request $datos)
    {
        $validate = $datos->validate([
            'licencia' => 'required'
        ]);

        if ($validate) {
            $tmp = convert_uudecode(base64_decode($datos->licencia));
            $array = explode('|', $tmp);
            $fecha = new Carbon($array[1]);
            $settings = Settings::first();
            if (strcasecmp($array[0], $settings->name) === 0) {
                DB::table('settings')->where('id', 1)->update([
                    'licencia' => $datos->licencia,
                    'fecha_vencimiento' => $fecha
                ]);
                return redirect('login')->with('msj2', 'Licencia Registrada Con Exito, se vence el '.date('d-m-Y', strtotime($array[1])));
            } else {
                return redirect('login')->with('msj3', 'Licencia No Valida, Comuniquese con el Administrador');
            }
            
        }
    }

    public function user_print(){
        // view()->share('title', 'Listado de Usuarios');

        $settings = Settings::first();

           // DO MENU
    //    view()->share('do',
    //            collect(['name' => 'usuarios', 'text' => 'Listado de Usuarios']));

       $sql="SELECT uc.*, (SELECT name FROM roles WHERE id=wu.rol_id) as 'rol', wu.created_at, (SELECT wu2.display_name from ".$settings->prefijo_wp."users as wu2 where wu2.ID=wu.referred_id) as patrocinador FROM ".$settings->prefijo_wp."users wu inner join user_campo uc on (wu.ID = uc.ID) order by wu.ID asc ";
           $usuarios =DB::select($sql);
    //     $user = DB::table('user_campo')->selec('*')->get();
        $formularios = Formulario::select('label', 'nameinput')->orderBy('id')->get();
       if (PHP_SAPI == 'cli'){
           die('Este archivo solo se puede ver desde un navegador web');
       }

       $objPHPExcel = new PHPExcel();
       
       $tituloReporte = "Datos del los Usuarios ".$settings->name;
       $titulosColumnas = [];
       foreach ($formularios as $item) {
        $titulosColumnas [] = $item->label;
       }
       $titulosColumnas [] = 'Rol'; $titulosColumnas [] = 'Fecha de Registro'; $titulosColumnas [] = 'Patrocinador';
       $letras= array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM', 'AN', 'AO', 'AP', 'AQ', 'AR', 'AS', 'AT', 'AU', 'AV', 'AW', 'AX', 'AY', 'AZ');

       
       $objPHPExcel->getProperties()->setCreator("Codedrinks") // Nombre del autor
           ->setLastModifiedBy("Codedrinks") //Ultimo usuario que lo modific���
           ->setTitle("Reporte Excel Usuarios ".$settings->name) // Titulo
           ->setSubject("Reporte Excel Usuarios ".$settings->name) //Asunto
           ->setDescription("Reporte de Usuarios ".$settings->name) //Descripci���n
           ->setKeywords("reporte Usuarios ".$settings->name) //Etiquetas
           ->setCategory("Reporte excel"); //Categorias
       // Se combinan las celdas A1 hasta D1, para colocar ah��� el titulo del reporte
       $objPHPExcel->setActiveSheetIndex(0)
           ->mergeCells('A1:D1');

       // Se agregan los titulos del reporte
       $objPHPExcel->setActiveSheetIndex(0)
       ->setCellValue('A1',$tituloReporte); // Titulo del reporte


       for ($i=0; $i <count($titulosColumnas) ; $i++) {

           $objPHPExcel->setActiveSheetIndex(0)->setCellValue($letras[$i].'3', utf8_decode($titulosColumnas[$i]));
       }

       $i=4;
       $letra=0;
       foreach ($usuarios as $usuario) {
           foreach ($formularios as $input) {
               $objPHPExcel->setActiveSheetIndex(0)
               ->setCellValue($letras[0].$i, $usuario[$input->nameinput])
               ->setCellValue($letras[0].$i, $usuario->rol)
               ->setCellValue($letras[0].$i, $usuario->created_at)
               ->setCellValue($letras[0].$i, $usuario->patrocinador);
            }
            $i++;
       }
       for($i = 'A'; $i <= count($titulosColumnas); $i++){
           $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension($i)->setAutoSize(TRUE);
       }

       // Se asigna el nombre a la hoja
       $objPHPExcel->getActiveSheet()->setTitle('Usuarios '.$settings->name);

       // Se activa la hoja para que sea la que se muestre cuando el archivo se abre
       $objPHPExcel->setActiveSheetIndex(0);

       // Inmovilizar paneles
       $objPHPExcel->getActiveSheet(0)->freezePane('A4');
       $objPHPExcel->getActiveSheet(0)->freezePaneByColumnAndRow(0,4);
       $objPHPExcel->setActiveSheetIndex(0);
       header("Pragma: no-cache");
       header('Content-type: application/vnd.ms-excel');
       header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
       header('Content-Disposition: attachment;filename="Usuarios '.$settings->name.' - '.Carbon::now()->format('d-m-Y').'.xlsx"');
       header('Cache-Control: max-age=0'); $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
       $objWriter->save('php://output');
       exit;

   }
}
