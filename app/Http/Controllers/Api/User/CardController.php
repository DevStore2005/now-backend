<?php

namespace App\Http\Controllers\Api\User;

use Stripe\Token;
use Stripe\Stripe;
use App\Models\User;
use Stripe\Customer;
use App\Utils\MyAppEnv;
use App\Http\Helpers\Common;
use Illuminate\Http\Request;
use App\Utils\HttpStatusCode;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class CardController extends Controller
{
    private $_user, $_customer, $_environment;

    /**
     * Create a new controller instance.
     * @param  User $user
     * @param  App $app
     * @param Customer $customer
     * @return void
     */
    public function __construct(User $user, Customer $customer, App $app)
    {
        $this->_user = $user;
        $this->_customer = $customer;
        $this->_environment = $app::environment();
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            if ($request->user()->stripe_id !== null) {
                $data = $this->_customer->allSources($request->user()->stripe_id);
                return response()->json([
                    'error' => false,
                    'data' => $data,
                    'message' => HttpStatusCode::$statusTexts[HttpStatusCode::OK]
                ], HttpStatusCode::OK);
            }

            return response()->json([
                'error' => true,
                'message' => HttpStatusCode::$statusTexts[HttpStatusCode::NOT_FOUND]
            ], HttpStatusCode::NOT_FOUND);
        } catch (\Exception $e) {
            Log::error('CardController -> index', [$e->getMessage()]);
            return response()->json([
                'error' => true,
                'message' => $this->_environment === MyAppEnv::LOCAL ? $e->getMessage() :  HttpStatusCode::$statusTexts[HttpStatusCode::INTERNAL_SERVER_ERROR]
            ], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'token' => 'required',
            // 'fingerprint' => 'required'
        ]);
        $fingerprint = isset($request->fingerprint) == true ? $request->fingerprint : null;
        try {
            $res = Common::stripe_add_card($request->token, $fingerprint);
            if ($res['error'] == false) {
                return response()->json([
                    'error' => false,
                    'data' => $res['data'],
                    'message' => HttpStatusCode::$statusTexts[HttpStatusCode::OK]
                ], HttpStatusCode::OK);
            }
            if ($res['error'] == true) { 
                $message = null;
                if (isset($res['message'])) {
                    $message = $res['message'];
                } elseif (isset($res['data'])) {
                    $message = $res['data'];
                }
                return response()->json([
                    'error' => true,
                    'message' => $message ?? HttpStatusCode::$statusTexts[HttpStatusCode::INTERNAL_SERVER_ERROR],
                ], HttpStatusCode::CONFLICT);
            }
            return response()->json([
                'error' => true,
                'message' => HttpStatusCode::$statusTexts[HttpStatusCode::INTERNAL_SERVER_ERROR]
            ], HttpStatusCode::INTERNAL_SERVER_ERROR);
        } catch (\Exception $e) {
            Log::error('AuthController -> addCard', [$e->getMessage()]);
            return response()->json([
                'error' => true,
                'message' => $this->_environment === MyAppEnv::LOCAL ? $e->getMessage() :  HttpStatusCode::$statusTexts[HttpStatusCode::INTERNAL_SERVER_ERROR]
            ], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response|null
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response|null
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Request  $resquest
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, $card_id)
    {
        try {
            if ($request->user()->findPaymentMethod($card_id)) {
                $request->user()->removePaymentMethod($card_id);
                return response()->json([
                    'error' => false,
                    'data' => $card_id,
                    'message' => HttpStatusCode::$statusTexts[HttpStatusCode::OK]
                ], HttpStatusCode::OK);
            } else {
                return response()->json([
                    'error' => true,
                    'message' => HttpStatusCode::$statusTexts[HttpStatusCode::NOT_FOUND]
                ], HttpStatusCode::NOT_FOUND);
            }
        } catch (\Exception $e) {
            Log::error('CardController -> destroy', [$e->getMessage()]);
            return response()->json([
                'error' => true,
                'message' => $this->_environment === MyAppEnv::LOCAL ? $e->getMessage() :  HttpStatusCode::$statusTexts[HttpStatusCode::INTERNAL_SERVER_ERROR]
            ], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }
}
