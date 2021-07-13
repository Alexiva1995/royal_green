<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;


// configuracion inicial

Route::group(['prefix' => 'installer', 'middleware' => 'licencia'], function (){
  Route::get('/step1', 'InstallController@index')->name('install-step1');
  Route::post('/savestep1', 'InstallController@saveStep1')->name('install-save-step1');
  Route::get('/step2', 'InstallController@step2')->name('install-step2');
  Route::post('/savestep2', 'InstallController@saveStep2')->name('install-save-step2');
  Route::get('/end', 'InstallController@end')->name('install-end');

});

Route::get('/clear-cache', function() {
  $exitCode = Artisan::call('config:clear');
  $exitCode = Artisan::call('cache:clear');
  $exitCode = Artisan::call('config:cache');
  $exitCode = Artisan::call('view:clear');
  $exitCode = Artisan::call('route:clear');
  // Mail::send('correo.subcripcion', ['data' => []], function ($correo2)
  //     {
  //         $correo2->subject('Limpio el sistema');
  //         $correo2->to('cgonzalez.byob@gmail.com');
  //     });
  return 'DONE'; //Return anything
});


Route::get('vistaCorreo', function ()
{
  return view('emails.plantilla');
});

// Route::prefix('office')->group(function ()
// {
  
Route::group(['prefix' => 'autentication'], function (){
  Route::get('/register', 'Auth\RegisterController@newRegister')->name('autenticacion.new-register');
  Route::post('/saveregister', 'Auth\RegisterController@creater')->name('autenticacion.save-register');
  // ruta para el segundo factor
  Route::get('2fact', 'Auth\RegisterController@fact2')->name('autenticacion.2fact');
  Route::post('2fact', 'Auth\RegisterController@validar2fact')->name('autenticacion.2fact');
    // Registro de las licencias
  Route::post('/savelicencia', 'HomeController@saveLicencia')->name('autenticacion-save-licencia');
   // pare enviar el correo
  Route::post('/recuperarclave', 'RecuperarController@Correo')->name('autenticacion.clave');
  // para recibir el codigo enviado y ir a la pagina de cambiar correo
  Route::get('/getcodigo/{id}', 'RecuperarController@getCodigo')->name('autenticacion-codigo');
  // para guardar el nuevo correo
  Route::post('/guardarclave', 'RecuperarController@change')->name('autenticacion-new-clave');
  Route::post('/loginnew', 'RecuperarController@nuevoLogin')->name('autenticacion-login');
  Route::get('{token}/validarcorreo', 'RecuperarController@validarCorreo')->name('autenticacion-validar-correo');
});



// Tienda Online

Route::group(['prefix' => 'tienda', 'middleware' => ['auth', 'licencia', 'guest']], function (){

    Route::get('/', 'TiendaController@index')->name('tienda-index');

    Route::post('savecompra', 'TiendaController@saveOrdenPosts')->name('tienda-save-compra');

    Route::post('savecupon', 'TiendaController@saveCupon')->name('tienda-save-cupon');

    Route::post('verificar_cupon', 'TiendaController@validacionCupon')->name('tienda-verificar-cupon');

    Route::get('/solicitudes', 'TiendaController@solicitudes')->name('tienda-solicitudes')->middleware('admin');

    Route::post('/solicitudes-paquete', 'TiendaController@agregarPaquetes')->name('tienda-activar-paquete')->middleware('admin');

    Route::get('/getdateuser', 'TiendaController@getUserActivar')->name('tienda.getdatauser')->middleware('admin');

    Route::post('/accionsolicitud', 'TiendaController@accionSolicitud')->name('tienda-accion-solicitud')->middleware('admin');

    Route::get('product', 'ProductController@index')->name('listProduct')->middleware('admin');

    Route::post('saveproduct', 'ProductController@saveProduct')->name('save.product')->middleware('admin');

    Route::post('editproduct', 'ProductController@editProduct')->name('edit.product')->middleware('admin');

    Route::get('{id}/delete', 'ProductController@deleteProduct')->name('save.delete')->middleware('admin');

    Route::get('{estado}/state', 'TiendaController@estadoTransacion')->name('tienda.estado')->middleware('admin');

});



