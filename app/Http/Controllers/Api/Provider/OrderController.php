<?php

namespace App\Http\Controllers\Api\Provider;

use App\Events\AlertEvent;
use Carbon\Carbon;
use Stripe\Account;
use App\Models\User;
use App\Models\Credit;
use App\Models\Message;
use App\Utils\AppConst;
use App\Utils\UserType;
use App\Http\Helpers\Fcm;
use App\Models\Commission;
use App\Models\WorkedTime;
use App\Utils\AccountType;
use App\Utils\ServiceType;
use App\Models\Transaction;
use App\Utils\ProviderType;
use App\Utils\WorkingStatus;
use Illuminate\Http\Request;
use App\Utils\HttpStatusCode;
use App\Jobs\RequestAcceptJob;
use App\Jobs\RequestRejectJob;
use App\Models\ServiceRequest;
use App\Utils\TransactionType;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Notifications\OrderNotification;

class OrderController extends Controller
{

    /**
     *  @var \App\Models\User $_user 
     *  @var \App\Models\ServiceRequest $_serviceRequest 
     *  @var \App\Http\Helpers\Fcm $_fcm 
     *  @var \App\Models\Message $_message 
     *  @var \App\Models\Transaction $_transaction 
     *  @var \App\Models\WorkedTime $_workedTime 
     *  @var Commission $_commission 
     */
    private $_user, $_serviceRequest, $_fcm, $_message, $_transaction, $_workedTime, $_commission;

