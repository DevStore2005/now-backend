<?php

namespace App\Http\Controllers\Api\Provider;

use Stripe\Stripe;
use App\Models\Plan;
use Stripe\Customer;
use App\Http\Helpers\Common;
use Illuminate\Http\Request;
use App\Utils\HttpStatusCode;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Requests\BuyCreditRequest;
use App\Http\Requests\CreateSubscriptionRequest;
use App\Models\Transaction;

class SubscriptionController extends Controller
{
    /**
     *  @var Plan $_plan 
     *  @var Transaction $_transaction
     *  @access private
     */
    private $_plan, $_transaction;

    public function __construct(Plan $plan, Transaction $_transaction)
    {
        $this->_plan = $plan;
        $this->_transaction = $_transaction;
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    /**
     * Display a listing of the Plan.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try {
            $plans = $this->_plan->WhereNull('provider_id')->get();
            if ($plans->isNotEmpty()) {
                return response()->json([
                    'error'     => false,
                    'status'    => HttpStatusCode::$statusTexts[HttpStatusCode::OK],
                    'data'      => $plans,
                    'message'   => "Plan List"
                ], HttpStatusCode::OK);
            }
            return response()->json([
                'error'     => true,
                'status'    => HttpStatusCode::$statusTexts[HttpStatusCode::NOT_FOUND],
                'message'   => "No Plan Found"
            ], HttpStatusCode::NOT_FOUND);
        } catch (\Exception $e) {
            $this->make_log(__METHOD__, $e, 'error');
            return response()->json([
                'error'     => true,
                'status'    => HttpStatusCode::$statusTexts[HttpStatusCode::INTERNAL_SERVER_ERROR],
                'message'   => 'Oops! Something went wrong'
            ], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }

    public function setupIntent(Request $request)
    {
        try {
            $intent = $request->user()->createSetupIntent();
            if ($intent)
                return response()->json([
                    'error'     => false,
                    'status'    => HttpStatusCode::$statusTexts[HttpStatusCode::OK],
                    'intent'    => $intent,
                    'message'   => 'Setup intent created successfully',
                ], HttpStatusCode::OK);
            else
                return response()->json([
                    'error'     => true,
                    'status'    => HttpStatusCode::$statusTexts[HttpStatusCode::CONFLICT],
                    'message'   => 'Setup intent could not be created',
                ], HttpStatusCode::CONFLICT);
        } catch (\Exception $e) {
            $this->make_log(__METHOD__, $e, 'error');
            return response()->json([
                'error'     => true,
                'status'    => HttpStatusCode::$statusTexts[HttpStatusCode::INTERNAL_SERVER_ERROR],
                'message'   => 'Oops! Something went wrong',
            ], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Buy Credit for the provider. 
     *
     * @param  \App\Http\Requests\BuyCreditRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function buyCredit(BuyCreditRequest $request)
    {
        try {
            $user = $request->user();
            $plan = $this->_plan->whereStripe_name($request->stripe_name)->first();
            if (!$plan->price)  return response()->json([
                'error'     => true,
                'status'    => HttpStatusCode::$statusTexts[HttpStatusCode::NOT_FOUND],
                'message'   => 'Plan not found',
            ], HttpStatusCode::NOT_FOUND);
            $error = null;
            $payment = null;
            if ($request->token) {
                $payment = Common::stripe_payment($request->token, $plan->price, "Buy Credit Package");
                $error = $payment['error'];
                $payment = $payment['data'];
            } else if ($request->card_id) {
                Customer::update($user->stripe_id, [
                    'default_source' => $request->card_id,
                ]);
                $payment = Common::stripe_payment(null, $plan->price, "Buy Credit Package");
                $error = $payment['error'];
                $payment = $payment['data'];
            }
            if ($error) {
                return response()->json([
                    'error'     => true,
                    'status'    => HttpStatusCode::$statusTexts[HttpStatusCode::CONFLICT],
                    'message'   => is_string($payment) ? $payment : 'Payment could not be processed',
                ], HttpStatusCode::CONFLICT);
            }
            if ($payment && $payment->id && $payment->amount_captured) {
                $transaction = $this->_transaction->create([
                    'provider_id'       => $user->id,
                    "payment_id"        => $payment->id,
                    "amount"            => '+' . $payment->amount_captured / 100,
                    "amount_captured"   => $payment->amount_captured / 100,
                    "status"            => $payment->status,
                    "payment_method"    => $payment->payment_method_details->card->brand,
                ]);

                $user->credit = $user->credit + $plan->credit;
                $user->save();

                return response()->json([
                    'error'     => false,
                    'data'      => $transaction,
                    'message'   => HttpStatusCode::$statusTexts[HttpStatusCode::OK]
                ], HttpStatusCode::OK);
            }
            return response()->json([
                'error'     => true,
                'message'   => "Transaction failed"
            ], HttpStatusCode::CONFLICT);
        } catch (\Exception $e) {
            $this->make_log(__METHOD__, $e, 'error');
            return response()->json([
                'error'     => true,
                'status'    => HttpStatusCode::$statusTexts[HttpStatusCode::INTERNAL_SERVER_ERROR],
                'message'   => 'Oops! Something went wrong'
            ], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  CreateSubscriptionRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CreateSubscriptionRequest $request)
    {

        try {
            $user = $request->user();
            $plan = $this->_plan->whereStripe_name($request->stripe_name)->first();
            dd($user->subscribed($plan->stripe_name));
            if ($user->subscribed($plan->stripe_name)) {
                $user->subscription($plan->stripe_name)->cancelAt(
                    now()->addDays(2)
                );
                return response()->json([
                    'error' => true,
                    'status' => HttpStatusCode::$statusTexts[HttpStatusCode::CONFLICT],
                    'message' => 'You are already subscribed to this plan',
                ], HttpStatusCode::CONFLICT);
            } else {
                $subscribe = $user->newSubscription(
                    $plan->stripe_name,
                    $plan->stripe_id,
                )->create($request->paymentMethodId);
                return response()->json([
                    'error'     => false,
                    'data'      => $subscribe,
                    'status'    => HttpStatusCode::$statusTexts[HttpStatusCode::OK],
                ], HttpStatusCode::OK);
            }
        } catch (\Exception $e) {
            $this->make_log(__METHOD__, $e, 'error');
            return response()->json([
                'error'     => true,
                'status'    => HttpStatusCode::$statusTexts[HttpStatusCode::INTERNAL_SERVER_ERROR],
                'message'   => 'Oops! Something went wrong',
            ], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Cancel subscription
     * @param Request $request
     */
    public function cancel(Request $request)
    {
        $this->validate($request, [
            'plan' => 'required',
        ]);

        try {
            $user = $request->user();
            if (!$user->subscribed($request->plan)) {
                return response()->json([
                    'error'     => true,
                    'status'    => HttpStatusCode::$statusTexts[HttpStatusCode::CONFLICT],
                    'message'   => 'You are not subscribed to this plan',
                ], HttpStatusCode::CONFLICT);
            }
            $user->subscription($request->plan)->cancel();
            return response()->json([
                'error'     => false,
                'status'    => HttpStatusCode::$statusTexts[HttpStatusCode::OK],
                'message'   => 'Subscription cancelled successfully',
            ], HttpStatusCode::OK);
        } catch (\Exception $e) {
            $this->make_log(__METHOD__, $e, 'error');
            return response()->json([
                'error'     => true,
                'status'    => HttpStatusCode::$statusTexts[HttpStatusCode::INTERNAL_SERVER_ERROR],
                'message'   => 'Oops! Something went wrong',
            ], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }
}
