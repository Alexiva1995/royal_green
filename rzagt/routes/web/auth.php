<?php
// Route::prefix('office')->group(function ()
// {
	
	Auth::routes();

// });
Route::get('login/{driver}', 
	'Auth\SocialAuthController@redirectToProvider')->name('social.oauth');

Route::get('login/{driver}/callback', 
	'Auth\SocialAuthController@handleProviderCallback')->name('social.callback');