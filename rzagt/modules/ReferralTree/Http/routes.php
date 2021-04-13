<?php

use Illuminate\Support\Facades\Route;



Route::group([
	'middleware' => ['web', 'auth'], 
	'prefix' => 'referraltree', 
	'namespace' => 'Modules\ReferralTree\Http\Controllers'], function() {
		//
		Route::get('/{type}', 'ReferralTreeController@index')->name('referraltree');
    	Route::get('{type}/{id}', 'ReferralTreeController@moretree')->name('moretree');
});
