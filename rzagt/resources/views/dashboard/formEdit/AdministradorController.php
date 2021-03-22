<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request; use App\Notification;
use App\Models\User; use App\Models\Commission; use App\Models\Liquidation; use App\Models\Audit;
use App\Models\Coupon; use App\Models\UserChange;
use DB; use Carbon\Carbon; use Auth; 
use App\Http\Controllers\TreeController; 
use App\Http\Controllers\CommissionController; use Session; use Mail;
use GuzzleHttp\Client;


class AdministradorController extends Controller{
   function __construct(){
      // TITLE
      view()->share('title', 'Admin');
   }

   //*** Admin / Inicio ***//
   public function index(){
      try{
         // DO MENU
         view()->share('do', collect(['name' => 'Inicio', 'text' => 'Inicio']));
         //

         
            //API URL
            $url = 'https://witpay.witcash.io:5001/api/apiws';

            $data = array();
            //create a new cURL resource
            //$ch = curl_init($url);

            //setup request to send json via POST
            $data = array(               
               ["userId" => 370,"addressBTC" => "15HHqWsbFQfgtftyoUzwNncFjZ7VcwLr5J","valueUSD" => 1,"txId" => 6698],

            );
            





            $payload = json_encode(
               array(
                  "apikey" => 'efb4b4308386f977400861142d5d755e646f6e5d4b81fd8b29634a43ff0e850e',
                  "apisecret" => 'a2f942f3796c6a6a7262abfe6a41c946e7accc7b027fb173ee09a58bba08ec8a-3d2c7251cd',
                  "method" => 'doWithdraw',
                  "data" => array('payments' => $data, 'callbackUrl' => 'https://universal-profits.com/office/payment-status-update'))
               );


               
               // $client = new Client([ 'headers' => [ 'Content-Type' => 'application/json' ] ]); 
               // $response = $client->post($url, ['body' => $payload]); 
               // dd($response);

            //dd($payload);
            /*  
            //attach encoded JSON string to the POST fields
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

            //set the content type to application/json
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));

            //return response instead of outputting
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            //execute the POST request
            $result = curl_exec($ch);

            //close cURL resource
            curl_close($ch);            
            */
               
         if (Auth::user()->status == 1) {
            $usuariosActivos = DB::table('webp_users')
                                    ->where('ID', '<>', 1)
                                    ->where('status', '=', 1)
                                    ->count();

            $usuariosInactivos = DB::table('webp_users')
                                    ->where('ID', '<>', 1)
                                    ->where('status', '=', 0)
                                    ->count();

            $totalUsuarios = $usuariosActivos + $usuariosInactivos;

            $ticketsAbiertos = DB::table('tickets')
                                 ->where('status_ticket', '=', 'Abierto')
                                 ->count();
               
            return view('admin.index')->with(compact('usuariosActivos', 'usuariosInactivos', 'totalUsuarios', 'ticketsAbiertos'));
         }else{
            Auth::logout();
            Session::flash('msj-status', '');
            return redirect()->route('login');
         }
      }catch (\Exception $e) {
         $error = explode('Stack trace', $e);
         \Log::error("Admin / Inicio -> ".$error[0]);
      }catch (\Throwable $ex) {
         $error = explode('Stack trace', $ex);
         \Log::error("Admin / Inicio -> ".$error[0]);
      }

      return view('errors.error');
   }

   //***** Admin / Clientes / Ver Redes y Árboles **//
   public function show_networks_and_trees(){
      try{
         view()->share('title', 'Mostrar Redes de Usuario');
         view()->share('do', collect(['name' => 'Redes de Usuario', 'text' => 'Redes de Usuario']));

         return view('admin.client.showNetworksAndTrees');
      }catch (\Exception $e) {
         $error = explode('Stack trace', $e);
         \Log::error("Admin / Clientes / Mostrar Redes -> ".$error[0]);
      }catch (\Throwable $ex) {
         $error = explode('Stack trace', $ex);
         \Log::error("Admin / Clientes / Mostrar Redes -> ".$error[0]);
      }

      return view('errors.error');
   }

