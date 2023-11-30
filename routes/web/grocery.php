<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RestaurantGrocery\AuthController;
use App\Http\Controllers\RestaurantGrocery\DashboardController;
use App\Http\Controllers\RestaurantGrocery\Grocery\ProductController;


Route::group(['PublicRoutes', 'subdomain' => 'grocer'], function () {
    Route::get('/', function () {
        return redirect()->route('grocer.login');
	});

	Route::match(['get', 'post'], '/login', [AuthController::class, 'login'])->name('grocer.login');
	Route::match(['get', 'post'], '/signup', [AuthController::class, 'signup'])->name('grocer.signup');
    Route::get('/index', function () {return view('restaurant_grocery.index');})->name('grocer.index');
});



Route::group(['PrivateRoutes', 'middleware' => ['auth', 'grocer', 'setlocale'], 'subdomain' => 'grocer'], function () {

    //AuthController
    Route::group([], function () {
        Route::get('/logout', [AuthController::class, 'logout'])->name('grocer.logout');
    });

    //ProductController
    Route::group(['ProductController', 'prefix' => 'product'], function () {
        Route::get('/', [ProductController::class, 'index'])->name('grocer.product.index');
        Route::match(['get', 'post'], '/upload-product', [ProductController::class, 'store'])->name('grocer.uploadProduct');
        Route::match(['get', 'post'], '/edit/{id}', [ProductController::class, 'update'])->name('grocer.editProduct');
        Route::get('/delete/{id}', [ProductController::class, 'destroy'])->name('grocer.deleteProduct');
    });

    //DashboardController
    Route::group(['DashboardController'], function () {
        Route::match(['get', 'post'], '/profile-settings', [DashboardController::class, 'profileSetting'])->name('grocer.profileSetting');
        Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('grocer.dashboard');
        Route::match(['get', 'post'], '/change-password', [DashboardController::class, 'changePassword'])->name('grocer.changePassword');
    });

    // Route::get('/products', 'RestaurantGrocery\DashboardController@products')->name('restaurant.products');
    // Route::get('/product-delete/{id}', 'RestaurantGrocery\ProductController@deleteProduct')->name('restaurant.deleteProduct');

});
