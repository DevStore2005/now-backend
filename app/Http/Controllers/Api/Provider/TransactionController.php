<?php

namespace App\Http\Controllers\Api\Provider;

use Stripe\Charge;
use Stripe\Stripe;
use App\Models\Plan;
use App\Models\User;
use Stripe\Customer;
use App\Models\Credit;
use App\Utils\UserType;
use App\Utils\AccountType;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Laravel\Cashier\Cashier;
use App\Utils\HttpStatusCode;
use App\Jobs\WithdrawalRequestJob;
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
     * @var \App\Models\User $_user
     * @var \App\Models\Plan $_plan
     * @var \App\Models\Transaction $_transaction
     */
    private $_user, $_plan, $_transaction;

    /**
     * Create a new controller instance.
     * @param  \App\Models\Transaction $transaction
     * @return void
     */
    public function __construct(Transaction $transaction, User $user, Plan $plan)
    {
        $this->_transaction = $transaction;
        $this->_user = $user;
        $this->_plan = $plan;
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse|null
     */
    public function index(Request $request)
    {
        try {
          
        } catch (\Exception $e) {
            Log::error('TransactionController -> index', [$e->getMessage()]);
            return response()->json([
                'error' => true,
                'message' => HttpStatusCode::$statusTexts[HttpStatusCode::INTERNAL_SERVER_ERROR]
            ], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Provider can buy Credit
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function makeTransaction(Request $request)
    {
        $request->validate([
            'amount' => 'required',
            'token' => 'required',
        ]);
        
        try {
            $amount = $request->amount * 100;
            try {
                $data = Charge::create([
                    "amount" => $amount,
                    "currency" => "usd",
                    "source" => $request->token,
                    "description" => "Buy credit"
                ]);
                // Stripe::setApiKey(config('services.stripe.secret'));
                // $customer = Customer::retrieve($request->user()->stripe_id);

                // if($customer === null){
                //     $customer = Customer::create(array(
                //         'email' => $request->user()->email,
                //         'source'  => $request->token
                //     ));
                // }

                // $user = $request->user();
                // $user->stripe_id = $customer->id;
                // $user->save();

                // $data = Charge::create(array(
                //     'customer' => $customer->id,
                //     'amount'   => $amount,
                //     'currency' => 'usd',
                //     "description" => "Buy credit"
                // ));
            } catch (CardException $cardExp) {
                return response()->json([
                    'error' => true,
                    'message' => $cardExp->getMessage()
                ], HttpStatusCode::OK);
            } catch (RateLimitException  $rateExp) {
                return response()->json([
                    'error' => true,
                    'message' => $rateExp->getMessage()
                ], HttpStatusCode::OK);
            } catch (InvalidRequestException   $invalidExp) {
                return response()->json([
                    'error' => true,
                    'message' => $invalidExp->getMessage()
                ], HttpStatusCode::OK);
            } catch (AuthenticationException   $authExp) {
                return response()->json([
                    'error' => true,
                    'message' => $authExp->getMessage()
                ], HttpStatusCode::OK);
            } catch (ApiConnectionException   $apiConnectionExp) {
                return response()->json([
                    'error' => true,
                    'message' => $apiConnectionExp->getMessage()
                ], HttpStatusCode::OK);
            } catch (ApiErrorException    $apiErrorExp) {
                return response()->json([
                    'error' => true,
                    'message' => $apiErrorExp->getMessage()
                ], HttpStatusCode::OK);
            } catch (\Exception $exp) {
                Log::error('TransactionController -> Stripe', [$exp->getMessage()]);
                return response()->json([
                    'error' => true,
                    'message' => $exp->getMessage()
                ], HttpStatusCode::OK);
            } 
            
            if($data->id !== null && $data->amount_captured !== null && $data->status === "succeeded"){
                $userId = $request->user()->role === UserType::PROVIDER ? 'provider_id' : "user_id";
                $this->_transaction->$userId = $request->user()->id;
                $this->_transaction->payment_id = $data->id;
                $this->_transaction->amount = '+'.$request->amount;
                $this->_transaction->amount_captured = $data->amount_captured/100;
                $this->_transaction->status = $data->status;
                $this->_transaction->payment_method = $data->payment_method_details->card->brand;
                $this->_transaction->save();

                $user = $this->_user->find($request->user()->id);
                if($user->credit !== null && $user->account_type !== AccountType::PREMIUM){
                    $user->account_type = AccountType::PREMIUM;
                }


                $user->credit = $user->credit + $this->_transaction->amount_captured;
                $user->save();

                return response()->json([
                    'error' => false,
                    'data' => $this->_transaction,
                    'message' => HttpStatusCode::$statusTexts[HttpStatusCode::OK]
                ], HttpStatusCode::OK);
            } else {
                return response()->json([
                    'error' => true,
                    'message' => "Transaction failed"
                ], HttpStatusCode::CONFLICT);
            }
        } catch (\Exception $e) {
            Log::error('TransactionController -> makeTransaction', [$e->getMessage()]);
            return response()->json([
                'error' => true,
                'message' => HttpStatusCode::$statusTexts[HttpStatusCode::INTERNAL_SERVER_ERROR]
            ], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }

    public function subscribe(Request $request)
    {
        $this->validate($request, [
            'token' => 'required',
            'plan' => 'required'
        ]);

        $plan = $this->_plan->where('identifier', $request->plan)
            ->orWhere('identifier', 'premium')
            ->first();

        $newSubscription = $request->user()->newSubscription('default', $plan->stripe_id)->create($request->token);
        dd($newSubscription);

    }

    /**
     * get 
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function transationHistory()
    {
        try {
            $history = $this->_transaction->whereProvider_id(auth()->user()->id)->latest()->paginate(10);
            if($history->isEmpty() === false){
                return response()->json([
                    'error' => false,
                    'data' => $history,
                    'message' => HttpStatusCode::$statusTexts[HttpStatusCode::OK]
                ], HttpStatusCode::OK);
            } else {
                return response()->json([
                    'error' => false,
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

    /**
     * withdrawal request
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function withdrawal(Request $request)
    {
        $provider = $request->user();
        $request->validate([
            'amount' => ['required', 'min:1', function ($attribute, $value, $fail) use ($provider) {
                $provider->load('provider_profile');
                if ($provider->provider_profile && (floatval($provider->provider_profile->earn) <= $value)) {
                    $fail('You do not have enough credit to withdraw');
                }
            }],
            'description' => 'required|string|max:255',
        ]);
        try {
            dispatch(new WithdrawalRequestJob(
                $provider,
                $request->amount,
                $request->description
            ));
            return $this->success(null, 'Withdrawal request has been sent to admin successfully');
        } catch (\Exception $e) {
            $this->make_log(__METHOD__, $e);
            return response()->json([
                'error' => true,
                'message' => HttpStatusCode::$statusTexts[HttpStatusCode::INTERNAL_SERVER_ERROR]
            ], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }
}