   //***** Admin / Clientes / Ver Redes y Árboles / Elegir Red o Árbol **//
   public function choose_network_or_tree(Request $request){
      try{
         $usuario = DB::table('webp_users')
                     ->where('ID', '=', $request->usuario)
                     ->first();

         if (!is_null($usuario)){
            if ($request->seleccion == 'rd'){
               return redirect('admin/client/show-direct-referrals/'.$request->usuario);
            }else if ($request->seleccion == 'rr_arbol'){
               return redirect('admin/client/show-network-records-tree/'.$request->usuario.'/'.$request->usuario);
            }else if ($request->seleccion == 'rr_matriz'){
               return redirect('admin/client/show-network-records-matrix/'.$request->usuario.'/'.$request->usuario);
            }else if ($request->seleccion == 'rr_binario'){
               return redirect('admin/client/show-network-records-binary/'.$request->usuario.'/'.$request->usuario);
            }else if ($request->seleccion == 'arbol_d'){
               return redirect('admin/client/show-referral-tree/'.$request->usuario.'/1');
            }else if ($request->seleccion == 'matriz'){
               return redirect('admin/client/show-referral-matrix/'.$request->usuario.'/1');
            }else if ($request->seleccion == 'arbol_si'){
               return redirect('admin/client/show-smart-investor-tree/'.$request->usuario.'/1');
            }else if ($request->seleccion == 'arbol_e'){
               return redirect('admin/client/show-team-tree/'.$request->usuario.'/1');
            }else if ($request->seleccion == 'arbol_b'){
               return redirect('admin/client/show-binary-tree/'.$request->usuario.'/1');
            }
         }else{
            return redirect('admin/client/show-networks-and-trees')->with('msj-error', 'El ID ingresado no se encuentra en los registros de usuario');
         }
      }catch (\Exception $e) {
         $error = explode('Stack trace', $e);
         \Log::error("Admin / Clientes / Mostrar Redes -> ".$error[0]);
      }catch (\Throwable $ex) {
         $error = explode('Stack trace', $ex);
         \Log::error("Admin / Clientes / Mostrar Redes -> ".$error[0]);
      }

      return view('errors.error');
   }

   //***** Admin / Auditoría / Registro de Operaciones Aprobadas **//
   public function audit_records(Request $request){
      try{
         view()->share('title', 'Auditoría');
         view()->share('do', collect(['name' => 'Registro de Operaciones', 'text' => 'Registro de Operaciones']));

         $cantOperaciones = DB::table('audit_records')
                              ->where('status', '=', 1)
                              ->count();

         $operaciones = Audit::administrador($request->get('admin'))
                           ->desde($request->get('fecha_inicial'))
                           ->hasta($request->get('fecha_final'))
                           ->where('status', '=', 1)
                           ->orderBy('date', 'DESC')
                           ->take(2000)
                           ->get();

         $administradores = DB::table('webp_users')
                              ->select('ID', 'user_email')
                              ->where('admin', '=', 1)
                              ->orderBy('id', 'ASC')
                              ->get();

         return view('admin.audit.auditRecords')->with(compact('operaciones', 'administradores', 'cantOperaciones'));
      }catch (\Exception $e) {
         $error = explode('Stack trace', $e);
         \Log::error("Admin / Auditoría / Registro de Operaciones -> ".$error[0]);
      }catch (\Throwable $ex) {
         $error = explode('Stack trace', $ex);
         \Log::error("Admin / Auditoría / Registro de Operaciones -> ".$error[0]);
      }

      return view('errors.error');
   }

   //***** Admin / Auditoría / Registro de Operaciones Denegadas **//
   public function unapproved_audits_record(Request $request){
      try{
         view()->share('title', 'Auditoría');
         view()->share('do', collect(['name' => 'Registro de Operaciones', 'text' => 'Registro de Operaciones']));

         $operaciones = Audit::administrador($request->get('admin'))
                           ->desde($request->get('fecha_inicial'))
                           ->hasta($request->get('fecha_final'))
                           ->where('status', '=', 2)
                           ->orderBy('date', 'DESC')
                           ->get();

         $administradores = DB::table('webp_users')
                              ->select('ID', 'user_email')
                              ->where('admin', '=', 1)
                              ->orderBy('id', 'ASC')
                              ->get();

         return view('admin.audit.unapprovedAuditsRecord')->with(compact('operaciones', 'administradores'));
      }catch (\Exception $e) {
         $error = explode('Stack trace', $e);
         \Log::error("Admin / Auditoría / Registro de Operaciones Denegadas -> ".$error[0]);
      }catch (\Throwable $ex) {
         $error = explode('Stack trace', $ex);
         \Log::error("Admin / Auditoría / Registro de Operaciones Denegadas -> ".$error[0]);
      }

      return view('errors.error');
   }

