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

Route::get('/', function () {
    return view('welcome');
});


Route::namespace('Admin')->group(function(){
    Route::namespace('User')->group(function(){
        Route::get('admin/users', "UserController@users");
		// Route::post('logout', 'UserController@logout');
	    // Route::put('update_user', 'UserController@update');
	    // Route::post('register', 'UserController@register'); 	    
	    // Route::get('single_user', 'UserController@single');
    });
});
Route::namespace('Admin')->group(function(){
    Route::namespace('Dashboard')->group(function(){
        Route::get('admin/dashboard', "DashboardController@index");
    });
});