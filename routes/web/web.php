<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CommissionController;
use App\Http\Controllers\Admin\CurrencyController;
use App\Http\Controllers\Admin\FrontPageController;
use App\Http\Controllers\Admin\HelpPageController;
use App\Http\Controllers\Admin\HistoryController;
use App\Http\Controllers\Admin\LinkController;
use App\Http\Controllers\Admin\MessageController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\PartnerController;
use App\Http\Controllers\Admin\PlanController;
use App\Http\Controllers\Admin\PortfolioController;
use App\Http\Controllers\Admin\SlidersController;
use App\Http\Controllers\Admin\TransactionController;
use App\Models\User;
use Illuminate\Http\Request;
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

Route::get('/image', function (Request $request) {
    if ($request->image) return response()->file(public_path($request->image));
})->name('image');

Route::get('/email', function () {
    // 	$serviceRequest = ServiceRequest::latest()->with([
    // 		'user',
    // 		'provider',
    // 		"book_time_slots",
    // 		"quotation_info",
    // 		"transaction",
    // 		"providers_subscription.subscription_histories"
    // 	])->where('is_quotation', 1)
    // 	->first();
    $user = User::where('email', 'asadrazajutt1@gmail.com')->latest()->first();
//    dispatch(new UserRegisterJob($user));
    $markdown = new \Illuminate\Mail\Markdown(view(), config('mail.markdown'));
    $html = $markdown->render('emails.welcome', [
        'user' => $user,
        'to_user' => 'user'
    ]);
    return $html;
});

Route::group(['PublicRoutes', 'middleware' => ['guest', 'subdomain', 'setlocale']], function () {
    Route::get('/', function () {
        return redirect()->route('admin.auth.login');
    });
    // Route::get('/login', function () {return redirect()->route('admin.login');})->name('login');

    Route::match(['get', 'post'], '/login', 'Admin\AuthController@login')->name('admin.auth.login');
    Route::match(['get', 'post'], '/forgot-password', 'Admin\AuthController@forgotPassword')->name('admin.auth.forgotPassword');
    Route::get('/reset-password/{token}', 'Admin\AuthController@resetPassword')->name('password.reset');
    Route::post('/reset-password', 'Admin\AuthController@updatePassword')->name('password.update');
});