   //***** Admin / Auditoría / Registro de Operaciones de Clientes **//
   public function client_changes_record(Request $request){
      try{
         view()->share('title', 'Auditoría');
         view()->share('do', collect(['name' => 'Registro de Operaciones', 'text' => 'Registro de Operaciones']));

         $operaciones = UserChange::desde($request->get('fecha_inicial'))
                           ->hasta($request->get('fecha_final'))
                           ->where('status', '<>', 0)
                           ->orderBy('date', 'DESC')
                           ->get();

         return view('admin.audit.clientChangesRecord')->with(compact('operaciones'));
      }catch (\Exception $e) {
         $error = explode('Stack trace', $e);
         \Log::error("Admin / Auditoría / Registro de Operaciones de Clientes -> ".$error[0]);
      }catch (\Throwable $ex) {
         $error = explode('Stack trace', $ex);
         \Log::error("Admin / Auditoría / Registro de Operaciones de Clientes -> ".$error[0]);
      }

      return view('errors.error');
   }

   //***** Admin / Auditoría / Solicitudes de Clientes **//
   public function client_changes_request(Request $request){
      try{
         view()->share('title', 'Auditoría');
         view()->share('do', collect(['name' => 'Solicitudes de Clientes', 'text' => 'Solicitudes de Clientes']));

         $operaciones = UserChange::desde($request->get('fecha_inicial'))
                           ->hasta($request->get('fecha_final'))
                           ->where('status', '=', 0)
                           ->orderBy('date', 'DESC')
                           ->get();

         return view('admin.audit.clientChangesRequest')->with(compact('operaciones'));
      }catch (\Exception $e) {
         $error = explode('Stack trace', $e);
         \Log::error("Admin / Auditoría / Solicitudes de Clientes -> ".$error[0]);
      }catch (\Throwable $ex) {
         $error = explode('Stack trace', $ex);
         \Log::error("Admin / Auditoría / Solicitudes de Clientes -> ".$error[0]);
      }

      return view('errors.error');
   }

