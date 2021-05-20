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


// 用户
Route::prefix('user')->group(function(){
	Route::post('login', 'Auth\User\AuthController@login')->name('login');
	Route::post('register', 'Auth\User\AuthController@register');

	Route::post('password/email', 'Auth\User\AuthController@sendPasswordResetLink');
	Route::post('password/reset', 'Auth\User\ResetPasswordController@callResetPassword');

	Route::get('email/verify', 'Auth\User\VerificationController@show')->name('verification.notice');
	Route::get('email/verify/{id}', 'Auth\User\VerificationController@verify')->name('verification.verify');
	Route::get('email/resend', 'Auth\User\VerificationController@resend')->name('verification.resend');

	Route::middleware(['auth:api'])->group(function(){
		Route::resource('address','UserAddressController');
		Route::get('order/status/{status}','UserOrderController@orderStatus');
		Route::resource('order','UserOrderController',['as' => 'user']);
		Route::get('logout', 'Auth\User\AuthController@logout');
	});
});

// 管理员
Route::prefix('admin')->group(function(){
	// 登入
	Route::post('login','Auth\Admin\AuthController@login');
	// 发送管理员重置密码邮件
	Route::post('password/email', 'Auth\Admin\AuthController@sendPasswordResetLink');
	// 重置密码
	Route::post('password/reset', 'Auth\Admin\ResetPasswordController@callResetPassword');

	Route::middleware(['auth:adminApi'])->group(function(){
		// coupon
		Route::resource('coupon','CouponController');
		// 刷新token
		Route::get('refresh/token','Auth\Admin\AuthController@refreshToken');
		// 商品
		Route::post('products/{id}','ProductController@update');
		Route::resource('products','ProductController');
		Route::delete('product/image/{id}','ProductController@deleteImage');
		Route::post('product/set','ProductController@setActive');
		Route::post('product/colour','ProductController@addColour');
		Route::delete('product/colour/{id}','ProductController@deleteColour');
		// 地址
		Route::resource('address','AdminAddressController',['as'=>'admin']);
		// 订单
		Route::resource('order','AdminOrderController',['as' => 'admin']);
		// 登出
		Route::get('logout', 'Auth\Admin\AuthController@logout');
	});
});