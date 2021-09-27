<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Country;
use App\Models\Timezone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use App\Rules\MatchOldPassword;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;

class UserController extends Controller
{

    // public function index()
    // {
    //     View::share('titleg', 'Usuarios');
    //     return view('users.index');
    // }
    
    /**
     * Vista de la lista de usuarios de la pagina
     *
     * @return void
     */
    public function listUser()
    {

       $user = User::all();

        return view('users.componenteUsers.admin.list-users')
        ->with('user',$user);

    }

    /**
     * Vista para el KYC
     *
     * @return void
     */
    public function kyc()
    {

         return view('users.componenteProfile.kyc');

    }

    /**
     * Funcion para actualizar el KYC
     *
     * @param Request $request
     * @return void
     */
    public function updateProfileKYC(Request $request)
    {

        $user = User::find(Auth::user()->id);

        $fields = [   
            "wallet_address" => ['string', 'min:21', 'max:35'],
        ];

        $msj = [  
            "wallet_address.min" => 'La dirección de la billetera debe tener un minimo de 21 caracteres',
            "wallet_address.max" => 'La dirección de la billetera no puede tener mas de 35 caracteres',
        ];

        $this->validate($request, $fields, $msj);

        $user->update($request->all());

     if($request->hasFile('dni')){

        $file = $request->file('dni');
        $name = $user->id.'_'.$file->getClientOriginalName();
        $file->move(public_path('storage') . '/dni', $name);
        $user->dni = $name;

     }

     $user->wallet_address = $request->wallet_address;

     $user->save();

     return redirect()->route('kyc')->with('msj-success','Se actualizo tu perfil');

    }

    /**
     * Vista para revisar la informacion del usuario
     *
     * @param [type] $id
     * @return void
     */
    public function showUser($id){

        $user = User::find($id);

        return view('users.componenteUsers.admin.show-user')
        ->with('user', $user);

    }

    /**
     * Vista para editar la informacion del usuario
     *
     * @param [type] $id
     * @return void
     */
    public function editUser($id)
    {

        $user = User::find($id);

        // $timezone = Timezone::orderBy('list_utc','ASC')->get();
        // $countries = Country::orderBy('name','ASC')->get();
 
 
        return view('users.componenteUsers.admin.edit-user')
              ->with('user',$user);
            //   ->with('countries',$countries)
            //   ->with('timezone',$timezone)

    }
    
    /**
     * Funcion para actualizar un usuario
     *
     * @param Request $request
     * @param [type] $id
     * @return void
     */

    // ¿Este metodo se usa? Parece que no, la que funciona es "updateProfile"
    public function updateUser(Request $request, $id)
    {
        $user = User::find($id);

        $request->validate([

            'current_password' => ['required', new MatchOldPassword],
            'new_password' => ['required'],
            'new_confirm_password' => ['same:new_password']

        ]);
   
        User::find(auth()->user()->id)->update(['password'=> Hash::make($request->new_password)]);

        $fields = [

             "fullname" => ['required'],
             "email" => [
                'required',
                'string',
                'email',
                'max:255',
            ],
            

        ];

        $msj = [

            'fullname.required' => 'El nombre completo es requerido',
            'email.unique' => 'El correo debe ser unico',
           
        ];


        $this->validate($request, $fields, $msj);
  
        $user->update($request->all());
  
        if ($request->hasFile('photoDB')) {

            $file = $request->file('photoDB');
            $name = $user->id.'_'.$file->getClientOriginalName();
            $file->move(public_path('storage') . '/photo', $name);
            $user->photoDB = $name;
    
         }

        $user->fullname = $request->fullname;
        // $user->utc = $request->utc;
        $user->admin = $request->admin;
        $user->status = $request->status;
        // $user->balance = $request->balance;
        // $user->website = $request->website;
        $user->whatsapp = $request->whatsapp;
        $user->address = $request->address;
        //$user->wallet_address = $request->wallet_address;

        $user->save();

        return redirect()->route('users.list-user')->with('msj-success','Se actualizo el perfil de '.$user->fullname.'');
    }

   /**
    * Vista para editar perfil
    *
    * @return void
    */
   public function editProfile()
   {

    //    $timezone = Timezone::orderBy('list_utc','ASC')->get();
    //    $countries = Country::orderBy('name','ASC')->get();

       $user = Auth::user();

       return view('users.profile')
             ->with('user',$user);
            //  ->with('countries',$countries)
            //  ->with('timezone',$timezone)

   }

    /**
     * Funcion para actualizar perfil
     *
     * @param Request $request
     * @return void
     */
    public function updateProfile(Request $request)
    {
        $user = User::find(Auth::user()->id);

        $fields = [
            "fullname" => ['required'],
            "email" => [
               'required',
               'string',
               'email',
               'max:255',
           ],
           'wallet_address' => ['min:21', 'max:35', 'nullable']
       
        ];

        $msj = [

            'name.required' => 'El nombre es requerido.',
            'last_name.required' => 'El apellido es requerido.',
            'email.unique' => 'El correo debe ser unico.',
            "wallet_address.min" => 'La dirección de la billetera debe tener un minimo de 21 caracteres.',
            "wallet_address.max" => 'La dirección de la billetera no puede tener mas de 35 caracteres.',

        ];

        $this->validate($request, $fields, $msj);

        $user->update($request->all());

     if ($request->hasFile('photoDB')) {

        $file = $request->file('photoDB');
        $name = $user->id.'_'.$file->getClientOriginalName();
        $file->move(public_path('storage') . '/photo', $name);
        $user->photoDB = $name;

     }

        $user->fullname = $request->fullname;
        $user->whatsapp = $request->whatsapp;

        $user->save();

        return redirect()->route('profile')->with('msj-success','Se actualizo tu perfil');

    }

    public function verifyUser(Request $request, $id)
    {

        $user = User::find($id);

        $user->update($request->all());

        $user->status = '1';
        $user->verify = '1';

        $user->save();

        return redirect()->route('users.list-user')->with('msj-success','Se Verifico el usuario Exitosamente');

    }

    /**
     * Funcion para eliminar un usuario
     *
     * @param [type] $id
     * @return void
     */
    public function destroyUser($id)
    {
      $user = User::find($id);
      
      $user->delete();
      
      return redirect()->route('users.list-user')->with('msj-success', 'Usuario '.$id.' Eliminado');
    }

    public function checkEmail($id)
    {
        $id = Crypt::decryptString($id);
        User::where('id', $id)->update(['email_verified_at' => Carbon::now()]);
        return redirect()->route('login')->with('msj-success', 'Correo Electronico confirmado');
    }
}

