<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


Route::get('/data-comisiones', 'AuditController@dataComisiones')->name('audit.data.comisiones');
Route::get('/eliminar-comision', 'AuditController@eliminarComision')->name('audit.eliminar.comisiones');
Route::get('/getBinaryPoints/{id}', 'TreeController@getBinaryPoints')->name('audit.get.puntos');