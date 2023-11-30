<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\Provider\AuthController;
use App\Http\Controllers\Api\Provider\PlanController;
use App\Http\Controllers\Api\Provider\MediaController;
use App\Http\Controllers\Api\Provider\OrderController;
use App\Http\Controllers\Api\Provider\CreditController;
use App\Http\Controllers\Api\Provider\VehicleController;
use App\Http\Controllers\Api\Provider\FeedbackController;
use App\Http\Controllers\Api\Provider\ProviderController;
use App\Http\Controllers\Api\Provider\ScheduleController;
use App\Http\Controllers\Api\Provider\ServicesController;
use App\Http\Controllers\Api\Provider\PortfolioController;
use App\Http\Controllers\Api\Provider\BlockedSlotController;
use App\Http\Controllers\Api\Provider\TransactionController;
use App\Http\Controllers\Api\Provider\SubscriptionController;
use App\Http\Controllers\Api\Provider\PaymentMethodController;


Route::group(['PublicRoutes'], function () {

	//Auth Controller
	Route::group(['AuthController'], function () {
		Route::group(['prefix' => 'signup'], function () {
			Route::post('/', [AuthController::class, 'signup']);
			Route::post('/email', [AuthController::class, 'signupEmail']);
			Route::post('/email/verify', [AuthController::class, 'signupEmailVerify']);
			Route::post('phone/verify', [AuthController::class, 'signupPhoneVerify']);
			Route::post('phone/verify/resend', [AuthController::class, 'signupPhoneVerifyResend']);
			Route::post('email/verify/resend', [AuthController::class, 'signupEmailVerifyResend']);
		});
		Route::post('profile-image', [AuthController::class, 'profileImage']);
		Route::post('login', [AuthController::class, 'login']);
		Route::post('social-login', [AuthController::class, 'handleSocialLogin']);
		Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
		Route::post('change-password', [AuthController::class, 'changePassword']);
	});

	Route::group(['ProviderController'], function () {
		Route::get('list', [ProviderController::class, 'index']);
		Route::get('show/{id}', [ProviderController::class, 'show']);
	});

	// Services Controller
	Route::group(['ServicesController', 'prefix' => 'services'], function () {
		Route::get('main', [ServicesController::class, 'getMain']);
		Route::get('sub/{id}', [ServicesController::class, 'getSub']);
	});

	//VehicleControler
	Route::group(['VehicleController', 'prefix' => 'vehicle'], function () {
		Route::get('types', [VehicleController::class, 'index']);
	});
});


