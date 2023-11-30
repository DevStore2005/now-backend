<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\RestaurantGrocery\Grocery\GroceryController;
use App\Http\Controllers\Api\RestaurantGrocery\Grocery\ProductController;


Route::group(['PublicRoutes'], function () {

    //GroceryController
    Route::group(['GroceryController'], function () {
        Route::get('/list', [GroceryController::class, 'index']);
        Route::get('/show/{id}', [GroceryController::class, 'show']);
    });

    //ProductController
    Route::group(['ProductController', 'prefix' => 'product'], function () {
        Route::get('list/{resturant_id}', [ProductController::class, 'index']);
        Route::get('show/{product}', [ProductController::class, 'show']);
    });
});

Route::group(['PrivateRoutes', 'middleware' => ['auth:api', 'user']], function () {

});