Route::group(['prefix' => 'admin', 'middleware' => ['auth', 'licencia', 'guest']], function() {

  Route::post('changeside', 'HomeController@changeSide')->name('change.side');

    // Actualiza todos la informacion para los usuarios

    Route::get('updateall', 'AdminController@ActualizarTodo')->name('admin-update-all');

    // Auditorias
    Route::resource('auditoria', 'AuditoriaController');

    // subdashboard
    Route::get('subdashboard', 'AdminController@subdashboard')->name('admin.subdashboard')->middleware('admin');
    
    // Resetear QR
    Route::get('resetQr/{iduser}', 'HomeController@resetearQR')->name('admin.reset-qr')->middleware('admin');

    //Admin Route
    Route::get('listRango', 'RangoController@indexRangos')->name('admin.list-rango')->middleware('admin');

    // Billetera

    Route::group(['prefix' => 'wallet'], function(){
        Route::get('/', 'WalletController@index')->name('wallet');
        Route::post('/detail_user', 'WalletController@indexUser')->name('wallet-user');
        // Route::get('/tantechcoins', 'WalletController@indexTantech')->name('wallet-index');
        // Route::get('/tantechcoinspersonal', 'WalletController@indexTantechPersonal')->name('wallet-index');
        // Route::get('/puntos', 'WalletController@indexPuntos')->name('wallet-index');
        Route::post('/transferencia', 'WalletController@transferencia')->name('wallet-transferencia');
        Route::get('/obtenermetodo/{id}', 'WalletController@datosMetodo')->name('wallet-metodo');
        Route::post('/retiro', 'WalletController@retiro')->name('wallet-retiro');
        Route::get('/historial', 'WalletController@historial')->name('wallet-historial');
        Route::post('/historialfechas', 'WalletController@historial_fechas')->name('wallet-historial-fechas');
        Route::get('/cobros', 'WalletController@cobros')->name('wallet-cobros');
        Route::post('/cobrosfechas', 'WalletController@cobros_fechas')->name('wallet-cobros-fechas');
        Route::post('/pay_rentabilidad', 'ComisionesController@process_rentabilidad')->name('wallet.pay.rentabilidad');
        Route::get('/hitorialpointbinario', 'WalletController@historialbinario')->name('wallet.binario');
        Route::post('/hitorialpointbinario', 'WalletController@historialbinario')->name('wallet.binario');
        Route::post('/validarretiro', 'WalletController@VerificarRetiro')->name('waller.confimacion');
        Route::get('/anularretiro', 'WalletController@anularRetiro')->name('wallet.anular');
        Route::post('/adminretiro', 'WalletController@admin_retiro')->name('wallet.admin.retiro')->middleware('admin');
    });

    // Rentabilidad
    Route::group(['prefix' => 'rentabilidad'], function ()
    {
        Route::get('/', 'RentabilidadController@index')->name('rentabilidad.index');
        Route::post('retirar', 'RentabilidadController@retiro')->name('rentabilidad.retirar');
    });

    // Remover Billetera
    Route::group(['prefix' => 'removerbilletera'], function ()
    {
        Route::get('/', 'ComisionesController@indexremover')->name('removerbilletera.index');
        Route::post('remover', 'ComisionesController@remover')->name('removerbilletera.remover');
    });

    // Edit Billetera
    Route::group(['prefix' => 'editWallet'], function ()
    {
        Route::get('/', 'ComisionesController@indexeditWallet')->name('editWallet.index');
        Route::post('remover', 'ComisionesController@editWallet')->name('editWallet.edit');
    });

    //activar o desactivar algunas funciones
    Route::group(['prefix' => 'disables'], function ()
    {
        // Activar/Desactivar 2fact
        Route::get('2fact/{iduser}/update/', 'HomeController@disable2fact')->name('disable_2fact.update');
        // Activar/Desactivar la rentabilidad
        Route::get('rentabilidad/{iduser}/update/', 'HomeController@disableRentabilidad')->name('disable_renta.update');
        // Activar/Desactivar el retiro
        Route::get('retiro/{iduser}/update/{admin?}', 'HomeController@disableRetiro')->name('disable_retiro.update');
        // Activar/Desactivar el Pago de Comisiones
        Route::get('pay_comisiones/{iduser}/update', 'HomeController@activarPagoRetiro')->name('active_pay_comisiones.update');
    });

    // Pago
    Route::group(['prefix' => 'price', 'middleware' => 'admin'], function(){
        Route::get('/historial', 'PagoController@historyPrice')->name('price-historial');
        Route::get('/confirmar/{tipo}', 'PagoController@confimPrice')->name('price-confirmar');
        Route::get('/aceptarpago/{id}', 'PagoController@aprobarPago')->name('price-aprobar');
        Route::get('/rechazarpago/{id}', 'PagoController@rechazarPago')->name('price-rechazar');
        Route::post('/filtro', 'PagoController@filtro')->name('price-filtro');
    });



    // graficas

    Route::group(['prefix' => 'chart'], function(){

        Route::get('ventas', 'IndexController@chartVentas')->name('chart.ventas');

        Route::get('pagos', 'IndexController@charPagos')->name('chart.pagos');

        // Route::get('rangos', 'IndexController@chartRangos')->name('chart.rangos');

        Route::get('usuarios', 'IndexController@chartUsuarios')->name('chart.usuarios');

    });

    // Configuraciones

    Route::group(['prefix' => 'settings', 'middleware' => 'admin'], function ()

    {

        // Permite Reiniciar el sistema
        Route::get('/reinicio', 'SettingController@resetSystem')->name('settings.reset');
        // seccion logo, favico y nombre sistema

        Route::get('/sistema', 'SettingController@indexLogo')->name('setting-logo');

        Route::post('/savelogo', 'SettingController@saveLogo')->name('setting-save-logo');

        Route::post('/savefavicon', 'SettingController@saveFavicon')->name('setting-save-favicon');

        Route::post('/savename', 'SettingController@updateName')->name('setting-save-name');

        Route::get('/chageporcent', 'HomeController@changePorcent')->name('setting-change-porcent');

        Route::post('/saveporcentniveles', 'SettingController@updateValorNiveles')->name('setting-save-porcent');
        // valor de tantech coins
        Route::post('/valuetantevh', 'SettingController@updateTantech')->name('setting-save-tantech');
        // valor de rentabilidad
        Route::post('/valuerentabilidad', 'SettingController@updateRentabilidad')->name('setting-save-rentabilidad');

        // seccion campos formularios

        Route::get('/formulario', 'SettingController@indexFormulario')->name('setting-formulario');

        Route::post('saveform', 'SettingController@saveForm')->name('setting-save-form');

        Route::get('/updatefield/{id}/{estado}', 'SettingController@statusField')->name('setting-update-field');

        Route::get('/getform/{id}', 'SettingController@getForm')->name('setting-get-form');

        Route::post('/updateform', 'SettingController@updateForm')->name('setting-update-form'); 

        Route::get('/deleteform/{id}', 'SettingController@deleteForm')->name('setting-delete-form');

        Route::post('/terminos', 'SettingController@terminos')->name('setting-terminos');

        // seccion de comisiones

        Route::get('/comisiones', 'SettingController@indexComisiones')->name('setting-comisiones');

        Route::post('/savecomision', 'SettingController@saveSettingComision')->name('setting-save-comision');

        Route::post('/savebono', 'SettingController@saveBono')->name('setting-save-bono');

        Route::post('/saveprimeracompra', 'SettingController@savePrimera_compra')->name('setting-save-primara-compra');

        Route::post('/saveproducto', 'SettingController@saveProducto')->name('setting-save-producto');

        Route::post('/deleteproducto', 'SettingController@deleteProducto')->name('setting-delete-producto');

        Route::get('/getrangosall', 'SettingController@allRangos')->name('settings-get-all-rangos');

        Route::get('/getproductosall', 'SettingController@allProductos')->name('settings-get-all-productos');

        // seccion de estructura (Arbol - Matrix)

        Route::get('/estructura', 'SettingController@indexEstructura')->name('setting-estructura');

        Route::post('saveestrutura', 'SettingController@saveEstructura')->name('setting-save-estructura');

        // seccion de Rango

        Route::get('/rangos', 'SettingController@indexRango')->name('setting-rango');

        Route::post('/saverango', 'SettingController@saveRangos')->name('setting-save-rango');

        // seccion de pago

        Route::get('/pagos', 'SettingController@indexPago')->name('setting-pago');

        Route::post('/savepagos', 'SettingController@savePagos')->name('setting-save-pagos');

        Route::get('/updatepago/{id}/{estado}', 'SettingController@statusPago')->name('setting-update-pagos');

        Route::post('/savecomisionpago', 'SettingController@comisionMetodoPago')->name('setting-comision-pago');

        Route::get('/getmetodo/{id}', 'SettingController@getMetodo')->name('setting-get-metodo');

        Route::post('/updatemetodo', 'SettingController@updateMetodo')->name('setting-update-metodo'); 

        Route::get('/deletemetodo/{id}', 'SettingController@deleteMetodo')->name('setting-delete-metodo');

        // seccion de plantilla de correo

        Route::get('/plantilla', 'SettingController@indexPlantilla')->name('setting-plantilla');

        Route::post('/saveplantilla', 'SettingController@savePlantilla')->name('setting-save-plantilla');

        Route::post('/probaplantilla', 'SettingController@probarPlantilla')->name('setting-probar-plantilla');

        // seccion permisos

        Route::get('/permisos', 'SettingController@indexPermisos')->name('setting-permisos');

        Route::post('/adminsave', 'SettingController@saveAdmin')->name('setting-save-admin');

        Route::get('/getpermisos/{id}', 'SettingController@getPermisos')->name('setting-get-permisos');

        Route::post('/savepermiso', 'SettingController@savePermisos')->name('setting-save-permisos');

        // seccion activacion

        Route::get('/activacion', 'SettingController@indexActivacion')->name('setting-activacion');

        Route::post('/saveactivacion', 'SettingController@saveActivacion')->name('setting-save-activacion');

        // seccion de monedas

        Route::get('/monedas', 'SettingController@indexMonedas')->name('setting-monedas');

        Route::post('/savemonedas', 'SettingController@saveMonedas')->name('setting-save-monedas');

        Route::get('/updatemoneda/{id}/{estado}', 'SettingController@statusMoneda')->name('setting-update-moneda-principal');

        Route::get('/deletemoneda/{id}', 'SettingController@deleteMoneda')->name('setting-delete-moneda');

    });



    //Generar las comisiones mensuales de todos los usuarios (OpcSidebar: Comisiones / Generar Comisiones)

    Route::get('/generatecommisions', 'ComisionesController@ObtenerUsuarios')->name('admin.generate-commissions')->middleware('admin');

    //Historial de comisiones de todos los usuarios (OpcSidebar: Reportes / Historial de Comisiones)

    Route::get('/commissionrecords', 'AdministradorController@commission_records')->name('admin.commission-records')->middleware('admin');



    //   Rutas pasa las liquidaciones

    Route::get('/liquidaciones', 'LiquidacionesController@index')->name('admin.liquidaciones')->middleware('admin');

    Route::get('/generarliquidaciones', 'LiquidacionesController@procesarLiquidacion')->name('admin.generarliquidaciones')->middleware('admin');

    Route::post('/liquidacion_estatus', 'LiquidacionesController@estado')->name('admin.liquidacion_estatus')->middleware('admin');

    Route::get('/liquidar_todo', 'LiquidacionesController@liquidar_todo')->name('admin.liquidar_todo')->middleware('admin');



    // Transferir comisiones pendientes

    Route::get('/pagocomision/{id}', 'CommissionController@pago_comisiones')->name('admin.pagocomision')->middleware('admin');

    // Historia del Liquidaciones

    Route::get('/recordliquidacion', 'LiquidacionesController@recordliquidacion')->name('admin.liquidacion-record')->middleware('admin');



    //comisiones por fechas

     Route::get('/comisiones_filter', 'ComisionesController@comisiones_filter')->name('admin.comisiones_filter')->middleware('admin');

     Route::post('/filter_comisiones', 'ComisionesController@filter_comisiones')->name('admin.filter_comisiones')->middleware('admin');



    Route::get('/', 'AdminController@index')->name('admin.index');

    Route::get('/ranking', 'Ranking2Controller@ranking')->name('admin.ranking');



    //Transfiere lo que hay en las billeteras al banco

    Route::get('/paycommissions', 'CommissionController@pay_commissions')->name('admin.pay-commissions')->middleware('admin');

    Route::get('/recordtransfers', 'CommissionController@record_transfers')->name('admin.record-transfers')->middleware('admin');



    // Modificacion del usuario por parte del admin
    Route::get('/userrecords/{email?}', 'HomeController@user_records')->name('admin.userrecords')->middleware('admin');
    Route::get('/useredit/{id}', 'ActualizarController@user_edit')->name('admin.useredit')->middleware('admin');
    Route::get('/userinactive', 'HomeController@userActiveManual')->name('admin.userinactive')->middleware('admin');
    Route::post('/userinactive', 'HomeController@saveActiveManual')->name('admin.userinactive')->middleware('admin');
    Route::post('/userdeletetodos/{id}', 'AdminController@deleteTodos')->name('admin.userdeletetodos')->middleware('admin');
    Route::get('/notifications', 'NotificationController@index')->name('admin.notifications')->middleware('admin');
    //Search users por vision de usuario
    Route::get('/buscar','AdminController@buscar')->name('admin.buscar')->middleware('admin');
    Route::get('/vista','AdminController@vista')->name('admin.vista')->middleware('admin');
    //fin de vision de usuario
    //Todo tipo de informes

     Route::group(['prefix' => 'info', 'middleware' => 'admin'], function(){

      Route::get('reportDirect', 'AdminController@indexReportDirectDate')->name('report_direct');
      Route::post('reportDirect', 'AdminController@reportDirectDate')->name('report_direct');

      // info rango
      Route::get('rangouser', 'RangoController@listRangos')->name('info.list-rango');
      route::get('{iduser}/{idrango}/{estado}/actualizarpremio', 'RangoController@cambiarEstadoDelosrangos')->name('info.rango-actualizar');

         //informes de perfil buscar por nombre

        Route::get('/perfil', 'ReporteController@perfil')->name('info.perfil');

        Route::post('/nombre','ReporteController@nombre')->name('info.nombre');

      

      //buscar por ID de usuario

        Route::post('/usuario','ReporteController@usuario')->name('info.usuario');

        Route::get('/mostrar-usuario','ReporteController@mostrarusuario')->name('info.mostrar-usuario');

        

        //desde un ID hasta ID

         Route::post('/lista','ReporteController@lista')->name('info.lista');

        Route::get('/lista-final','ReporteController@listafinal')->name('info.lista-final');

        

        //informes de activos

         Route::get('/activacion','ReporteController@activacion')->name('info.activacion');

         Route::post('/mostrar-activo','ReporteController@mostraractivo')->name('info.mostrar-activo');

         Route::post('/fecha','ReporteController@fecha')->name('info.fecha');

         

         //Rangos

         Route::get('/rango','ReporteController@rango')->name('info.rango');

         Route::post('/mostrar-rango','ReporteController@mostrarrango')->name('info.mostrar-rango');

         

         //comisiones

         Route::get('/comisiones','ReporteController@comisiones')->name('info.comisiones');

         Route::post('/mostrar-comisiones','ReporteController@mostrarcomisiones')->name('info.mostrar-comisiones');
        
        // Comisiones con compras
        Route::get('{balance}/{tipo}/comisioncompra', 'ComisionesController@reporteCompraComision')->name('info.comisioncompra');
        Route::post('/comisioncomprafecha', 'ComisionesController@reporteCompraComisionxFecha')->name('info.comisioncompra.fechas');

        // Sumatoria de la Billeteras
         Route::get('billeteras', 'ReporteController@billeteras')->name('info.billeteras');

         //pagos

          Route::get('/pagos','ReporteController@pagos')->name('info.pagos');

         Route::get('/pagosusuario','ReporteController@pagosusuario')->name('info.pagosusuario');

         Route::post('/buscar','ReporteController@buscar')->name('info.buscar');

         

         //reportes pagos

          Route::get('/reportes','ReporteController@reportes')->name('info.reportes');

           Route::post('/repor-fecha','ReporteController@reporfecha')->name('info.repor-fecha');

            Route::post('/todos','ReporteController@todos')->name('info.todos');

          Route::post('/nombre-bus','ReporteController@nombrebus')->name('info.nombre-bus');

          

          //reportes de comision

           Route::get('/repor-comi','ReporteController@reporcomi')->name('info.repor-comi');

          Route::post('/repor-todos','ReporteController@reportodos')->name('info.repor-todos');

          

          //reporte de ventas

          Route::get('/ventas','ReporteController@ventas')->name('info.ventas');

          Route::post('/informe_fecha','ReporteController@informe_fecha')->name('info.informe_fecha');

          Route::post('/informe_ventas','ReporteController@informe_ventas')->name('info.informe_ventas');

          

          //liquidacion

           Route::get('/liquidacion','ReporteController@liquidacion')->name('info.liquidacion');



          // descuento

          Route::get('/feed', 'ReporteController@descuentos')->name('info.descuento');

         

    });

    

    //gestion de perfiles

     Route::group(['prefix' => 'gestion', 'middleware' => 'admin'], function(){

         //perfil

         Route::get('/verusuario/{id}', 'GestionController@verusuario')->name('gestion.verusuario');

         Route::get('/gestionperfiles', 'GestionController@gestionperfiles')->name('gestion.gestionperfiles');

          Route::post('/gestion','GestionController@gestion')->name('gestion.gestion');

          Route::get('/encontrado','GestionController@encontrado')->name('gestion.encontrado');

          

          //ingresos liberados

          Route::get('/ingresos/{id}','GestionController@ingresos')->name('gestion.ingresos');

          Route::get('/ingresos-valor','GestionController@ingresos_valor')->name('gestion.ingresos-valor');

          

          //ingresos detallados

          Route::get('/ingresos-detallado','GestionController@ingresos_detallado')->name('gestion.ingresos-detallado');

          

          //referidos

          Route::get('/referidos/{id}','GestionController@referidos')->name('gestion.referidos');

          Route::get('/directos','GestionController@directos')->name('gestion.directos');

          

          //billetera

          Route::get('/wallet/{id}','GestionController@wallet')->name('gestion.wallet');

          Route::get('/billetera','GestionController@billetera')->name('gestion.billetera');

          

          //pagos

          Route::get('/pago/{id}','GestionController@pago')->name('gestion.pago');

          Route::get('/liberado','GestionController@liberado')->name('gestion.liberado');

          

     });

    Route::group(['prefix' => 'user'], function(){
        Route::get('/edit', 'ActualizarController@editProfile')->name('admin.user.edit');
        Route::put('update', 'ActualizarController@updateProfile')->name('admin.user.update');       
        Route::get('setCode/{iduser}', 'ActualizarController@generarCode')->name('admin.generar.code');
    });

    

    //Historial de actividades

     Route::group(['prefix' => 'actividad'], function(){

          Route::get('/actividad', 'ActividadController@actividad')->name('actividad.actividad')->middleware('admin');

     });





    Route::group(['prefix' => 'network'], function(){

        Route::get('/directrecords', 'AdminController@direct_records')->name('directrecords');

        Route::get('/networkrecords', 'AdminController@network_records')->name('networkrecords');

         Route::post('/buscardirectos','AdminController@buscardirectos')->name('buscardirectos')->middleware('admin');

          Route::post('/buscarnetwork','AdminController@buscarnetwork')->name('buscarnetwork')->middleware('admin');

          Route::post('/buscarnetworknivel','AdminController@buscarnetworknivel')->name('buscarnetworknivel')->middleware('admin');

           

        Route::get('/commissionsrecords', 'ComisionesController@ObtenerUsuarios')->name('commissionsrecords')->middleware('admin');

        Route::get('/commissionspayment', 'ComisionesController@ObtenerUsuarios')->name('commissionspayment')->middleware('admin');

         Route::get('/aprobarcomision/{id}', 'ComisionesController@aprobarComision')->name('comisiones.aprobar')->middleware('admin');



    });



    Route::group(['prefix' => 'transactions'], function(){

        Route::get('/personalorders', 'AdminController@personal_orders')->name('personalorders');

        Route::get('/networkorders', 'AdminController@network_orders')->name('networkorders');

        Route::post('/filtreorders', 'AdminController@network_orders_filtre')->name('networkorders_filtre')->middleware('admin');

         Route::post('/buscarpersonalorder','AdminController@buscarpersonalorder')->name('buscarpersonalorder')->middleware('admin');

          Route::post('/buscarnetworkorder','AdminController@buscarnetworkorder')->name('buscarnetworkorder')->middleware('admin');

    });

    

    Route::group(['prefix' => 'ticket'], function(){
       Route::get('/ticket','TicketController@ticket')->name('ticket');
       Route::post('/generarticket','TicketController@generarticket')->name('generarticket');
       Route::get('/misticket','TicketController@misticket')->name('misticket');
        Route::get('/{id}/comentar','TicketController@comentar')->name('comentar');
        Route::post('subir','TicketController@subir')->name('subir');
        Route::get('/todosticket','TicketController@todosticket')->name('todosticket');
        Route::get('/{id}/ver','TicketController@ver')->name('ver');
       Route::get('/{id}/cerrar','TicketController@cerrar')->name('cerrar')->middleware('admin');

    });





});


// });

Route::get('/', 'HomeController@index')->name('index');

Auth::routes();
