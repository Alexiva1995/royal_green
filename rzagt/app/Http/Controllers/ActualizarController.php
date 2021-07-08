<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Settings;
use App\User;
use App\Rol;
use App\SettingsEstructura;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\AuditoriaController;
use Carbon\Carbon;

use PragmaRX\Google2FA\Google2FA;
use BaconQrCode\Renderer\Image\Png;
use BaconQrCode\Writer as BaconQrCodeWriter;

class ActualizarController extends Controller
{
    /**
     * Lleva la vista donde el usuario actualiza su perfil
     *
     * @return void
     */
    public function editProfile(){
        // TITLE
        view()->share('title', 'Editar Perfil');

        // DO MENU
        view()->share('do', collect(['name' => 'editProfile', 'text' => 'Editar Perfil']));
    
        $data = $this->infoUsuario(Auth::user()->ID);

        return view('dashboard.editProfile')->with(compact('data'));
    }

    /**
     * permite llenar la informacion del usuario en cuestion
     *
     * @param int $id - id de usuario
     * @return void
     */
    public function infoUsuario($id)
    {
        $user = User::find($id);
        $data = [
            'principal' => $user,
            'segundo' => DB::table('user_campo')->where('ID', $user->ID)->get(),
            'rol' => Rol::find($user->rol_id),
            'puntos' => json_decode($user->puntos),
            'referido' => (Auth::user()->ID != 1) ? User::find($user->referred_id)->only('display_name') : ['display_name' => 'Administrador'] ,
            'controler' => 'ActualizarController@updateProfile',
            // 'urlqr' => $this->createUserUrlQR($user)
        ];

        $data['segundo'] = $data['segundo'][0];
        return $data;
    }

    /**
     * Permite genera la llave secreta si no esta creada
     *
     * @param integer $iduser
     * @return string
     */
    public function generateSecretKey($iduser): string
    {
        $user = User::find($iduser);
        $urkqb = '';
        if ($user->check_token_google == 0) {
            $user->update(['toke_google' => (new Google2FA)->generateSecretKey()]);
            $urkqb = $this->createUserUrlQR($user);
        }

        return $urkqb;
    }


    /**
     * permite crear el codigo QR para el auteticador
     *
     * @param object $user
     * @return void
     */
    public function createUserUrlQR($user)
    {
        
        $renderer = new Png();
        $renderer->setWidth(200);
        $renderer->setHeight(200);
        $bacon = new BaconQrCodeWriter($renderer);
    
        $data = $bacon->writeString(
            (new Google2FA)->getQRCodeUrl(
                config('app.name'),
                $user->user_email,
                $user->toke_google
            ), 'utf-8');

        // $google2fa = (new \PragmaRX\Google2FAQRCode\Google2FA());

        // $qrCodeUrl = $google2fa->getQRCodeInline(
        //     config('app.name'),
        //     $user->user_email,
        //     $user->toke_google
        // );

        // dd($qrCodeUrl);
        // return $qrCodeUrl;
    
        return 'data:image/png;base64,' . base64_encode($data);
    }

    /**
     * Lleva a la vista donde el admin Actualiza el perfil de un usuario
     *
     * @param int $id
     * @return void
     */
    public function user_edit($id){
        // TITLE
        view()->share('title', 'Lista de Usuarios');

        // DO MENU
        view()->share('do', collect(['name' => 'editProfile', 'text' => 'Editar Perfil']));

        $data = $this->infoUsuario($id);

        return view('admin.userEdit')->with(compact('data'));
    }
    