// Admin Routes
Route::group(['middleware' => ['subdomain', 'auth', 'admin', 'setlocale'], 'namespace' => 'Admin', 'as' => 'admin.'], function () {


    // Auth Controller
    Route::group(['AuthController'], function () {
        Route::get('logout', 'AuthController@logout')->name('logout');
        Route::post('change-password', 'AuthController@changePassword')->name('changePassword');
    });

    Route::group(['AdminController'], function () {
        Route::get('dashboard', 'AdminController@dashboard')->name('dashboard');
        Route::match(['get', 'post'], 'profile', 'AdminController@profile')->name('profile');
    });

    // user Controller
    Route::group(['UserController', 'prefix' => 'users'], function () {
        Route::get('list', 'UserController@index')->name('users');
        Route::get('providers', 'UserController@providers')->name('providers');
        Route::get('restaurants', 'UserController@restaurants')->name('restaurants');
        Route::get('grocery-store', 'UserController@groceryStores')->name('grocery_stores');
        Route::get('update/{status}/{id}', 'UserController@user_update_status')->name('user_update_status');
        Route::post('user_update_verified', 'UserController@user_update_verified')->name('user_update_verified');
        Route::get('profile/{user}', 'UserController@profile')->name('profiles.profile');
        Route::get('delete/{id}', 'UserController@delete')->name('users.delete');
        Route::post('credit/{id}', 'UserController@credit')->name('users.credit');
        Route::get('download/{role}', 'UserController@download')->name('users.download');
    });

    //Service Controller
    Route::group(['ServiceController'], function () {
        //Services
        Route::group(['prefix' => 'services'], function () {
            Route::get('list', 'ServiceController@main_list')->name('services_list');
            Route::post('create', 'ServiceController@main_create')->name('services_create');
            Route::get('update/{status}/{id}', 'ServiceController@main_update_status')->name('services_update_status');
            Route::get('service-content/{type}/{id}', 'ServiceController@content_list')->name('service_content_list');
            Route::post('create-content', 'ServiceController@createServiceContent')->name('service_create_content');
            Route::get('delete/{type}/{id}', 'ServiceController@destroy')->name('service_delete');
        });

        //Sub-Services
        Route::group(['prefix' => 'sub-services'], function () {
            Route::get('list', 'ServiceController@sub_list')->name('sub_services_list');
            Route::post('create', 'ServiceController@sub_create')->name('sub_services_create');
            Route::get('update/{status}/{id}', 'ServiceController@sub_update_status')->name('sub_services_update_status');
            Route::get('service-content/{type}/{id}', 'ServiceController@content_list')->name('sub_service_content_list');
            Route::post('create-content', 'ServiceController@createServiceContent')->name('sub_service_create_content');
            Route::post('update-content/{serviceContent}', 'ServiceController@updateServiceContent')->name('sub_service_update_content');
            Route::get('delete/content/{serviceContent}', 'ServiceController@deleteServiceContent')->name('delete_service_content');
        });
    });

    //Question Controller
    Route::group(['QuestionController', 'prefix' => 'quetions'], function () {
        Route::get('/{id}', 'QuestionController@index')->name('question_list');
        Route::post('store', 'QuestionController@store')->name('question_store');
        Route::put('update/{question}', 'QuestionController@update')->name('question_update');
        Route::get('delete/{question}', 'QuestionController@destroy')->name('destroy');
    });

    //Option Controller
    Route::group(['OptionController', 'prefix' => 'option'], function () {
        Route::post('store', 'OptionController@store')->name('option_store');
    });

    //vehicle-type
    Route::group(['VehicleController', 'prefix' => 'vehicle'], function () {
        Route::group(['type', 'prefix' => 'type'], function () {
            Route::get('/', 'VehicleController@index')->name('vehicle.type.index');
            Route::post('store', 'VehicleController@store')->name('vehicle.type.store');
            Route::patch('update/{vehicleType}', 'VehicleController@update')->name('vehicle.type.update');
            Route::get('delete/{vehicleType}', 'VehicleController@destroy')->name('vehicle.type.destroy');
        });
    });

    //Category Controller
    Route::name('category.')->prefix('category')->group(function () {
        Route::get('/', [CategoryController::class, 'index'])->name('index')->defaults('type', 'whereNotNull');
        Route::get('/blog', [CategoryController::class, 'index'])->name('index.blog')->defaults('type', 'whereNull');
        Route::post('/store', [CategoryController::class, 'store'])->name('store');
        Route::get('/update_status/{status}/{category}', [CategoryController::class, 'updateStatus'])->name('update_status');
        Route::post('/update', [CategoryController::class, 'update'])->name('update');
        Route::get('/delete/{category}', [CategoryController::class, 'destroy'])->name('delete');
    });

    //CurrenceyController
    Route::name('currency.')->prefix('currency')->group(function () {
        Route::get('/', [CurrencyController::class, 'index'])->name('index');
        Route::post('/store', [CurrencyController::class, 'store'])->name('store');
        Route::put('/update/{currency}', [CurrencyController::class, 'update'])->name('update');
        // Route::post('/update', [CurrencyController::class, 'update'])->name('update');
        Route::get('/delete/{currency}', [CurrencyController::class, 'destroy'])->name('delete');
    });

    //LinkController
    Route::name('link.')->prefix('link')->group(function () {
        Route::get('/', [LinkController::class, 'index'])->name('index');
        Route::get('/social', [LinkController::class, 'social'])->name('social');
        Route::get('/blogs', [LinkController::class, 'blog'])->name('blog');
        Route::post('/store', [LinkController::class, 'store'])->name('store');
        // Route::get('/update_status/{status}/{link}', [LinkController::class, 'updateStatus'])->name('update_status');
        Route::put('/update/{link}', [LinkController::class, 'update'])->name('update');
        Route::get('/delete/{link}', [LinkController::class, 'destroy'])->name('delete');
    });

    //ArticleController
    Route::get('article/{article}/delete', 'ArticleController@destroy')->name('article.destroy');
    Route::resource('article', 'ArticleController')->except(['destroy', 'show']);

    //FaqController
    Route::get('faq/{faq}/delete', 'FaqController@destroy')->name('faq.destroy');
    Route::resource('faq', 'FaqController')->except(['destroy']);

    //MessageController
    Route::name('chat.')->prefix('chat')->group(function () {
        Route::get('/', [MessageController::class, 'index'])->name('index');
        Route::post('/store', [MessageController::class, 'store'])->name('store');
        Route::get('/messages/{id}', [MessageController::class, 'chat'])->name('chat');
    });

    //TransactionController
    Route::name('transaction.')->prefix('transaction')->group(function () {
        Route::get('/', [TransactionController::class, 'index'])->name('index');
        Route::get('/pay/{user}', [TransactionController::class, 'pay'])->name('pay');
    });

    //PageController
    Route::name('page.')->prefix('page')->group(function () {
        Route::get('/', [PageController::class, 'index'])->name('index');
        Route::get('/create', [PageController::class, 'create'])->name('create');
        Route::post('/store', [PageController::class, 'store'])->name('store');
        Route::get('/edit/{page}', [PageController::class, 'edit'])->name('edit');
        Route::post('/update/{page}', [PageController::class, 'update'])->name('update');
        Route::get('/delete/{page}', [PageController::class, 'destroy'])->name('delete');
    });

    Route::resource('help-pages', HelpPageController::class);

    Route::name('commissions.')->prefix('commission')->group(function () {
        Route::get('/', [CommissionController::class, 'index'])->name('index');
        Route::post('/store', [CommissionController::class, 'store'])->name('store');
    });

    Route::name('plan.')->prefix('plan')->group(function () {
        Route::get('/', [PlanController::class, 'index'])->name('index');
        Route::post('/store', [PlanController::class, 'store'])->name('store');
        Route::put('/update/{plan}', [PlanController::class, 'update'])->name('update');
        Route::get('/delete/{plan}', [PlanController::class, 'destroy'])->name('delete');
    });

    Route::name('portfolios.')->prefix('portfolio')->group(function () {
        Route::get('change-status/{type}/{id}/{status}', [PortfolioController::class, 'changeStatus'])->name('status');
    });

    Route::name('history.')->prefix('history')->group(function () {
        Route::get('/{type}/{provider_id}', [HistoryController::class, 'index'])->name('index');
        Route::get('provider/{provider_id}/services', [HistoryController::class, 'providerServices'])->name('provider.services');
        Route::get('user/{user_id}/services', [HistoryController::class, 'userServices'])->name('user.services');
    });

    // BlogController
    Route::get('blog/{blog}/delete', 'BlogController@destroy')->name('blog.destroy');
    Route::resource('blog', 'BlogController')->except(['show', 'destroy']);

    Route::name('front-pages.')->prefix('front-pages')->group(function () {
        Route::post('/apps', [FrontPageController::class, 'appUrls'])->name('appUrls');
        Route::post('/info', [FrontPageController::class, 'appInfo'])->name('appInfo');
        Route::get('/partner', [PartnerController::class, 'index'])->name('partner');
        Route::post('/partner', [PartnerController::class, 'store'])->name('partner.store');
        Route::put('/partner/{partner}', [PartnerController::class, 'update'])->name('partner.update');
        Route::get('/partner/{partner}/delete', [PartnerController::class, 'destroy'])->name('partner.delete');
    });
    Route::resource('front-pages', 'FrontPageController')->except(['show', 'edit', 'destroy']);

    Route::get('payment-method/{paymentMethod}/delete', 'PaymentMethodController@destroy')->name('payment-method.destroy');
    Route::resource('payment-method', 'PaymentMethodController')->except(['show', 'edit', 'destroy']);
    Route::resource('seos', 'SeoController')->except(['show']);
    Route::resource('countries', 'CountryController')->except(['show']);

    Route::resource('sliders', SlidersController::class)->except('show');
    Route::get('sliders/change-status/{slider}', [SlidersController::class, 'changeStatus'])
        ->name('sliders.status.change');
});
