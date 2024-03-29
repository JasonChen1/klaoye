<?php

use Illuminate\Support\Facades\Route;
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

/*Route::get('/', function () {
    return view('welcome');
});*/

Route::get('/', 'ShopController@index');
// Route::get('/user', 'ShopController@index');
Route::get('/admin', 'AdminController@index');

Route::get('/admin/{any}','AdminController@index')->where('any', '.*');
// Route::get('/user/{any}','UserController@index')->where('any', '.*');
Route::get('/{any}','ShopController@index')->where('any', '.*');