    /**
     * Permite Actualizar la informacion de los usuario
     *
     * @param Request $request
     * @return view
     */
    public function updateProfile(Request $request){
        $settings = Settings::find(1);
        $settingEstructura = SettingsEstructura::find(1);
        $concepto = 'La sección '.$request->data.' ha sido actualizada ';
        $user = User::find($request->id);
        $userCampo = DB::table('user_campo')->where('ID', '=', $user->ID)->first();

        if ($request->data == 'general'){
            $validate = $request->validate([
                'firstname' => 'required',
                'lastname' => 'required',
                'genero' => 'required',
                'edad' => 'required',
                'nameuser' => 'required'
            ]);
           if ($validate) {
                if ($userCampo->firstname != $request['firstname']) {
                    $this->auditoria($user->ID, 'nombre', $userCampo->firstname, $request['firstname']);
                }
                if ($userCampo->lastname != $request['lastname']) {
                    $this->auditoria($user->ID, 'apellido', $userCampo->lastname, $request['lastname']);
                }
                if ($userCampo->genero != $request['genero']) {
                    $this->auditoria($user->ID, 'genero', $userCampo->genero, $request['genero']);
                }
                if ($userCampo->edad != $request['edad']) {
                    $this->auditoria($user->ID, 'edad', $userCampo->edad, $request['edad']);
                }
                if ($userCampo->nameuser != $request['nameuser']) {
                    $this->auditoria($user->ID, 'usuario', $userCampo->nameuser, $request['nameuser']);
                }

                DB::table('user_campo')
                    ->where('ID', '=', $user->ID)
                    ->update([
                            'firstname' => $request['firstname'], 
                            'lastname' => $request['lastname'], 
                            'genero' => $request['genero'],
                            'edad' => $request['edad'],
                            'nameuser' => $request['nameuser']
                            ]);

                $user->display_name = $request['firstname'].' '.$request['lastname'];
                $user->birthdate = $request['edad'];
                $user->gender = $request['genero'];
                $user->user_nicename = $request['nameuser'];
                $user->user_login = $request['nameuser'];
                
                $user->save();
           }
        }elseif ($request->data == 'contacto'){
            $validate = $request->validate([
                'clave' => 'confirmed',
                'dirección' => 'required',
                'codigo' => 'numeric',
                'phone' => 'numeric',
                'fijo' => 'numeric'
            ]);
            
            if ($validate) {
                if ($user->user_email != $request->user_email) {
                    if (Auth::user()->ID == 1) {
                        $validate2 = $request->validate([
                            'user_email' => 'required|max:100|unique:'.$settings->prefijo_wp.'users|confirmed',
                        ]);
                        if ($validate2) {
                            $this->auditoria($user->ID, 'email', $user->user_email, $request->user_email);
                            $user->user_email = $request->user_email;
                        }
                    }else{
                        $validate2 = $request->validate([
                            'user_email' => 'required|max:100|unique:'.$settings->prefijo_wp.'users|confirmed',
                            'code_email' => 'required', 
                            'code_google' => 'required'
                        ]);
                        if ($validate2) {
                            if ((new Google2FA())->verifyKey(Auth::user()->toke_google, $request->code_google)) {
                                if ($this->checkCodeEmail($request->code_email) == true) {
                                    $dataAuditoria = [
                                        'valor_old' => $user->user_email,
                                        'valor_new' => $request->user_email,
                                        'code_used' => 1
                                    ];
                                    $auditoriaController = new AuditoriaController();
                                    $auditoriaController->updateAuditoria($dataAuditoria, $user->ID, $request->code_email);
                                }else{
                                    return redirect()->back()->with('msj3', 'El codigo del correo es incorrecto');
                                }    
                            }else{
                                return redirect()->back()->with('msj3', 'El codigo de google es incorrecto');
                            }
                            
                            $user->user_email = $request->user_email;
                        }
                    }
                }
                
                $user->save();
                
                if ($userCampo->direccion != $request['dirección']) {
                    $this->auditoria($user->ID, 'dirección', $userCampo->direccion, $request['dirección']);
                }
                if ($userCampo->direccion2 != $request['direccion2']) {
                    $this->auditoria($user->ID, 'direccion 2', $userCampo->direccion2, $request['direccion2']);
                }
                if ($userCampo->pais != $request['pais']) {
                    $this->auditoria($user->ID, 'pais', $userCampo->pais, $request['pais']);
                }
                if ($userCampo->estado != $request['estado']) {
                    $this->auditoria($user->ID, 'estado', $userCampo->estado, $request['estado']);
                }
                if ($userCampo->ciudad != $request['ciudad']) {
                    $this->auditoria($user->ID, 'ciudad', $userCampo->ciudad, $request['ciudad']);
                }
                if ($userCampo->codigo != $request['codigo']) {
                    $this->auditoria($user->ID, 'codigo', $userCampo->codigo, $request['codigo']);
                }
                if ($userCampo->phone != $request['phone']) {
                    $this->auditoria($user->ID, 'celular', $userCampo->phone, $request['phone']);
                }
                if ($userCampo->fijo != $request['fijo']) {
                    $this->auditoria($user->ID, 'telefono', $userCampo->fijo, $request['fijo']);
                }

                DB::table('user_campo')
                    ->where('ID', '=', $user->ID)
                    ->update([
                        'direccion' => $request['dirección'], 
                        'direccion2' => $request['direccion2'], 
                        'pais' => $request['pais'],
                        'estado' => $request['estado'],
                        'ciudad' => $request['ciudad'],
                        'codigo' => $request['codigo'],
                        'phone' => $request['phone'],
                        'fijo' => $request['fijo'],
                        ]);
            }
        }elseif($request->data == 'password'){
            $validate = $request->validate([
                'clave' => 'confirmed',
                // 'code' => 'required'
            ]);

            // if ((new Google2FA())->verifyKey(Auth::user()->toke_google, $request->code)) {
                $this->auditoria($user->ID, 'clave', '', 'actualizacion');
                $user->user_pass = md5($request->clave);
                $user->password = bcrypt($request->clave);
                $user->clave = encrypt($request->clave);
                $user->save();
            // }else{
            //     return redirect()->back()->with('msj3', 'el codigo es incorrecto');
            // }
            
        }elseif ($request->data == 'social'){
            $validate = $request->validate([
                'binario_der' => 'required',
                'binario_izq' => 'required',
            ]);

            if ($validate) {
                $user = User::find($request->id);
                $puntos = json_decode($user->puntos);
                $puntos->binario_der = $request->binario_der;
                if ($puntos->binario_der != $request->binario_der) {
                    $this->auditoria($user->ID, 'puntos binarios derechos', $puntos->binario_der, $request->binario_der);
                }
                $puntos->binario_izq = $request->binario_izq;
                if ($puntos->binario_izq != $request->binario_izq) {
                    $this->auditoria($user->ID, 'puntos binarios izquierdos', $puntos->binario_izq, $request->binario_izq);
                }
                $user->puntos = json_encode($puntos);
                $user->save();

                $concepto = 'Puntos Binarios Actualizados Exitosamente';
            }
            
            
        }elseif ($request->data == 'auspiciado'){
            
            $validate = $request->validate([
                'id_referred' => 'numeric',
            ]);
            if ($validate) {
                $user = User::find($request->id);
                $user->referred_id = $request['id_referred'];
                if ($user->referred_id != $request['id_referred']) {
                    $this->auditoria($user->ID, 'patrocinador', $user->referred_id, $request['id_referred']);
                }
                $user->save();
            }
            
        }elseif ($request->data == 'pago'){
         
            DB::table('user_campo')
                ->where('ID', '=', $user->ID)
                ->update([
                            'paypal' => $request['paypal'],
                            ]);
            if ($userCampo->paypal != $request['paypal']) {
                $this->auditoria($user->ID, 'billetera', $userCampo->paypal, $request['paypal']);
            }
        }
        elseif($request->data == '2fact'){
            $validate = $request->validate([
                'code' => 'required|numeric'
            ]);

            if ((new Google2FA())->verifyKey(Auth::user()->toke_google, $request->code)) {
                $user->check_token_google = 1;
                $user->save();
                $concepto = 'Su validacion 2fact configurada con exito';
            }else{
                return redirect()->back()->with('msj', 'el codigo es incorrecto');
            }
        }
      

        if (Auth::user()->ID != 1){
            return redirect()->route('admin.user.edit')->with('msj', $concepto);
        }else{
            return redirect()->back()->with('msj', $concepto.' del usuario '.$user->display_name);
        }
    }

