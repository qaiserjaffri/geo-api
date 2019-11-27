<?php

use Illuminate\Http\Request;

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

Route::namespace('GeoLocation')->group(function(){
		
	Route::get('geolocation', "GeoLocationController@get");
});
Route::namespace('LocationWeather')->group(function(){

	Route::get('weather', "LocationWeatherController@get");
});


