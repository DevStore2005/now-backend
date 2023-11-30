<?php

use App\Http\Controllers\Api\FlutterwaveServiceRequestController;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Http\Request;
use App\Utils\HttpStatusCode;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\FlutterwavePaymentController;

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

Route::get('/', function (): JsonResponse {
    return response()->json([
        'message' => 'Welcome to the API',
        'info' => [
            'zone' => config('app.timezone'),
            'time' => Carbon::now()->toDateTimeString(),
        ],
        'code' => 200
    ], HttpStatusCode::OK);
});

Route::post('logs', function (Request $request) {
    $request->validate([
        'mobile_type' => 'required|string',
        'errors' => 'required|array',
    ]);
    Log::error("Mobile: {$request->mobile_type}", $request->errors);
    return response()->json([
        'error' => false,
        'message' => 'Log saved successfully',
    ], HttpStatusCode::OK);
});

Route::get('/img', function (Request $request) {
    return response()->file(public_path($request->img));
})->name('image');

Route::get('logo', fn() => response()->file(public_path('admin/assets/img/logo.png')));
Route::get('logo-icon', fn() => response()->file(public_path('admin/assets/img/icon.png')));

Route::get('/is-user/{id}', function (User $id): JsonResponse {
    return response()->json([
        'error' => false,
        'message' => 'User found',
    ], HttpStatusCode::OK);
});

Route::get('comment/{blog}', 'Api\CommentController@index')->name('comment.index');

Route::middleware(['auth:api'])->group(function () {
    Route::middleware(['auth:api', 'admin'])->prefix('message')->group(function () {
        Route::get('/', [MessageController::class, 'index']);
        Route::get('/chat/{id}', [MessageController::class, 'chat']);
        Route::get('/active-order-chat', [MessageController::class, 'activeOrderChat']);
        Route::post('/send', [MessageController::class, 'store']);
    });

    Route::apiResource('comment', 'Api\CommentController')->except(['index', 'edit']);
});

Route::apiResource('blog', 'Api\BlogController')->only(['index', 'show']);

Route::apiResource('subscribers', 'Api\SubscribersController')->only(['store']);

Route::apiResource('category', 'Api\CategoryController')->only('index');

Route::group(['FlutterwavePaymentController', 'prefix' => 'flutterwave'], function () {
    Route::post('services/service-request', [FlutterwaveServiceRequestController::class, 'store'])->middleware('auth:api');
    Route::post('/webhook', [FlutterwavePaymentController::class, 'webhook']);
    Route::post('/payment/verify', [FlutterwavePaymentController::class, 'verify'])->middleware('auth:api');
});
Route::get('/seos', [\App\Http\Controllers\Admin\SeoController::class, 'getAll']);
Route::get('/countries', [\App\Http\Controllers\Api\CountryController::class, 'index']);
Route::get('/sliders', [\App\Http\Controllers\Api\CountryController::class, 'sliders']);
Route::get('/get-partner', [\App\Http\Controllers\Api\CommonController::class, 'getPartner']);
Route::get('/help-pages', [\App\Http\Controllers\Api\CommonController::class, 'getHelpPage']);
Route::get('/country-wise-state/{country}', [\App\Http\Controllers\Api\CommonController::class, 'getCountryWiseCity']);