   //***** Admin / Auditoría / Solicitudes de Clientes / Aprobar - Rechazar **//
   public function process_client_changes(Request $request){
      try{

         $cambio = UserChange::find($request->operacion);
         $usuario = User::find($cambio->user_id);

         if ($request->accion == 'A'){
            switch ($cambio->field) {
               case 'DNI':
                  $usuario->dni = $cambio->new_value;
                  $usuario->save();

                  $cambio->status = 1;
                  $cambio->admin = Auth::user()->ID;
               break;

               case 'Nombres':
                  $usuario->names = $cambio->new_value;
                  $usuario->display_name = $cambio->new_value." ".$usuario->last_names;
                  $usuario->save();
                  
                  $cambio->status = 1;
                  $cambio->admin = Auth::user()->ID;
               break;

               case 'Apellidos':
                  $usuario->last_names = $cambio->new_value;
                  $usuario->display_name = $usuario->names." ".$cambio->new_value;
                  $usuario->save();
                  
                  $cambio->status = 1;
                  $cambio->admin = Auth::user()->ID;
               break;

               case 'Fecha de Nacimiento':
                  $usuario->birthdate = $cambio->new_value;
                  $usuario->save();
                  
                  $cambio->status = 1;
                  $cambio->admin = Auth::user()->ID;
               break;

               case 'Correo Electrónico':
                  $usuario->user_email = $cambio->new_value;
                  $usuario->save();
                  
                  $cambio->status = 1;
                  $cambio->admin = Auth::user()->ID;
               break;
            }

            if ( ($usuario->names) != NULL || ($usuario->names != "") ){
               $data['usuario'] = $usuario->names." ".$usuario->last_names;
            }else{
               $data['usuario'] = $usuario->display_name;
            }     
            $data['correo'] = $usuario->user_email;
            $data['campo'] = $cambio->field;
            $data['accion'] = $request->accion;

            Mail::send('emails.processClientChange',  ['data' => $data], function($msj) use ($data){
               $msj->subject(' Universal Profits - No responder - Cambio de Información Personal');
               $msj->to($data['correo']);
               $msj->from('corporate@universal-profits.com');
            });
         }else{
            $cambio->status = 2;
            $cambio->admin = Auth::user()->ID;
            $cambio->admin_comments = $request->comentarios;

            if ( ($usuario->names) != NULL || ($usuario->names != "") ){
               $data['usuario'] = $usuario->names." ".$usuario->last_names;
            }else{
               $data['usuario'] = $usuario->display_name;
            }     
            $data['correo'] = $usuario->user_email;
            $data['campo'] = $cambio->field;
            $data['accion'] = $request->accion;
            $data['comentarios'] = $request->comentarios;

            Mail::send('emails.processClientChange',  ['data' => $data], function($msj) use ($data){
               $msj->subject(' Universal Profits - No responder - Cambio de Información Personal');
               $msj->to($data['correo']);
               $msj->from('corporate@universal-profits.com');
            });
         }

         $cambio->save();

         return redirect('admin/audit/client-changes-request')->with('msj', 'La solicitud ha sido procesada con éxito');
      }catch (\Exception $e) {
         $error = explode('Stack trace', $e);
         \Log::error("Admin / Auditoría / Solicitudes de Clientes -> ".$error[0]);
      }catch (\Throwable $ex) {
         $error = explode('Stack trace', $ex);
         \Log::error("Admin / Auditoría / Solicitudes de Clientes -> ".$error[0]);
      }

      return view('errors.error');
   }

   //***** Admin / Auditoría / Operaciones Pendientes **//
   public function pending_changes(){
      try{
         view()->share('title', 'Auditoría');
         view()->share('do', collect(['name' => 'Operaciones Pendientes', 'text' => 'Operaciones Pendientes']));

         $operaciones = DB::table('audit_records')
                           ->where('status', '=', 0)
                           ->orderBy('date', 'DESC')
                           ->get();

         return view('admin.audit.pendingChanges')->with(compact('operaciones'));
      }catch (\Exception $e) {
         $error = explode('Stack trace', $e);
         \Log::error("Admin / Auditoría / Operaciones Pendientes -> ".$error[0]);
      }catch (\Throwable $ex) {
         $error = explode('Stack trace', $ex);
         \Log::error("Admin / Auditoría / Operaciones Pendientes -> ".$error[0]);
      }

      return view('errors.error');
   }

