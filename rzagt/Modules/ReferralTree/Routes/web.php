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

// Route::prefix('referraltree')->group(function() {
//     Route::get('/', 'ReferralTreeController@index');
// });


use Illuminate\Support\Facades\Route;



Route::group([
	'middleware' => ['web', 'auth'], 
	'prefix' => 'referraltree'], function() {
		//
		Route::get('/{type}', 'ReferralTreeController@index')->name('referraltree');
    	Route::get('{type}/{id}', 'ReferralTreeController@moretree')->name('moretree');
});
