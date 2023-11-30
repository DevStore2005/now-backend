<?php

namespace App\Http\Controllers\Api\User;

use Stripe\Card;
use Stripe\Token;
use Stripe\Charge;
use Stripe\Stripe;
use App\Models\Plan;
use App\Models\User;
use Stripe\Customer;
use App\Models\Credit;
use App\Utils\AppConst;
use App\Utils\MyAppEnv;
use App\Utils\UserType;
use Stripe\PaymentIntent;
use App\Utils\AccountType;
use App\Models\Transaction;
use App\Http\Helpers\Common;
use Illuminate\Http\Request;
use App\Utils\HttpStatusCode;
use App\Models\ServiceRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Stripe\Exception\CardException;
use App\Http\Controllers\Controller;
use Stripe\Exception\ApiErrorException;
use Stripe\Exception\RateLimitException;
use Stripe\Exception\ApiConnectionException;
use Stripe\Exception\AuthenticationException;
use Stripe\Exception\InvalidRequestException;

class TransactionController extends Controller
{
    /**
     * @var Transaction $transaction
     * @var User $user
     * @var Plan $plan
     * @var Common $common
     * @var ServiceRequest $serviceRequest
     * @var string $_environment
     * @var Customer $_customer
     */
    private $_transaction, $_user, $_plan, $_common, $_serviceRequest, $_environment, $_customer;

