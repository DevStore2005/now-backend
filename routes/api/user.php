<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\User\AuthController;
use App\Http\Controllers\Api\User\CardController;
use App\Http\Controllers\Api\User\CartController;
use App\Http\Controllers\Api\User\PageController;
use App\Http\Controllers\Api\User\OrderController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\User\AddressController;
use App\Http\Controllers\Api\User\VehicleController;
use App\Http\Controllers\Api\User\CategoryController;
use App\Http\Controllers\Api\User\FeedbackController;
use App\Http\Controllers\Api\User\ProviderController;
use App\Http\Controllers\Api\User\QuestionController;
use App\Http\Controllers\Api\User\ServicesController;
use App\Http\Controllers\Api\User\TransactionController;

Route::group(['PublicRoutes'], function () {

    Route::group(['AuthController'], function () {
        Route::group(['prefix' => 'signup'], function () {
            Route::group(['prefix' => 'phone'], function () {
                Route::post('/', [AuthController::class, 'signupPhone']);
                Route::post('verify', [AuthController::class, 'signupPhoneVerify']);
            });
            Route::group(['prefix' => 'email'], function () {
                Route::post('/', [AuthController::class, 'signupEmail']);
                Route::post('verify', [AuthController::class, 'signupEmailVerify']);
            });

            Route::post('/', [AuthController::class, 'signup']);
        });
        Route::post('profile-image', [AuthController::class, 'profileImage']);
        Route::post('login', [AuthController::class, 'login']);
        Route::post('login/{provider}/callback', [AuthController::class, 'handleProviderCallback']);
        Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
        Route::post('send-otp', [AuthController::class, 'sendOtp']);
        Route::post('resend-otp', [AuthController::class, 'signupEmailVerifyResend']);
        Route::post('change-password', [AuthController::class, 'changePassword']);
        Route::get('profile/{id}', [AuthController::class, 'profile']);
    });


    Route::group(['ServicesController'], function () {
        //Menu
        Route::get('get-menu', [ServicesController::class, 'getMenu']);

        Route::group(['prefix' => 'services'], function () {
            Route::get('provider-list', [ServicesController::class, 'providerList']);
            Route::get('zip-code', [ServicesController::class, 'searchZipCode']);
            Route::get('countries', [ServicesController::class, 'getCountries']);
            Route::get('check-place/{id}', [ServicesController::class, 'hasPlaceId']);
        });
    });

    Route::group(['QuestionController', 'prefix' => 'questions'], function () {
        Route::get('/{id}', [QuestionController::class, 'index']);
    });

    Route::group(['ProviderController', 'prefix' => 'provider'], function () {
        Route::get('list', [ProviderController::class, 'index']);
        Route::get('/{id}', [ProviderController::class, 'show']);
        Route::get('username/{username}', [ProviderController::class, 'showByUsername']);
    });

    //VehicleControler
    Route::group(['VehicleController', 'prefix' => 'vehicle'], function () {
        Route::get('types', [VehicleController::class, 'index']);
    });

    //CategoryController
    Route::namespace('Api\User')->prefix('category')->group(function () {
        Route::get('/', [CategoryController::class, 'index']);
    });

    //CategoryController
    Route::namespace('Api\User')->prefix('page')->group(function () {
        Route::get('/', [PageController::class, 'index']);
    });

    //FeedbackController
    Route::group(['FeedbackController', 'prefix' => 'feedback'], function () {
        Route::get('/', [FeedbackController::class, 'index']);
    });

    //TransactionController
    Route::group(['TransactionController', 'prefix' => 'transaction'], function () {
        Route::get('/payment-intent', [TransactionController::class, 'paymentIntent']);
        Route::get('/cancel-payment-intent', [TransactionController::class, 'cancelPaymentIntent']);
    });

    //ArticleController
    Route::apiResource('article', 'Api\User\ArticleController', ['only' => ['index', 'show']]);

    //FaqController
    Route::apiResource('faqs', 'Api\User\FaqController', ['only' => ['index', 'show']]);

    //ServiceRequestController
    Route::apiResource('service-request', 'Api\User\ServiceRequestController');

});

