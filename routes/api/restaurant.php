<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\RestaurantGrocery\Restaurant\ProductController;
use App\Http\Controllers\Api\RestaurantGrocery\Restaurant\RestaurantController;


Route::group(['PublicRoutes'], function () {

    //RestaurantController
    Route::group(['RestaurantController'], function () {
      Route::get('/list', [RestaurantController::class, 'index']);
      Route::get('/show/{id}', [RestaurantController::class, 'show']);
    });

    //ProductController
    Route::group(['ProductController', 'prefix' => 'food'], function () {
      Route::get('list/{resturant_id}', [ProductController::class, 'index']);
      Route::get('show/{product}', [ProductController::class, 'show']);
    });
});

Route::group(['PrivateRoutes', 'middleware' => ['auth:api', 'user']], function () {
  
  //RestaurantController
  // Route::group(['RestaurantController'], function () {
  //   Route::get('/restaurants',[RestaurantController::class,'index']);
  //   Route::get('/restaurant-products/{resturant_id}', [FoodController::class, 'products']);
  //   Route::post('/add-to-cart', [RestaurantController::class, 'addToCart']);
  //   Route::get('/cart', [RestaurantController::class, 'getCart']);
  // });
});



//  Route::group(['prefix' => 'api/restaurant','middleware' => ['auth:api', 'user']], function () {


//  });