   //***** Admin / Auditoría / Operaciones Pendientes / Aprobar - Rechazar **//
   public function process_operation($operacion, $accion){
      try{
         $audit = Audit::find($operacion);

         if ($accion == 'A'){
            switch ($audit->action) {
               //ACCIONES DEL MÓDULO CLIENTES
               case 'Activación Permanente':
                  $info = explode("*", $audit->additional_info);
                  DB::table('webp_users')
                        ->where('ID', '=', $audit->client)
                        ->update(['activado_permanente' => 1,
                                  'observacion_activacion' => $info[0],
                                  'autorizacion_activacion' => $info[1],
                                  'extendido' => 0,
                                  'observacion_caducado' => NULL,
                                  'autorizacion_caducado' => NULL,
                                  'inactivado' => 0,
                                  'observacion_inactivacion' => NULL,
                                  'autorizacion_inactivacion' => NULL,
                                  'fecha_inactivacion' => NULL,
                                  'status' => 1]);

                  $audit->status = 1;
                  $audit->admin2 = Auth::user()->ID;
               break;

               case 'Desactivación':
                  DB::table('webp_users')
                        ->where('ID', '=', $audit->client)
                        ->update(['activado_permanente' => 0,
                                  'observacion_activacion' => NULL,
                                  'autorizacion_activacion' => NULL,
                                  'extendido' => 0,
                                  'observacion_caducado' => NULL,
                                  'autorizacion_caducado' => NULL,
                                  'inactivado' => 0,
                                  'observacion_inactivacion' => NULL,
                                  'autorizacion_inactivacion' => NULL,
                                  'fecha_inactivacion' => NULL,
                                  'status' => 0]);

                  $audit->status = 1;
                  $audit->admin2 = Auth::user()->ID;
               break;

               case 'Compra de Paquete Manual':
                  $datosCompra = DB::table('pending_purchases')
                                    ->where('payment_id', '=', $audit->additional_info)
                                    ->first();

                  DB::table('webp_users')
                     ->where('ID', '=', $audit->client)
                     ->update(['status' => 1,
                               'paquete' => $datosCompra->membership_id,
                               'activado_permanente' => 0,
                               'observacion_activacion' => NULL,
                               'autorizacion_activacion' => NULL,
                               'observacion_caducado' => NULL,
                               'autorizacion_caducado' => NULL,
                               'extendido' => 0,
                               'inactivado' => 0,
                               'observacion_inactivacion' => NULL,
                               'autorizacion_inactivacion' => NULL,
                               'fecha_inactivacion' => NULL]);

                  $fecha = date("Y-m-d H:i:s");  

                  $pagoExistente = DB::table('membership_purchases')
                                      ->select('id')
                                      ->where('payment_id', '=', $audit->additional_info)
                                      ->first();

                  if ($pagoExistente == NULL){
                    DB::insert('insert into membership_purchases (user_id, membership_id, purchase_date, total_paid, payment_method, comments, authorization, payment_id, created_at, updated_at) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', [$audit->client, $datosCompra->membership_id, date("Y-m-d"), (double)$datosCompra->total_paid, $datosCompra->payment_method, $datosCompra->comments, $datosCompra->authorization, $audit->additional_info, $fecha, $fecha]);

                    $pago_comisiones = new PaymentController;
                    $pago_comisiones->approve_payment($audit->additional_info, $datosCompra->payment_method);
                  }
                 

                  $audit->status = 1;
                  $audit->admin2 = Auth::user()->ID;
               break;

               case 'Extensión de Paquete':
                  $fecha = date("Y-m-d H:i:s"); 

                  $datosCompra = DB::table('pending_purchases')
                                    ->where('payment_id', '=', $audit->additional_info)
                                    ->first();

                  DB::insert('insert into membership_purchases (user_id, membership_id, purchase_date, total_paid, payment_method, additional_info, comments, authorization, payment_id, created_at, updated_at) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', [$audit->client, $datosCompra->membership_id, date("Y-m-d"), 0, 'Extensión', $datosCompra->additional_info, $datosCompra->comments, $datosCompra->authorization, $audit->additional_info, $fecha, $fecha]);

                  DB::table('webp_users')
                     ->where('ID', '=', $audit->client)
                     ->update(['status' => 1,
                               'extendido' => 1,
                               'paquete' => $datosCompra->membership_id,
                               'activado_permanente' => 0,
                               'observacion_activacion' => NULL,
                               'autorizacion_activacion' => NULL,
                               'observacion_caducado' => NULL,
                               'autorizacion_caducado' => NULL,
                               'inactivado' => 0,
                               'observacion_inactivacion' => NULL,
                               'autorizacion_inactivacion' => NULL,
                               'fecha_inactivacion' => NULL]);

                  $audit->status = 1;
                  $audit->admin2 = Auth::user()->ID;
               break;
               
               case 'Inactivar Manualmente':
                  $info = explode("*", $audit->additional_info);
                  DB::table('webp_users')
                     ->where('ID', '=', $audit->client)
                     ->update(['status' => 0,
                               'extendido' => 0,
                               'activado_permanente' => 0,
                               'observacion_activacion' => NULL,
                               'autorizacion_activacion' => NULL,
                               'observacion_caducado' => NULL,
                               'autorizacion_caducado' => NULL,
                               'inactivado' => 1,
                               'observacion_inactivacion' => $info[0],
                               'autorizacion_inactivacion' => $info[1],
                               'fecha_inactivacion' => date('Y-m-d')]);

                  $audit->status = 1;
                  $audit->admin2 = Auth::user()->ID;
               break;

               case 'Caducar Manualmente':
                  $info = explode("*", $audit->additional_info);
                  DB::table('webp_users')
                     ->where('ID', '=', $audit->client)
                     ->update(['status' => 2,
                               'extendido' => 0,
                               'activado_permanente' => 0,
                               'observacion_activacion' => NULL,
                               'autorizacion_activacion' => NULL,
                               'inactivado' => 0,
                               'observacion_inactivacion' => NULL,
                               'autorizacion_inactivacion' => NULL,
                               'fecha_inactivacion' => NULL,
                               'observacion_caducado' => $info[0],
                               'autorizacion_caducado' => $info[1]]);

                  $audit->status = 1;
                  $audit->admin2 = Auth::user()->ID;
               break;

               case 'Editar':
                  switch ($audit->field) {
                     case 'Correo Electrónico':
                        DB::table('webp_users')
                           ->where('ID', '=', $audit->client)
                           ->update(['user_email' => $audit->new_value]);

                        $audit->status = 1;
                        $audit->admin2 = Auth::user()->ID;
                     break;

                     case 'Patrocinador':
                        DB::table('webp_users')
                           ->where('ID', '=', $audit->client)
                           ->update(['referred_id' => $audit->new_value]);

                        $audit->status = 1;
                        $audit->admin2 = Auth::user()->ID;

                        $script = new ScriptController;
                        $script->limpiar_sponsors();
                        $script->reorganizar_matriz();
                        $script->reestructurar_binario();
                        $script->verificar_usuarios_sin_referidos();
                     break;

                     case 'Alias':
                        DB::table('webp_users')
                           ->where('ID', '=', $audit->client)
                           ->update(['user_login' => $audit->new_value]);

                        $audit->status = 1;
                        $audit->admin2 = Auth::user()->ID;
                     break;

                     case 'Lado Binario':
                        DB::table('webp_users')
                           ->where('ID', '=', $audit->client)
                           ->update(['binary_side' => $audit->new_value]);

                        $audit->status = 1;
                        $audit->admin2 = Auth::user()->ID;

                        $script = new ScriptController;
                        $script->reestructurar_binario();
                     break;
                  }
               break;

               //ACCIONES DEL MÓDULO CUPONES
               case 'Nuevo Cupón':
                  $datos = explode(":", $audit->new_value);
                  $nombre = explode(". Descuento", $datos[1]);
                  $descuento = explode(". Límite", $datos[2]);
                  $limite = explode(". Paquetes", $datos[3]);
                  $paquetes = explode(". Validez", $datos[4]);
                  $validez = explode(". Compra", $datos[5]);
                  $compra = $datos[6];
                  
                  $cupon = new Coupon();
                  $cupon->name = $nombre[0];
                  $cupon->comments = $audit->additional_info;
                  $cupon->discount = $descuento[0];
                  $cupon->limit = $limite[0];
                  $cupon->code = str_random(20);
                  $cupon->applications = 0;
                  $cupon->validity_time = $validez[0];
                  $cupon->status = 1;
                  $cupon->membership_availables = $paquetes[0];
                  $cupon->purchase_type = $compra;
                  $cupon->save();

                  $audit->status = 1;
                  $audit->admin2 = Auth::user()->ID;
               break;

               //ACCIONES DEL MÓDULO COMPRESIÓN DINÁMICA
               case 'Restaurar Cliente (M1)':
                  $accionUsuario = new UserController();
                  $accionUsuario->create_restored_user($audit->client, $audit->additional_info);

                  $audit->status = 1;
                  $audit->admin2 = Auth::user()->ID;
               break;

               case 'Restaurar Cliente (M2)':
                  DB::table('webp_users')
                     ->where('ID', '=', $audit->client)
                     ->update(['restaurado' => 1,
                               'fecha_restauracion' => date('Y-m-d'),
                               'updated_at' => date('Y-m-d H:i:s')]);

                  $audit->status = 1;
                  $audit->admin2 = Auth::user()->ID;
               break;

               case 'Cambiar Estatus Paquete':
                  if ($audit->new_value == 'Oculto'){
                     DB::table('membership')
                        ->where('id', '=', $audit->additional_info)
                        ->update(['visible' => 0]);
                  }else{
                     DB::table('membership')
                     ->where('id', '=', $audit->additional_info)
                     ->update(['visible' => 1]);
                  }
                  

                  $audit->status = 1;
                  $audit->admin2 = Auth::user()->ID;
               break;

               //ACCIONES DEL MÓDULO ADMINISTRADOR DE COMISIONES
               case 'Cambiar Comisión Matriz':
                   DB::table('membership')
                        ->where('id', '=', $audit->additional_info)
                        ->update(['bono_matriz' => $audit->new_value]);

                  $audit->status = 1;
                  $audit->admin2 = Auth::user()->ID;
               break;

               case 'Cambiar Comisión Binario':
                   DB::table('membership')
                        ->where('id', '=', $audit->additional_info)
                        ->update(['bono_binario' => $audit->new_value]);

                  $audit->status = 1;
                  $audit->admin2 = Auth::user()->ID;
               break;
            }

            $audit->save();
            return redirect('admin/audit/pending-changes')->with('msj', 'La operación ha sido aprobada con éxito');
         }else{
            $audit->status = 2;
            $audit->admin2 = Auth::user()->ID;
            $audit->save();

            return redirect('admin/audit/pending-changes')->with('msj', 'La operación ha sido rechazada con éxito');
         }  
      }catch (\Exception $e) {
         $error = explode('Stack trace', $e);
         \Log::error("Admin / Auditoría / Operaciones Pendientes / Aprobar - Rechazar -> ".$error[0]);
      }catch (\Throwable $ex) {
         $error = explode('Stack trace', $ex);
         \Log::error("Admin / Auditoría / Operaciones Pendientes / Aprobar - Rechazar -> ".$error[0]);
      }

      return view('errors.error');
   }