    /**
     * Create a new controller instance.
     * @param  \App\Models\ServiceRequest $serviceRequest
     * @param \App\Models\Commission $commission
     * @return void
     */
    public function __construct(ServiceRequest $serviceRequest, User $user, Fcm $fcm, Message $message, Transaction $transaction, WorkedTime $workedTime, Commission $commission)
    {
        $this->_user = $user;
        $this->_serviceRequest = $serviceRequest;
        $this->_fcm = $fcm;
        $this->_message = $message;
        $this->_transaction = $transaction;
        $this->_workedTime = $workedTime;
        $this->_commission = $commission;
    }

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $order = $this->_serviceRequest->ListOfOrder($request->all());
            if ($order) {
                return response()->json([
                    'error' => false,
                    'message' => 'success',
                    'data' => $order
                ], HttpStatusCode::OK);
            } else {
                return response()->json([
                    'error' => true,
                    'message' => HttpStatusCode::$statusTexts[HttpStatusCode::NOT_FOUND]
                ], HttpStatusCode::NOT_FOUND);
            }
        } catch (\Exception $e) {
            Log::error('OrderController -> index', [$e->getMessage()]);
            return response()->json([
                'error' => true,
                'message' => HttpStatusCode::$statusTexts[HttpStatusCode::INTERNAL_SERVER_ERROR]
            ], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * 
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function acceptOrReject(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:ACCEPTED,REJECTED',
        ]);
        try {
            $provider = $request->user();
            $order = $this->_serviceRequest->whereProvider_id($provider->id)->find($id);
            if ($order === null) {
                return response()->json([
                    'error' => true,
                    'message' => HttpStatusCode::$statusTexts[HttpStatusCode::NOT_FOUND]
                ], HttpStatusCode::NOT_FOUND);
            } else {
                switch ($order->status) {
                    case  AppConst::ACCEPTED:
                        return response()->json([
                            'error' => true,
                            'data' => $order,
                            'message' => "Offer already Accepted"
                        ], HttpStatusCode::OK);

                    case AppConst::REJECTED:
                        return response()->json([
                            'error' => true,
                            'data' => $order,
                            'message' => "Offer already Rejected"
                        ], HttpStatusCode::OK);

                    default:
                        if ($order->is_quotation == true && $request->status === AppConst::ACCEPTED) {
                            $order->load('requested_sub_service');
                            $credit = $order->requested_sub_service ? $order->requested_sub_service->credit : 1;
                            if (floatval($provider->credit) >= $credit) {
                                $provider->credit = floatval($provider->credit) - $credit;
                                $provider->save();

                                $this->_transaction->create(['provider_id' => $provider->id,
                                    'service_request_id' => $id,
                                    'amount' => '-' . $credit
                                ]);
                            } else {
                                return response()->json([
                                    'error' => true,
                                    'message' => "you don't have enough credit to Accept the offer"
                                ], HttpStatusCode::OK);
                            }
                        }
                        
                        break;
                }

                // isset($request->direct_contact) ? $direct_contact = $request->direct_contact : $direct_contact = null;

                // if ($request->status === 'REJECTED' &&  isset($direct_contact) {
                //     $this->_message->where('service_request_id', $id)->delete();
                // }

                $order->status = $request->status;
                $order->save();
                $user = $this->_user->whereRole(UserType::USER)->find($order->user_id);

                if ($order->status == AppConst::ACCEPTED) {
                    $this->_message->create([
                        'receiver_id' => $order->user_id,
                        'sender_id' => $provider->id,
                        'service_request_id' => $order->id,
                        'message' => 'Your order has been accepted by ' . $provider->name,
                    ]);
                    dispatch(new RequestAcceptJob($order, $user->email, $provider));
                }
                if ($order->status == AppConst::REJECTED) {
                    $refund = null;
                    try {
                        $order->load(['user', 'transaction']);
                        if ($order->user && $order->transaction && $order->transaction->status == "succeeded") {
                            $refundObj = $order->user->refund(null, ["charge" => $order->transaction->payment_id]);
                            if ($refundObj && $refundObj->status == "succeeded") {
                                $order->transaction->update(['refund_id' => $refundObj->id]);
                                $refund = true;
                            }
                        }
                    } catch (\Exception $e) {
                    }
                    $this->_message->create([
                        'receiver_id' => $order->user_id,
                        'sender_id' => $provider->id,
                        'service_request_id' => $order->id,
                        'message' => 'Your order has been rejected by ' . $provider->name,
                    ]);
                    dispatch(new RequestRejectJob($order, $user, $provider, $refund));
                }
                $payload = [
                    'title' => 'Request ' . $request->status,
                    'body' => "Provider " . $provider->first_name . " " . $request->status . " your service request",
                    'provider_id' => $provider->id,
                    'type' => 'SERVICE_REQUEST',
                    'service_request_id' => $order->id
                ];
                if ($user && isset($user->device_token)) {
                    Fcm::push_notification($payload, [$user->device_token], $user->role, $user->os_platform);
                }
                broadcast(new AlertEvent(['id' => $user->id, 'payload' => $payload]));
                $user->notify(new OrderNotification($payload));
                return response()->json([
                    'error' => false,
                    'message' => 'success',
                    'data' => $order
                ], HttpStatusCode::OK);
            }
        } catch (\EXception $e) {
            Log::error('OrderController -> acceptOrReject', [$e->getMessage()]);
            return response()->json([
                'error' => true,
                'message' => HttpStatusCode::$statusTexts[HttpStatusCode::INTERNAL_SERVER_ERROR]
            ], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Undocumented function
     *
     * @param Request $request
     * @param [type] $id
     * @return JsonResponse
     */
    public function acceptOrRejectChat(Request $request, $id)
    {
        $request->validate(['status' => 'required|in:ACCEPTED,REJECTED']);
        try {
            $chatRequest = $this->_serviceRequest->whereProvider_id(auth()->user()->id)->find($id);

            if ($chatRequest !== null) {
                switch ($chatRequest->status) {
                    case  AppConst::ACCEPTED:

                        return response()->json([
                            'error' => true,
                            'data' => $chatRequest,
                            'message' => "Offer already Accepted"
                        ], HttpStatusCode::OK);

                    case AppConst::REJECTED:

                        return response()->json([
                            'error' => true,
                            'data' => $chatRequest,
                            'message' => "Offer already Rejected"
                        ], HttpStatusCode::OK);

                    default:
                        if ($request->status === 'REJECTED') {
                            $this->_message->where('service_request_id', $id)->delete();
                        } else {
                            if ($chatRequest->is_replied !== true) {
                                $user = $request->user();
                                $chatRequest->load('requested_sub_service');
                                $credit = $chatRequest->requested_sub_service ? $chatRequest->requested_sub_service->credit : 1;
                                if (floatval($user->credit) >= $credit) {
                                    $user->credit = floatval($user->credit) - $credit;
                                    $user->save();
                                    $this->_transaction->create([
                                        'provider_id' => $user->id,
                                        'service_request_id' => $id,
                                        'amount' => '-' . $credit
                                    ]);
                                } else {
                                    return response()->json([
                                        'error' => true,
                                        'message' => "you don't have enough credit to Accept the offer"
                                    ], HttpStatusCode::OK);
                                }
                            }
                        }
                        $chatRequest->status = $request->status;
                        $chatRequest->save();

                        $user = $this->_user->find($chatRequest->user_id);
                        $payload = [
                            'title' => 'Service request status change',
                            'body' => auth()->user()->first_name . ' change to ' . $chatRequest->status,
                            'provider_id' => auth()->user()->id,
                            'service_request_id' => $chatRequest->id,
                            'type' => 'SERVICE_REQUEST'
                        ];
                        if ($user->device_token !== null) {
                            Fcm::push_notification($payload, [$user->device_token], $user->role, $user->os_platform);
                        }
                        try {
                            broadcast(new AlertEvent(['id' => $user->id, 'payload' => $payload]));
                        } catch (\Throwable $th) {
                            //throw $th;
                        }
                        $user->notify(new OrderNotification($payload));

                        return response()->json([
                            'error' => false,
                            'data' => $chatRequest,
                            'message' => HttpStatusCode::$statusTexts[HttpStatusCode::OK]
                            ],
                            HttpStatusCode::OK
                        );
                }
            } else {
                return response()->json([
                    'error' => true,
                    'message' => HttpStatusCode::$statusTexts[HttpStatusCode::NOT_FOUND]
                ], HttpStatusCode::NOT_FOUND);
            }
        } catch (\Exception $e) {
            Log::error('OrderController -> acceptOrRejectChat', [$e->getMessage()]);
            return response()->json([
                'error' => true,
                'message' => HttpStatusCode::$statusTexts[HttpStatusCode::INTERNAL_SERVER_ERROR]
            ], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Undocumented function
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function quotation(Request $request, $id)
    {
        $request->validate([
            'duration' => 'required',
            'price' => 'required|min:1|max:3',
        ]);
        try {
            $serviceRequest = $this->_serviceRequest->where('provider_id', '=', auth()->user()->id)->find($id);

            if ($serviceRequest !== null) {
                $order = $this->_serviceRequest->quotation($request->all(), $serviceRequest);
                $user = $this->_user->whereRole(UserType::PROVIDER)->find($order->user_id);

                if ($user !== null && isset($user->device_token)) {
                    Fcm::push_notification($order, [$user->device_token], $user->role, $user->os_platform);
                }
                try {
                    broadcast(new AlertEvent(['id' => $user->id, 'payload' => $order]));
                } catch (\Throwable $th) {
                    //throw $th;
                }
                $user->notify(new OrderNotification($order));
                return response()->json([
                    'error' => false,
                    'message' => HttpStatusCode::$statusTexts[HttpStatusCode::OK],
                    'data' => $order
                ], HttpStatusCode::OK);
            } else {
                return response()->json([
                    'error' => true,
                    'message' => HttpStatusCode::$statusTexts[HttpStatusCode::NOT_FOUND]
                ], HttpStatusCode::NOT_FOUND);
            }
        } catch (\Exception $e) {
            Log::error('OrderController -> quotation', [$e->getMessage()]);
            return response()->json([
                'error' => true,
                'message' => HttpStatusCode::$statusTexts[HttpStatusCode::INTERNAL_SERVER_ERROR]
            ], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Undocumented function
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|null
     */
    public function workingTime(Request $request)
    {
        $request->validate([
            'service_request_id' => 'required',
            'type' => 'required|in:start_at,end_at',
            'is_paused' => 'required|boolean',
        ]);
        try {
            $this->_serviceRequest = $this->_serviceRequest->where('provider_id', $request->user()->id)
                ->find($request->service_request_id);

            if ($this->_serviceRequest === null) {
                return response()->json([
                    'error' => true,
                    'message' => HttpStatusCode::$statusTexts[HttpStatusCode::NOT_FOUND]
                ], HttpStatusCode::NOT_FOUND);
            }

            if (intval($this->_serviceRequest->provider_id) !== $request->user()->id) {
                return response()->json([
                    'error' => true,
                    'message' => HttpStatusCode::$statusTexts[HttpStatusCode::FORBIDDEN]
                ], HttpStatusCode::FORBIDDEN);
            }

            if ($this->_serviceRequest->status === AppConst::PENDING || $this->_serviceRequest->status === AppConst::REJECTED) {
                return response()->json([
                    'error' => true,
                    'message' => 'Service Request is Pending or Rejected',
                    'data' => $this->_serviceRequest
                ], HttpStatusCode::OK);
            }

            $workedTime = $this->_workedTime->where('is_paused', false)
                ->where('service_request_id', $request->service_request_id)->first();

            if ($request->is_paused === false) {

                if ($request->type === 'start_at') {

                    if ($this->_serviceRequest->working_status == null) {
                        $newWorkedTime = $this->_workedTime->create([
                            'service_request_id' => $request->service_request_id,
                            'start_at' => Carbon::now(config('app.timezone')),
                        ]);

                        $this->_serviceRequest->working_status = WorkingStatus::STARTED;
                        $this->_serviceRequest->save();

                        $user = $this->_user->find($this->_serviceRequest->user_id);
                        $payload = [
                            'title' => 'Start working on your Service Request',
                            'body' => auth()->user()->first_name . ' working on your Service Request just started now',
                            'provider_id' => auth()->user()->id,
                            'service_request_id' => $this->_serviceRequest->id,
                            'type' => 'SERVICE_REQUEST'
                        ];
                        if ($user->device_token !== null) {
                            Fcm::push_notification($payload, [$user->device_token], $user->role, $user->os_platform);
                        }
                        try {
                            broadcast(new AlertEvent(['id' => $user->id, 'payload' => $payload]));
                        } catch (\Throwable $th) {
                            //throw $th;
                        }
                        $user->notify(new OrderNotification($payload));

                        return response()->json([
                            'error' => false,
                            'data' => $newWorkedTime,
                            'message' => HttpStatusCode::$statusTexts[HttpStatusCode::OK]
                        ], HttpStatusCode::OK);
                    } else {
                        return response()->json([
                            'error' => true,
                            'data' => $this->_serviceRequest,
                            'message' => HttpStatusCode::$statusTexts[HttpStatusCode::OK]
                        ], HttpStatusCode::OK);
                    }
                }


                if ($request->type === 'end_at') {

                    if ($this->_serviceRequest->working_status === WorkingStatus::STARTED) {


                        $lastPause = $this->_workedTime->where('is_paused', true)
                            ->where('service_request_id', $request->service_request_id)->latest()->first();

                        if ($lastPause !== null && $lastPause->end_at === null) {

                            return response()->json([
                                'error' => false,
                                'data' => $workedTime,
                                'message' => 'please start work first'
                            ], HttpStatusCode::OK);
                        } else {

                            $this->_serviceRequest->working_status = WorkingStatus::ENDED;
                            $this->_serviceRequest->save();
                            $workedTime->end_at = Carbon::now(config('app.timezone'));
                            $workedTime->save();

                            $this->_serviceRequest->is_completed = true;
                            $this->_serviceRequest->save();
                            $pausedTime = $this->_workedTime->where('is_paused', true)
                                ->where('service_request_id', $request->service_request_id)->get();

                            $totalPauseTime = 0;

                            foreach ($pausedTime as $time) {
                                $pauseStart = Carbon::parse($time->start_at);
                                $pauseEnd = Carbon::parse($time->end_at);
                                $totalPauseTime += $pauseStart->diffInMinutes($pauseEnd);
                            }
                            $start = Carbon::parse($workedTime->start_at);
                            $end = Carbon::parse($workedTime->end_at);

                            $totalWorkedTime = $start->diffInMinutes($end) - $totalPauseTime;

                            $provider = $this->_user->with('provider_profile')->find($request->user()->id);

                            $hours = 0;
                            $transaction = null;
                            if (($totalWorkedTime % 60) > 10) {
                                $hours = intdiv($totalWorkedTime, 60) + 1;
                            } else {
                                $hours = intdiv($totalWorkedTime, 60) == 0 ? 1 : intdiv($totalWorkedTime, 60);
                            }

                            $extraTime = 0;
                            if ($hours > $this->_serviceRequest->hours) {
                                $extraTime = $hours - $this->_serviceRequest->hours;
                            }

                            if (
                                // $provider->account_type === AccountType::BASIC && 
                                $provider->provider_profile->hourly_rate !== null &&
                                $this->_serviceRequest->is_quotation == 0 &&
                                $provider->service_type != ServiceType::MOVING &&
                                $extraTime > 0
                            ) {
                                $this->_serviceRequest->payable_amount = $extraTime * $provider->provider_profile->hourly_rate;
                                $this->_serviceRequest->payment_status = false;
                                $this->_serviceRequest->save();
                                $transaction = $this->_transaction->create([
                                    'service_request_id' => $this->_serviceRequest->id,
                                    'amount' => $extraTime * $provider->provider_profile->hourly_rate,
                                    'status' => AppConst::PENDING,
                                    'is_credit' => null,
                                    'user_id' => $this->_serviceRequest->user_id,
                                    'is_payable' => true
                                ]);
                                $paidAmount = intval($this->_serviceRequest->paid_amount);
                                $provider->provider_profile->total_earn =  intval($provider->provider_profile->earn) + $paidAmount;
                                $commission = $this->_commission->first();
                                $provider->provider_profile->commission = intval($provider->provider_profile->commission)  + ($paidAmount * ($commission->percentage / 100));
                                $provider->provider_profile->earn = (intval($provider->provider_profile->earn) + $paidAmount) - $provider->provider_profile->commission;
                                $this->_transaction->create([
                                    'provider_id' => $this->_serviceRequest->provider_id,
                                    'amount' => $paidAmount * ($commission->percentage / 100),
                                    'service_request_id' => $this->_serviceRequest->id,
                                    'type' => TransactionType::COMMISSION,
                                    'is_credit' => 0
                                ]);
                                $provider->push();
                            }

                            $this->_serviceRequest->worked_hours = $hours;
                            $this->_serviceRequest->save();

                            $user = $this->_user->find($this->_serviceRequest->user_id);
                            $payload = [
                                'title' => 'End working on your Service Request',
                                'body' => auth()->user()->first_name . ' end working on your Service Request just now',
                                'provider_id' => auth()->user()->id,
                                'paid_amount' => $this->_serviceRequest->paid_amount,
                                'requested_hours' => $this->_serviceRequest->hours,
                                'worked_hours' => $this->_serviceRequest->worked_hours,
                                'service_request_id' => $this->_serviceRequest->id,
                                'payable' => $transaction !== null ? $transaction : null,
                                'type' => 'SERVICE_REQUEST'
                            ];
                            if ($user->device_token !== null) {
                                Fcm::push_notification($payload, [$user->device_token], $user->role, $user->os_platform);
                            }
                            try {
                                broadcast(new AlertEvent(['id' => $user->id, 'payload' => $payload]));
                            } catch (\Throwable $th) {
                                //throw $th;
                            }
                            $user->notify(new OrderNotification($payload));

                            return response()->json([
                                'error' => false,
                                // $transaction !== null ? ['payable' => $transaction] : null,
                                'data' => $workedTime,
                                'message' => HttpStatusCode::$statusTexts[HttpStatusCode::OK]
                            ], HttpStatusCode::OK);
                        }
                    } else {
                        return response()->json([
                            'error' => true,
                            'data' => $this->_serviceRequest,
                            'message' => HttpStatusCode::$statusTexts[HttpStatusCode::OK]
                        ], HttpStatusCode::OK);
                    }
                }
            } else {
                if ($request->type === 'start_at') {

                    if ($this->_serviceRequest->working_status === WorkingStatus::STARTED) {

                        $this->_serviceRequest->working_status = WorkingStatus::PAUSED;
                        $this->_serviceRequest->save();

                        $isPause = $this->_workedTime->where('end_at', null)
                            ->where('is_paused', true)
                            ->where('service_request_id', $request->service_request_id)
                            ->first();

                        if ($this->_serviceRequest->is_paused == false) {

                            $paused = $this->_workedTime->create([
                                'service_request_id' => $request->service_request_id,
                                'is_paused' => 1,
                                'start_at' => Carbon::now(config('app.timezone'))
                            ]);

                            $user = $this->_user->find($this->_serviceRequest->user_id);
                            $payload = [
                                'title' => 'Stop working on your Service Request',
                                'body' => auth()->user()->first_name . ' stop working on your Service Request just now',
                                'provider_id' => auth()->user()->id,
                                'service_request_id' => $this->_serviceRequest->id,
                                'type' => 'SERVICE_REQUEST'
                            ];
                            if ($user->device_token !== null) {
                                Fcm::push_notification($payload, [$user->device_token], $user->role, $user->os_platform);
                            }
                            try {
                                broadcast(new AlertEvent(['id' => $user->id, 'payload' => $payload]));
                            } catch (\Throwable $th) {
                                //throw $th;
                            }
                            $user->notify(new OrderNotification($payload));

                            return response()->json([
                                'error' => false,
                                'data' => $paused,
                                'message' => HttpStatusCode::$statusTexts[HttpStatusCode::OK]
                            ], HttpStatusCode::OK);
                        } else {
                            return response()->json([
                                'error' => false,
                                'message' => 'Already paused',
                                'data' => $isPause
                            ], HttpStatusCode::OK);
                        }
                    } else {
                        return response()->json([
                            'error' => true,
                            'data' => $this->_serviceRequest,
                            'message' => HttpStatusCode::$statusTexts[HttpStatusCode::OK],
                        ], HttpStatusCode::OK);
                    }
                }

                if ($request->type === 'end_at') {

                    if ($this->_serviceRequest->working_status === WorkingStatus::PAUSED) {

                        $this->_serviceRequest->working_status = WorkingStatus::STARTED;
                        $this->_serviceRequest->save();

                        $isPause = $this->_workedTime->where('start_at', '!=', null)
                            ->where('end_at', null)
                            ->where('is_paused', true)
                            ->where('service_request_id', $request->service_request_id)
                            ->latest()
                            ->first();

                        $isPause->end_at = Carbon::now(config('app.timezone'));
                        $isPause->save();

                        $user = $this->_user->find($this->_serviceRequest->user_id);
                        $payload = [
                            'title' => 'Resume working on your Service Request',
                            'body' => auth()->user()->first_name . ' Resume working on your Service Request just now',
                            'provider_id' => auth()->user()->id,
                            'service_request_id' => $this->_serviceRequest->id,
                            'type' => 'SERVICE_REQUEST'
                        ];
                        if ($user->device_token !== null) {
                            Fcm::push_notification($payload, [$user->device_token], $user->role, $user->os_platform);
                        }
                        try {
                            broadcast(new AlertEvent(['id' => $user->id, 'payload' => $payload]));
                        } catch (\Throwable $th) {
                            //throw $th;
                        }
                        $user->notify(new OrderNotification($payload));

                        return response()->json([
                            'error' => false,
                            'data' => $isPause,
                            'message' => HttpStatusCode::$statusTexts[HttpStatusCode::OK]
                        ], HttpStatusCode::OK);
                    } else {
                        return response()->json([
                            'error' => true,
                            'data' => $this->_serviceRequest,
                            'message' => HttpStatusCode::$statusTexts[HttpStatusCode::OK],
                        ], HttpStatusCode::OK);
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error('OrderController -> workingTime', [$e->getMessage()]);
            return response()->json([
                'error' => true,
                'message' => HttpStatusCode::$statusTexts[HttpStatusCode::INTERNAL_SERVER_ERROR]
            ], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }

    public function workingStatus(Request $request)
    {
        $request->validate([
            'service_request_id' => 'required',
        ]);

        $this->_serviceRequest = $this->_serviceRequest->with('worked_times')
            ->where('provider_id', $request->user()->id)
            ->find($request->service_request_id);

        if ($this->_serviceRequest === null) {
            return response()->json([
                'error' => true,
                'message' => HttpStatusCode::$statusTexts[HttpStatusCode::NOT_FOUND]
            ], HttpStatusCode::NOT_FOUND);
        }

        return response()->json([
            'error' => false,
            'data' => $this->_serviceRequest,
            'message' => HttpStatusCode::$statusTexts[HttpStatusCode::OK]
        ], HttpStatusCode::OK);
    }
    /***************** Private functions **********************/

    private function _ownsTheRequest($provider_id, $service_request_id)
    {
        $this->_serviceRequest = $this->_serviceRequest->where('provider_id', $provider_id)
            ->find($service_request_id);

        if ($this->_serviceRequest === null) {
            return response()->json([
                'error' => true,
                'message' => HttpStatusCode::$statusTexts[HttpStatusCode::NOT_FOUND]
            ], HttpStatusCode::NOT_FOUND);
        }
    }

    private function _checkStatus($status)
    {
        if ($status === AppConst::PENDING || $status === AppConst::REJECTED) {
            return response()->json([
                'error' => true,
                'message' => "Service Request is Pending or Rejected",
                'data' => $this->_serviceRequest
            ], HttpStatusCode::OK);
        }
    }

    private function _handleStartOrEndService($type, $service_request_id, $workedTime, $providerId)
    {
        if ($type === 'start_at') {
            $this->_handleStartService($service_request_id);
        }
        if ($type === 'end_at') {
            $this->_handleEndService($service_request_id, $workedTime, $providerId);
        }
    }

    private function _handleStartService($service_request_id)
    {
        if ($this->_serviceRequest->working_status == null) {
            $newWorkedTime = $this->_workedTime->create([
                'service_request_id' => $service_request_id,
                'start_at' => Carbon::now(config('app.timezone'))
            ]);

            $this->_serviceRequest->working_status = WorkingStatus::STARTED;
            $this->_serviceRequest->save();

            $user = $this->_user->find($this->_serviceRequest->user_id);
            $payload = [
                'title' => 'Start working on your Service Request',
                'body' => auth()->user()->first_name . ' working on your Service Request just started now',
                'provider_id' => auth()->user()->id,
                'service_request_id' => $this->_serviceRequest->id,
                'type' => 'SERVICE_REQUEST'
            ];
            if ($user->device_token !== null) {
                Fcm::push_notification($payload, [$user->device_token], $user->role, $user->os_platform);
            }
            try {
                broadcast(new AlertEvent(['id' => $user->id, 'payload' => $payload]));
            } catch (\Throwable $th) {
                //throw $th;
            }
            $user->notify(new OrderNotification($payload));

            return response()->json([
                'error' => false,
                'data' => $newWorkedTime,
                'message' => HttpStatusCode::$statusTexts[HttpStatusCode::OK]
            ], HttpStatusCode::OK);
        } else {
            return response()->json([
                'error' => true,
                'data' => $this->_serviceRequest,
                'message' => HttpStatusCode::$statusTexts[HttpStatusCode::OK]
            ], HttpStatusCode::OK);
        }
    }

    private function _handleEndService($service_request_id, $workedTime, $providerId)
    {
        if ($this->_serviceRequest->working_status === WorkingStatus::STARTED) {
            $lastPause = $this->_workedTime->query()
                ->where('is_paused', true)
                ->where('service_request_id', $service_request_id)
                ->latest()
                ->first();

            if ($lastPause !== null && $lastPause->end_at === null) {

                return response()->json([
                    'error' => false,
                    'data' => $workedTime,
                    'message' => 'please start work first'
                ], HttpStatusCode::OK);
            } else {

                $this->_serviceRequest->working_status = WorkingStatus::ENDED;
                $this->_serviceRequest->save();
                $workedTime->end_at = Carbon::now(config('app.timezone'));
                $workedTime->save();

                $this->_serviceRequest->is_completed = true;
                $this->_serviceRequest->save();
                $pausedTime = $this->_workedTime->where('is_paused', true)
                ->where('service_request_id', $service_request_id)->get();

                $totalPauseTime = 0;

                foreach ($pausedTime as $time) {
                    $pauseStart = Carbon::parse($time->start_at);
                    $pauseEnd = Carbon::parse($time->end_at);
                    $totalPauseTime += $pauseStart->diffInMinutes($pauseEnd);
                }
                $start = Carbon::parse($workedTime->start_at);
                $end = Carbon::parse($workedTime->end_at);

                $totalWorkedTime = $start->diffInMinutes($end) - $totalPauseTime;

                $provider = $this->_user->with('provider_profile')->find($providerId);

                $hours = 0;
                $transaction = null;
                if (($totalWorkedTime % 60) > 10) {
                    $hours = intdiv($totalWorkedTime, 60) + 1;
                } else {
                    $hours = intdiv($totalWorkedTime, 60) == 0 ? 1 : intdiv($totalWorkedTime, 60);
                }

                $extraTime = 0;
                if ($hours > $this->_serviceRequest->hours) {
                    $extraTime = $hours - $this->_serviceRequest->hours;
                }

                if (
                    // $provider->account_type === AccountType::BASIC && 
                    $provider->provider_profile->hourly_rate !== null &&
                    $this->_serviceRequest->is_quotation == 0 &&
                    $provider->service_type != ServiceType::MOVING &&
                    $extraTime > 0
                ) {
                    $this->_serviceRequest->payable_amount = $extraTime * $provider->provider_profile->hourly_rate;
                    $this->_serviceRequest->payment_status = false;
                    $this->_serviceRequest->save();
                    $transaction = $this->_transaction->create([
                        'service_request_id' => $this->_serviceRequest->id,
                        'amount' => $extraTime * $provider->provider_profile->hourly_rate,
                        'status' => AppConst::PENDING,
                        'is_credit' => null,
                        'user_id' => $this->_serviceRequest->user_id,
                        'is_payable' => true
                    ]);
                    $paidAmount = intval($this->_serviceRequest->paid_amount);
                    $provider->provider_profile->total_earn =  intval($provider->provider_profile->earn) + $paidAmount;
                    $commission = $this->_commission->first();
                    $provider->provider_profile->commission = intval($provider->provider_profile->commission)  + ($paidAmount * ($commission->percentage / 100));
                    $provider->provider_profile->earn = (intval($provider->provider_profile->earn) + $paidAmount) - $provider->provider_profile->commission;
                    $this->_transaction->create([
                        'provider_id' => $this->_serviceRequest->provider_id,
                        'amount' => $paidAmount * ($commission->percentage / 100),
                        'service_request_id' => $this->_serviceRequest->id,
                        'type' => TransactionType::COMMISSION,
                        'is_credit' => 0
                    ]);
                    $provider->push();
                }

                $this->_serviceRequest->worked_hours = $hours;
                $this->_serviceRequest->save();

                $user = $this->_user->find($this->_serviceRequest->user_id);
                $payload = [
                    'title' => 'End working on your Service Request',
                    'body' => auth()->user()->first_name . ' end working on your Service Request just now',
                    'provider_id' => auth()->user()->id,
                    'paid_amount' => $this->_serviceRequest->paid_amount,
                    'requested_hours' => $this->_serviceRequest->hours,
                    'worked_hours' => $this->_serviceRequest->worked_hours,
                    'service_request_id' => $this->_serviceRequest->id,
                    'payable' => $transaction !== null ? $transaction : null,
                    'type' => 'SERVICE_REQUEST'
                ];
                if ($user->device_token !== null) {
                    Fcm::push_notification($payload, [$user->device_token], $user->role, $user->os_platform);
                }
                try {
                    broadcast(new AlertEvent(['id' => $user->id, 'payload' => $payload]));
                } catch (\Throwable $th) {
                    //throw $th;
                }
                $user->notify(new OrderNotification($payload));

                return response()->json([
                    'error' => false,
                    // $transaction !== null ? ['payable' => $transaction] : null,
                    'data' => $workedTime,
                    'message' => HttpStatusCode::$statusTexts[HttpStatusCode::OK]
                ], HttpStatusCode::OK);
            }
        } else {
            return response()->json([
                'error' => true,
                'data' => $this->_serviceRequest,
                'message' => HttpStatusCode::$statusTexts[HttpStatusCode::OK]
            ], HttpStatusCode::OK);
        }
    }
}