    /**
     * Llama a la funcion que va a guardar la auditoria
     *
     * @param integer $iduser
     * @param string $campo
     * @param string $valor_old
     * @param string $valor_new
     * @return void
     */
    public function auditoria(int $iduser, string $campo, $valor_old, $valor_new)
    {
        try {
            $auditoriaController = new AuditoriaController();
            $code = base64_encode(Carbon::now()->format('Y-m-d').'-'.$iduser.'-'.Carbon::now()->format('H-i-s'));
            $code_used = 1;
            if (Auth::user()->ID != 1) {
                $code_used = ($campo == 'email') ? 0 : 1;

                if ($code_used == 0) {
                    $user = User::find($iduser);
                    Mail::send('emails.codigoEmail', ['code' => $code], function ($msj) use ($user)
                    {
                        $msj->subject('Cambio de Correo');
                        $msj->to($user->user_email);
                    });
                }
            }
            $data = [
                'iduser' => $iduser,
                'campo' => ucfirst($campo),
                'valor_old' => $valor_old,
                'valor_new' => $valor_new,
                'code' => $code,
                'code_used' => $code_used,
                'user_change' => Auth::user()->display_name,
                'id_user_change' => Auth::user()->ID
            ];
            $auditoriaController->saveAuditoria($data);
            if ($code_used == 0) {
                return 1;
            }
        } catch (\Throwable $th) {
            \Log::error('Actualizar - auditoria ->'.$th);
            return 0;
        }

        
    }

    /**
     * llama a la funcion que revisa el codigo
     *
     * @param string $code
     * @return boolean
     */
    public function checkCodeEmail($code): bool
    {
        $auditoriaController = new AuditoriaController();
        return $auditoriaController->checkCode($code);
    }

    /**
     * Permite general el codigo para el correo 
     *
     * @param integer $iduser
     * @return void
     */
    public function generarCode($iduser)
    {
        return $this->auditoria($iduser, 'email', '', 'se actualiza despues');   
    }
}