    //Función para guardar los registros de auditorías
    public function save_audit($cliente, $modulo, $accion, $campo, $valor_viejo, $valor_nuevo, $status, $info_adicional = null){
      try{
        $audit = new Audit();
        $audit->admin = Auth::user()->ID;
        $audit->client = $cliente;
        $audit->module = $modulo;
        $audit->action = $accion;
        $audit->field = $campo;
        $audit->previous_value = $valor_viejo;
        $audit->new_value = $valor_nuevo;
        $audit->date = date('Y-m-d');
        $audit->status = $status;
        $audit->additional_info = $info_adicional;
        $audit->save();
      }catch (\Exception $e) {
         $error = explode('Stack trace', $e);
         \Log::error("Guardar en la BD una Auditoría -> ".$error[0]);
      }catch (\Throwable $ex) {
         $error = explode('Stack trace', $ex);
         \Log::error("Guardar en la BD una Auditoría -> ".$error[0]);
      }

      return view('errors.error');
   }

   //*** Admin / Compresión Dinámica / Historial ***//
   public function dynamic_compression_record(Request $request){
      try{
         view()->share('do', collect(['name' => 'Historial de Compresiones', 'text' => 'Historial de Compresiones']));

            if ( ($request->get('status') == '2') || ($request->get('status') == '3') ){
               $usuarios = User::status($request->get('status'))
                             ->orderBy('updated_at', 'DESC')
                             ->take(2000)
                             ->get();
            }else{
               $usuarios = User::where('status', '=', 2)
                              ->orWhere('status', '=', 3)
                              ->orderBy('updated_at', 'DESC')
                              ->take(2000)
                              ->get();
            }

           return view('admin.compression.record')->with(compact('usuarios'));
      }catch (\Exception $e) {
         $error = explode('Stack trace', $e);
         \Log::error("Admin / Compresión Dinámica / Historial -> ".$error[0]);
      }catch (\Throwable $ex) {
         $error = explode('Stack trace', $ex);
         \Log::error("Admin / Compresión Dinámica / Historial -> ".$error[0]);
      }

      return view('errors.error');
   }
}