Route::group(['PrivateRoutes'], function () {

	//api auth middleware
	Route::group(['middleware' => ['auth:api', 'provider']], function () {

		//Auth Controller
		Route::group(["AuthController"], function () {
			Route::group(['prefix' => 'signup'], function () {
				Route::post('name', [AuthController::class, 'signup_name']);
				Route::post('profile', [AuthController::class, 'profile']);
			});
			Route::get('profile', [AuthController::class, 'profile']);
			Route::post('device-token', [AuthController::class, 'deviceToken']);
			Route::delete('delete', [AuthController::class, 'delete']);
		});

		// Media Controller
		Route::group(['MediaController', 'prefix' => 'media'], function () {
			Route::post('store', [MediaController::class, 'store']);
			Route::get('list', [MediaController::class, 'index']);
		});

		//Services Controller
		Route::group(['ServicesController'], function () {
			Route::post('signup/my-services', [ServicesController::class, 'post']);

			Route::group(['prefix' => 'services'], function () {
				Route::get('/', [ServicesController::class, 'providerServices']);
				Route::post('/status', [ServicesController::class, 'updateStatusProviderService']);
				Route::get('schedule', [ServicesController::class, 'scheduleList']);
				Route::post('store-schedule', [ServicesController::class, 'schedule']);
				Route::get('zip-code-list', [ServicesController::class, 'zipCodeList']);
				Route::post('zip-code', [ServicesController::class, 'storeZipCode']);
				// Route::patch('zip-code/{id}', [ServicesController::class, 'updateZipCode']);
				Route::delete('zip-code/{id}', [ServicesController::class, 'deleteZipCode']);
			});
		});

		// OrderController
		Route::group(['OrderController', 'prefix' => 'order'], function () {
			Route::get('/', [OrderController::class, 'index']);
			Route::post('status/{id}', [OrderController::class, 'acceptOrReject']);
			Route::post('chat-request/{id}', [OrderController::class, 'acceptOrRejectChat']);
			Route::post('quotation/{id}', [OrderController::class, 'quotation']);
			Route::post('start-end', [OrderController::class, 'workingTime']);
			Route::post('worked-status', [OrderController::class, 'workingStatus']);
		});

		// ProviderController
		Route::group(['ProviderController'], function () {
			Route::post('/payment-update', [ProviderController::class, 'paymentUpdate']);
			Route::get('/show-credit', [ProviderController::class, 'showCredit']);
		});

		// PlanController
		Route::get('users-plan',
			[PlanController::class, 'usersSubscriptions']
		);
		Route::apiResource('plan', 'Api\Provider\PlanController');

		// SubscriptionController
		Route::group(['SubscriptionController', 'prefix' => 'subscription'], function () {
			Route::get('/', [SubscriptionController::class, 'index']);
			Route::get('intent', [SubscriptionController::class, 'setupIntent']);
			Route::post('buy', [SubscriptionController::class, 'buyCredit']);
			Route::post('store', [SubscriptionController::class, 'store']);
			Route::post('cancel', [SubscriptionController::class, 'cancel']);
		});

		//TransactionController
		Route::group(['TransactionController', 'prefix' => 'transaction'], function () {
			Route::post('/subscribe', [TransactionController::class, 'subscribe']);
			Route::post('/payment', [TransactionController::class, 'makeTransaction'])->name('stripe.payment');
			Route::get('/history', [TransactionController::class, 'transationHistory']);
			Route::post('/withdrawal', [TransactionController::class, 'withdrawal']);
		});

		// CreditController
		Route::group(['CreditController', 'prefix' => 'credit'], function () {
			Route::get('/', [CreditController::class, 'index']);
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

		//VehicleControler
		Route::group(['VehicleController', 'prefix' => 'vehicle'], function () {
			Route::post('store', [VehicleController::class, 'store']);
			Route::patch('update/{id}', [VehicleController::class, 'update']);
			Route::get('delete/{id}', [VehicleController::class, 'destroy']);
		});

		//NotificationController
		Route::namespace('Api')->prefix('notification')->group(function () {
			Route::get('/', [NotificationController::class, 'index']);
			// Route::get('/read/{id}', [NotificationController::class, 'read']);
			// Route::get('/unread', [NotificationController::class, 'unread']);
			// Route::get('/read-all', [NotificationController::class, 'readAll']);
		});

		//PortfolioController
		Route::namespace('Api')->prefix('portfolio')->group(function () {
			Route::get('/', [PortfolioController::class, 'index']);
			Route::post('/store', [PortfolioController::class, 'store']);
			Route::patch('/update/{portfolio}', [PortfolioController::class, 'update']);
			Route::delete('/delete/{portfolio?}', [PortfolioController::class, 'destroy']);
			// Route::delete('/delete', [PortfolioController::class, 'deleteImage']);
		});

		//ScheduleController
		Route::namespace('Api')->prefix('schedule')->group(function () {
			Route::get('/', [ScheduleController::class, 'index']);
			Route::post('/store', [ScheduleController::class, 'store']);
		});

		//BlockedSlotController
		Route::namespace('Api')->prefix('blocked-slot')->group(function () {
			Route::get('/', [BlockedSlotController::class, 'index']);
			Route::post('/store', [BlockedSlotController::class, 'store']);
			Route::put('/{blockedSlot}/update', [BlockedSlotController::class, 'update']);
			Route::delete('/{blockedSlot}/delete', [BlockedSlotController::class, 'destroy']);
		});

		//PaymentMethodController
		Route::namespace('Api')->prefix('payment-method')->group(function () {
			Route::get('/', [PaymentMethodController::class, 'index']);
			Route::post('/', [PaymentMethodController::class, 'toggle']);
		});
		// Route::resource('payment-method', 'Api\User\PaymentMethodController')->only(['index']);
	});
});
