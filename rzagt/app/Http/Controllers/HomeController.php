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
// include(app_path() .'/../public/PHPExcel/Classes/PHPExcel.php');
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
            // dd('ente');
            // return redirect('login');
        }else{
            // dd('entre');
                if (empty(Auth::user()->verificar_correo)) {
                    return redirect()->route('autenticacion.2fact');
                }else{
                    return redirect('admin');
                }

            
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
                        ->select('wp.ID', 'wp.post_title', 'wp.to_ping as porcentaje', 'wp.post_password as nivel_pago', 'wpm.meta_value')
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

            return redirect('office/admin/tienda');
        }else{
            return redirect('office/admin/tienda')->with('msj', 'Clave incorreta, intento nuevamente');
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
            'nombre_referido' => ($usuario) ? $usuario->display_name : 'Usuario no disponible',
            'phone' => $masinfo->phone,
            'wallet' => $llave->wallet_amount,
            '2fact' => (!empty($llave->verificar_correo)) ? 1 : 0
          ]);
        }

        return view('admin.userRecords')->with(compact('datos'));
    }


    /**
     * Permite activar el desactivar el 2 fact
     *
     * @param integer $block
     * @return void
     */
    public function disable2fact($iduser)
    {
        try {
            $user = User::find($iduser);
            $fact2 = 's2f';
            if (!empty($user->verificar_correo)) {
                $fact2 = null;
            }
            User::where('ID', $iduser)->update(['verificar_correo' => $fact2]);

            return redirect()->back()->with('msj', 'Google Autenticacion Actualizado con exito');
        } catch (\Throwable $th) {
            \Log::error('Resetear QR ->'.$th);
            return redirect()->back()->with('msj', 'Ocurrio un error al resetear el Codigo QR');
        }
    }

    public function changePorcent()
    {
        view()->share('title', 'Confi Porcent');
        return view('setting.porcent');
    }

    /**
     * Permite cambiar el lado donde se registra la matriz
     *
     * @param Request $request
     * @return void
     */
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
     * Permite restablecer el codigo QR del usuario
     *
     * @param integer $iduser
     * @return void
     */
    public function resetearQR($iduser)
    {
        try {
            User::where('ID', $iduser)->update(['check_token_google' => 0]);
            return redirect()->back()->with('msj', 'Codigo QR Reseteado con exito');
        } catch (\Throwable $th) {
            \Log::error('Resetear QR ->'.$th);
            return redirect()->back()->with('msj', 'Ocurrio un error al resetear el Codigo QR');
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
                return redirect('office/admin/userrecords')->with('msj', 'El usuario '.$nombreuser.' ha sido eliminado corretamente');
            } else {
                return redirect('office/admin/userrecords')->with('msj2', 'La clave del administrado es incorrecta');
            }
        }
    }
}