Route::group(['PrivateRoutes', 'middleware' => ['auth:api', 'user']], function () {

    Route::group(['AuthController'], function () {
        Route::patch('update-profile/{id}', [AuthController::class, 'updateProfile']);
        Route::post('device-token', [AuthController::class, 'deviceToken']);
        Route::post('add-card', [AuthController::class, 'addCard']);
        Route::patch('profile-image', [AuthController::class, 'profileImage']);
        Route::delete('delete', [AuthController::class, 'delete']);
    });

    // ServicesController
    Route::group(['ServicesController', 'prefix' => 'services'], function () {
        Route::post('service-request', [ServicesController::class, 'serviceRequest']);
        Route::post('direct-contact', [ServicesController::class, 'directContact']);
        Route::post('update-service-request/{id}', [ServicesController::class, 'updateServiceRequest']);
        Route::get('provider-schedule/{id}', [ServicesController::class, 'providerSchedule']);
    });

    // OrderController
    Route::group(['OrderController', 'prefix' => 'order'], function () {
        Route::get('/', [OrderController::class, 'getOrder']);
        Route::get('/list', [OrderController::class, 'index']);
        Route::get('/service-request/{serviceRequest}', [OrderController::class, 'showServiceRequest']);
        Route::get('/{order}', [OrderController::class, 'show']);
        Route::get('/cancel/{id}', [OrderController::class, 'cancelRequest']);
        Route::post('status/{id}', [OrderController::class, 'acceptOrReject']);
        Route::post('move-request', [OrderController::class, 'moveRequest']);
        Route::post('create', [OrderController::class, 'create']);
        Route::post('worked-status', [OrderController::class, 'workingStatus']);
    });

    //TransactionController
    Route::group(['TransactionController', 'prefix' => 'transaction'], function () {
        Route::get('/payable', [TransactionController::class, 'payable']);
        Route::get('/history', [TransactionController::class, 'transationHistory']);
        Route::post('/payable-amount', [TransactionController::class, 'payableAmount']);
        Route::get('/card', [TransactionController::class, 'getSavedCard']);
        Route::post('/subscribe', [TransactionController::class, 'subscribe']);
    });

    //MessageControler
    Route::group(['MessageController', 'prefix' => 'message'], function () {
        Route::get('/', [MessageController::class, 'index']);
        Route::get('/chat/{id}', [MessageController::class, 'chat']);
        Route::get('/active-order-chat', [MessageController::class, 'activeOrderChat']);
        Route::post('/send', [MessageController::class, 'store']);
    });

    //FeedbackControler
    Route::group(['FeedbackController', 'prefix' => 'feedback'], function () {
        Route::post('create', [FeedbackController::class, 'store']);
    });

    //CartController
    Route::namespace('Api\User')->prefix('cart')->group(function () {
        Route::get('/', [CartController::class, 'index']);
        Route::post('/', [CartController::class, 'addToCart']);
        Route::delete('/{cart}', [CartController::class, 'removeFromCart']);
        Route::put('/{cart}', [CartController::class, 'updateQuantity']);
    });

    //AddressController
    Route::namespace('Api\User')->prefix('address')->group(function () {
        Route::get('/', [AddressController::class, 'index']);
        Route::post('store', [AddressController::class, 'store']);
        Route::delete('delete/{address}', [AddressController::class, 'destroy']);
        // Route::put('update/{address}', [AddressController::class, 'update']);
    });

    //CardController
    Route::namespace('Api\User')->prefix('card')->group(function () {
        Route::get('/', [CardController::class, 'index']);
        Route::post('store', [CardController::class, 'store']);
        Route::delete('delete/{card_id}', [CardController::class, 'destroy']);
        // Route::put('update/{card}', [CardController::class, 'update']);
    });

    //NotificationController
    Route::namespace('Api')->prefix('notification')->group(function () {
        Route::get('/', [NotificationController::class, 'index']);
        // Route::get('/read/{id}', [NotificationController::class, 'read']);
        // Route::get('/unread', [NotificationController::class, 'unread']);
        // Route::get('/read-all', [NotificationController::class, 'readAll']);
    });

    Route::get('plan/cancel/{plan}', 'Api\User\PlanController@cancel')->name('plan.cancel');
    Route::apiResource('plan', 'Api\User\PlanController')->only('index');
});