    /**
     * Create a new controller instance.
     * @param  Transaction $transaction
     * @param  User $user
     * @param  Plan $plan
     * @param  Common $common
     * @param  ServiceRequest $serviceRequest
     * @param  App $app
     * @return void
     */
    public function __construct(Transaction $transaction, User $user, Plan $plan, Common $common, ServiceRequest $serviceRequest, App $app, Customer $customer)
    {
        $this->_transaction = $transaction;
        $this->_user = $user;
        $this->_plan = $plan;
        $this->_common = $common;
        $this->_serviceRequest = $serviceRequest;
        $this->_environment = $app::environment();
        $this->_customer = $customer;
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    /**
     * Display a listing of the payable dues.
     *
     * @return JsonResponse
     */
    public function payable(Request $request)
    {
        try {
            $payable = $this->_transaction->whereUser_id($request->user()->id)->whereStatus(AppConst::PENDING)->get();
            if ($payable->count() > 0) {
                return response()->json([
                    'error' => true,
                    'data' => $payable,
                    'message' => HttpStatusCode::$statusTexts[HttpStatusCode::OK]
                ], HttpStatusCode::OK);
            }
            return response()->json([
                'error' => false,
                'message' => "don't any dues"
            ], HttpStatusCode::OK);
        } catch (\Exception $e) {
            Log::error('TransactionController -> payable', [$e->getMessage()]);
            return response()->json([
                'error' => true,
                'message' => HttpStatusCode::$statusTexts[HttpStatusCode::INTERNAL_SERVER_ERROR]
            ], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * pay pending payable amount
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function payableAmount(Request $request)
    {
        $request->validate([
            'payable_id' => 'required',
        ]);

        if(!isset($request->token) && !isset($request->card_id)){
            return response()->json([
                'error' => true,
                'message' => 'token or card_id is required'
            ], HttpStatusCode::UNPROCESSABLE_ENTITY);
        }
        try {
            $payable = $this->_transaction->where('status', AppConst::PENDING)->find($request->payable_id);
            if ($payable === null) {
                return response()->json([
                    'error' => true,
                    'message' => HttpStatusCode::$statusTexts[HttpStatusCode::NOT_FOUND]
                ], HttpStatusCode::NOT_FOUND);
            }

            $description = "Hourly payable dues of Service Request";
            if (isset($request->card_id)) {
                Stripe::setApiKey(config('services.stripe.secret'));
                Customer::update(auth()->user()->stripe_id, [
                    'default_source' => $request->card_id
                ]);
            }
            $payment = Common::stripe_payment(isset($request->card_id) == true ? null : $request->token, $payable->amount, $description);
            if ($payment['error'] === true) {
                return response()->json([
                    'error' => true,
                    'message' => 'issue in payment'
                ], HttpStatusCode::OK);
            }

            if ($payment['error'] === false) {

                $payable->payment_id = $payment['data']->id;
                $payable->status = $payment['data']->status;
                $payable->amount_captured = $payment['data']->amount_captured / 100;
                $payable->payment_method = $payment['data']->payment_method_details->card->brand;
                $payable->save();

                $serviceRequest = $this->_serviceRequest->find($payable->service_request_id);
                $serviceRequest->payment_status = true;
                $serviceRequest->save();
                return response()->json([
                    'error' => false,
                    'data' => $payable,
                    'message' => 'Successfully paid'
                ], HttpStatusCode::OK);
            }
            return response()->json([
                'error' => true,
                'message' => 'issue in payment'
            ], HttpStatusCode::OK);
        } catch (\Exception $e) {
            Log::error('TransactionController -> payableAmount', [$e->getMessage()]);
            return response()->json([
                'error' => true,
                'message' => HttpStatusCode::$statusTexts[HttpStatusCode::INTERNAL_SERVER_ERROR]
            ], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * get transaction historty
     *
     * @return JsonResponse
     */
    public function transationHistory()
    {
        try {
            $history = $this->_transaction->whereUser_id(auth()->user()->id)->latest()->paginate(20);
            if ($history->isEmpty() === false) {
                return response()->json([
                    'error' => false,
                    'data' => $history,
                    'message' => HttpStatusCode::$statusTexts[HttpStatusCode::OK]
                ], HttpStatusCode::OK);
            } else {
                return response()->json([
                    'error' => true,
                    'message' => HttpStatusCode::$statusTexts[HttpStatusCode::NOT_FOUND]
                ], HttpStatusCode::NOT_FOUND);
            }
        } catch (\Exception $e) {
            Log::error('TransactionController -> transationHistory', [$e->getMessage()]);
            return response()->json([
                'error' => true,
                'message' => HttpStatusCode::$statusTexts[HttpStatusCode::INTERNAL_SERVER_ERROR]
            ], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }

    public function paymentIntent(Request $request)
    {
        $request->validate([
            'amount' => 'required',
        ]);

        $paymentIntent = PaymentIntent::create([
            'amount' => $request->amount * 100,
            'currency' => 'usd',
            'automatic_payment_methods' => [
                'enabled' => true,
            ],
            
        ]);

        $output = [
            'clientSecret' => $paymentIntent->client_secret,
            'paymentIntentId' => $paymentIntent->id,

        ];

        return response()->json([
            'error' => false,
            'data' => $output,
            'message' => HttpStatusCode::$statusTexts[HttpStatusCode::OK]
        ], HttpStatusCode::OK);
    }

    public function cancelPaymentIntent(Request $request)
    {
        $request->validate([
            'payment_id' => 'required',
        ]);
        $paymentIntent = PaymentIntent::retrieve($request->payment_id);
        $paymentIntent->cancel();
        return response()->json([
            'error' => false,
            'message' => 'payment cancelled'
        ], HttpStatusCode::OK);
    }

    // public function subscribe(Request $request)
    // {
    //     $customer = Customer::retrieve(auth()->user()->stripe_id);
    //     dd($customer);
    //     $token = Token::create([
    //         'card' => [
    //             'number' => '4242424242424242',
    //             'exp_month' => 02,
    //             'exp_year' => 2022,
    //             'cvc' => 111,
    //         ],
    //     ]);

    //     dd($customer);

    //     $customer->save();
    //     dd($customer);
    //     $user = User::find($request->user()->id);
    //     // dd(config('services.stripe.secret'));
    //     // if ($user->hasDefaultPaymentMethod()) {
    //     dd($user->addPaymentMethod($token->id));
    //     // }
    //     // else {
    //     dd("# code...");
    //     // }
    //     $user->addPaymentMethod(
    //         $token->id
    //     );
    //     dd($user);
    //     //         $stripe = new \Stripe\StripeClient(
    //     //             config('services.stripe.secret')
    //     //         );
    //     //         $stripe->customers->allSources(
    //     //             'cus_KedMkX6bxV5Rfu'
    //     // ,            ['object' => 'card', 'limit' => 3]
    //     //         );

    //     // $stripe->customers->createSource(
    //     //     'cus_KedMkX6bxV5Rfu',
    //     //     ['source' => $token->id]
    //     // );
    //     // return $customer;

    //     return $customer;
    //     if ($customer === null) {
    //         $customer = Customer::create(array(
    //             'email' => $request->user()->email,
    //             'source'  => $request->token
    //         ));
    //     }

    //     // $this->validate($request, [
    //     //     'token' => 'required',
    //     //     'plan' => 'required'
    //     // ]);

    //     // $plan = $this->_plan->where('identifier', $request->plan)
    //     //     ->orWhere('identifier', 'premium')
    //     //     ->first();

    //     // $newSubscription = $request->user()->newSubscription('default', $plan->stripe_id)->create($request->token);
    //     // dd($newSubscription);
    // }

    /**
     * get saved carda
     *
     * @return JsonResponse
     */
    public function getSavedCard(Request $request)
    {
        return response()->json([
            'error' => true,
            'message' => 'Route change to {{base_url}}/user/card'
        ], HttpStatusCode::OK);

        // try {
        //     if ($request->user()->stripe_id !== null) {
        //         $customer = $this->_customer->allSources($request->user()->stripe_id);
        //         return response()->json([
        //             'error' => false,
        //             'data' => $customer,
        //             'message' => HttpStatusCode::$statusTexts[HttpStatusCode::OK]
        //         ], HttpStatusCode::OK);
        //     }

        //     return response()->json([
        //         'error' => true,
        //         'message' => HttpStatusCode::$statusTexts[HttpStatusCode::NOT_FOUND]
        //     ], HttpStatusCode::NOT_FOUND);
        // } catch (\Exception $e) {
        //     Log::error(['TransactionController -> getSavedCard', $e->getMessage()]);
        //     return response()->json([
        //         'error' => true,
        //         'message' => $this->_environment === MyAppEnv::LOCAL ? $e->getMessage() :  HttpStatusCode::$statusTexts[HttpStatusCode::INTERNAL_SERVER_ERROR]
        //     ], HttpStatusCode::INTERNAL_SERVER_ERROR);
        // }
    }
}
