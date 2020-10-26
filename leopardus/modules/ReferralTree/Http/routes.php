<?php

Route::group([
	'middleware' => ['web', 'auth'], 
	'prefix' => 'mioficina/referraltree', 
	'namespace' => 'Modules\ReferralTree\Http\Controllers'], function() {
		//
		Route::get('/', 'ReferralTreeController@index')->name('referraltree');
    	Route::get('/{id}', 'ReferralTreeController@moretree')->name('moretree');
		Route::post('/moretree', 'ReferralTreeController@moretree2')->name('moretree2');
    	Route::get('/getReferreds', 'ReferralTreeController@getReferreds');
		Route::get('/tablero/{tablero}', 'ReferralTreeController@indexTablero')->name('tablero');
		Route::get('/tablero/{id}/{tablero}', 'ReferralTreeController@moreTablero')->name('moretablero');
});
