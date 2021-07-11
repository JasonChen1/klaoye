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

Route::get('categories','ShopController@categories');
Route::get('search','ShopController@search');
Route::get('products/{id}','ShopController@details');
Route::get('products','ShopController@products');
Route::get('testimonials','ShopController@testimonials');
Route::post('checkout','ShopController@checkout');
Route::post('paypal/checkout','ShopController@paypalCheckout');
Route::post('stripe/callback','ShopController@stripeCallback');
Route::post('enquiry','ShopController@enquiry');


// Route::get('rename','ShopController@rename');

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
		// user cart
		Route::post('cart','UserController@addtoCart');
		Route::get('cart/count','UserController@cartCount');
		Route::get('cart/empty','UserController@emptyCart');
		Route::get('cart','UserController@cart');
		Route::delete('cart/{id}','UserController@deleteFromCart');
		Route::patch('cart/{id}','UserController@updateQuantity');
		// user detail
		Route::get('details','UserController@details');
		Route::patch('details','UserController@updateDetails');
		// user addreess
		Route::get('address','UserController@address');
		Route::patch('address','UserController@updateAddress');
		// payment
		Route::post('checkout','UserController@checkout');
		Route::post('paypal/checkout','UserController@paypalCheckout');
		Route::post('stripe/callback','UserController@stripeCallback');
		// 订单
		Route::get('order','UserController@getOrders',['as' => 'user']);
		// order details
		Route::get('order/details/{id}','OrderDetailController@show',['as' => 'user']);
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
		// 刷新token
		Route::get('refresh/token','Auth\Admin\AuthController@refreshToken');
		// Testimonials
		Route::resource('testimonials','TestimonialController');		
		Route::post('testimonials/{id}','TestimonialController@update');		
		// 商品
		Route::resource('products','ProductController');
		Route::post('products/{id}','ProductController@update');
		Route::delete('product/image/{id}','ProductController@deleteImage');
		Route::post('product/set','ProductController@setActive');
		Route::post('product/colour','ProductController@addColour');
		Route::delete('product/colour/{id}','ProductController@deleteColour');
		// categories
		Route::resource('categories','CategoryController');
		Route::get('categories/all','CategoryController@all');
		Route::delete('subcategories/{id}','CategoryController@destroySubCategory');
		// 订单
		Route::resource('order','OrderController',['as' => 'admin']);
		// order details
		Route::resource('order/details','OrderDetailController',['as' => 'admin']);
		// enquiries
		Route::resource('enquiry','EnquiryController');
		// 登出
		Route::get('logout', 'Auth\Admin\AuthController@logout');
	});
});