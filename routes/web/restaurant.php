<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RestaurantGrocery\AuthController;
use App\Http\Controllers\RestaurantGrocery\DashboardController;
use App\Http\Controllers\RestaurantGrocery\Restaurant\ProductController;




Route::group(['PublicRoutes', 'subdomain' => 'restaurant'],function () {
  Route::get('/', function () {
			return redirect()->route('restaurant.login');
	});

	Route::match(['get', 'post'], '/login', [AuthController::class, 'login'])->name('restaurant.login');
	Route::match(['get', 'post'], '/signup', [AuthController::class, 'signup'])->name('restaurant.signup');
  Route::get('/index', function () {return view('restaurant_grocery.index');})->name('restaurant.index');
});

Route::group(['PrivateRoutes', 'middleware' => ['auth', 'restaurant', 'setlocale'], 'subdomain' => 'restaurant'], function() {

  //AuthController
  Route::group([],function () {
      Route::get('/logout', [AuthController::class, 'logout'])->name('restaurant.logout');
  });

  //ProductController
  Route::group(['ProductController', 'prefix' => 'food'], function () {
    Route::get('/', [ProductController::class, 'index'])->name('restaurant.food.index');
    Route::match(['get', 'post'], '/upload-food', [ProductController::class, 'store'])->name('restaurant.uploadFood');
    Route::match(['get', 'post'], '/edit/{id}', [ProductController::class, 'update'])->name('restaurant.editFood');
    Route::get('/delete/{id}', [ProductController::class, 'destroy'])->name('restaurant.deleteFood');
  });

  //DashboardController
  Route::group(['DashboardController'], function () {
    Route::match(['get', 'post'],'/profile-settings', [DashboardController::class, 'profileSetting'])->name('restaurant.profileSetting');
    Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('restaurant.dashboard');
    Route::match(['get', 'post'],'/change-password', [DashboardController::class, 'changePassword'])->name('restaurant.changePassword');
  });

  // Route::get('/products', 'RestaurantGrocery\DashboardController@products')->name('restaurant.products');
  // Route::get('/product-delete/{id}', 'RestaurantGrocery\ProductController@deleteProduct')->name('restaurant.deleteProduct');

});


