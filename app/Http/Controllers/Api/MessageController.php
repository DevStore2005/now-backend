<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Message;
use App\Utils\AppConst;
use App\Utils\UserType;
use App\Http\Helpers\Fcm;
use App\Models\Transaction;
use Illuminate\Support\Arr;
use App\Events\MessageEvent;
use Illuminate\Http\Request;
use App\Utils\HttpStatusCode;
use App\Models\ServiceRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Notifications\MessageNotification;

class MessageController extends Controller
{
    /**
     *  @var \App\Models\User $_user 
     *  @var \App\Models\Message $_message 
     *  @var \App\Models\Transaction $_transaction
     *  @var \App\Models\ServiceRequest $_serviceRequest
     *  @var \App\Http\Helpers\Fcm $_fcm
     */
    private $_user, $_message, $_transaction, $_serviceRequest, $_fcm;

    /**
     * Create a new controller instance.
     * @param  \App\Models\User  $user
     * @param  \App\Models\Service $service
     * @return void
     */
    public function __construct(Message $message, User $user, Fcm $fcm, ServiceRequest $serviceRequest, Transaction $transaction)
    {
        $this->_message = $message;
        $this->_user = $user;
        $this->_fcm = $fcm;
        $this->_serviceRequest = $serviceRequest;
        $this->_transaction = $transaction;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            // $users = $this->_message->where('sender_id', $request->user()->id)
            // ->orWhere('receiver_id', $request->user()->id)
            // ->latest()
            // ->with('receiver', 'sender')
            // ->get()
            // ->unique('sender_id')
            // ->values();
            // $users = $this->_user->
            // orderByDesc(
            //     $this->_message->where('sender_id', auth()->user()->id)
            //         ->orderByDesc('created_at')
            //         ->limit(1)
            // )->
            // paginate(5);
            $users = $this->_user->has('sender_message')->orHas('receiver_message')->whereHas('sender_message', function ($q) {
                return $q->Where('sender_id', '=', auth()->user()->id);
            })->WhereHas('receiver_message', function ($q) {
                return $q->Where('receiver_id', '=', auth()->user()->id);
            })->with('sender_message', 'receiver_message')->paginate(20);
            if ($users->count() > 0) {
                return response()->json([
                    'error' => false,
                    'data' => $users,
                    'message' => HttpStatusCode::$statusTexts[HttpStatusCode::OK]
                ], HttpStatusCode::OK);
            }
            return response()->json([
                'error' => true,
                'message' => HttpStatusCode::$statusTexts[HttpStatusCode::NOT_FOUND]
            ], HttpStatusCode::NOT_FOUND);
        } catch (\Exception $e) {
            Log::error('MessageController -> index ', [$e->getMessage()]);
            return response()->json([
                'error' => true,
                'message' => HttpStatusCode::$statusTexts[HttpStatusCode::INTERNAL_SERVER_ERROR]
            ], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * get chat with spescific users or provider
     *
     * @param int $id
     * @return JsonResponse
     */
    public function chat(Request $request, $id)
    {
        try {
            $messages = $this->_message->where(function ($query) use ($id) {
                return $query->Where(function ($query) use ($id) {
                    return $query->where('sender_id', '=', auth()->user()->id)
                        ->Where('receiver_id', '=', $id);
                })->orWhere(function ($query) use ($id) {
                    return $query->where('sender_id', '=', $id)
                        ->Where('receiver_id', '=', auth()->user()->id);
                });
            })->when(isset($request->service_request_id) == true, function ($query) use ($request) {
                return $query->where('service_request_id', $request->service_request_id);
            })->with([
                'sender' => function ($q) {
                    $q->select('id', 'first_name', 'last_name', 'image');
                }, 'receiver' => function ($q) {
                    $q->select('id', 'first_name', 'last_name', 'image');
                }
            ])->latest()->paginate(20);

            if ($messages->isEmpty() === false) {
                return response()->json([
                    'error' => false,
                    'data' => $messages,
                    'message' => HttpStatusCode::$statusTexts[HttpStatusCode::OK]
                ], HttpStatusCode::OK);
            } else {
                return response()->json([
                    'error' => true,
                    'message' => HttpStatusCode::$statusTexts[HttpStatusCode::NOT_FOUND]
                ], HttpStatusCode::NOT_FOUND);
            }
        } catch (\Exception $e) {
            Log::error('MessageController -> chat ', [$e->getMessage()]);
            return response()->json([
                'error' => true,
                'message' => HttpStatusCode::$statusTexts[HttpStatusCode::INTERNAL_SERVER_ERROR]
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
            'service_request_id' => 'required_without:is_admin',
            'is_admin' => 'required_without:service_request_id|boolean',
            'receiver_id' => 'required',
            'message' => 'required',
        ]);
        
        try {
            if($request->is_admin == true){
                $message = [
                    'sender_id' => auth()->user()->id,
                    'receiver_id' => $request->receiver_id,
                    'message' => $request->message,
                    'is_admin' => $request->is_admin,
                ];
            } else {
                if ($request->sender_id === $request->receiver_id) {
                    return response()->json([
                        'error' => true,
                        'message' => "you can't send message to yourself"
                    ], HttpStatusCode::NOT_FOUND);
                }
                $this->_serviceRequest = $this->_serviceRequest->where('is_replied', false)
                    ->find($request->service_request_id);
    
                $this->_user = $request->user();
                if ($this->_user->role === UserType::PROVIDER && $this->_serviceRequest !== null && $this->_serviceRequest->status === AppConst::PENDING) {
                    $this->_serviceRequest->load('requested_sub_service');
                    $credit = $this->_serviceRequest->requested_sub_service ? $this->_serviceRequest->requested_sub_service->credit : 1;
                    if (floatval($this->_user->credit) > $credit) {
                        $this->_user->credit = floatval($this->_user->credit) - $credit;
                        $this->_user->save();
    
                        $this->_transaction->create([
                            'provider_id' => $this->_user->id,
                            'service_request_id' => $this->_serviceRequest->id,
                            'amount' => '-' . $credit
                        ]);
    
                        $this->_serviceRequest->is_replied = true;
                        $this->_serviceRequest->save();
                    } else {
                        return response()->json([
                            'error' => true,
                            'message' => "you don't have enough credit to Accept the offer"
                        ], HttpStatusCode::OK);
                    }
                }
                $message =[
                    'service_request_id' => $request->service_request_id,
                    'sender_id' => auth()->user()->id,
                    'receiver_id' => intval($request->receiver_id),
                    'message' => $request->message
                ];
            }

            $message = $this->_message->create($message);

            $message->load('sender', 'receiver');
            broadcast(new MessageEvent($message));

            $message['type'] = 'MESSAGE';
            $message['title'] = 'New message received';
            $message['body'] = "New message from " . auth()->user()->first_name;


            $receiver = $this->_user->find($request->receiver_id);
            $receiver->notify(new MessageNotification($message));
            if (isset($receiver->device_token)) {
                Fcm::push_notification($message, [$receiver->device_token], $receiver->role, $receiver->os_platform);
            }
            return response()->json([
                'data' => Arr::except($message, ['type', 'title', 'body']),
                'error' => false,
                'message' => 'success'
            ], HttpStatusCode::OK);
        } catch (\Exception $e) {
            Log::error('MessageController -> store ', [$e->getMessage()]);
            return response()->json([
                'error' => true,
                'message' => HttpStatusCode::$statusTexts[HttpStatusCode::INTERNAL_SERVER_ERROR]
            ], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }



    public function activeOrderChat(Request $request)
    {
        // try {
        $userRole = $request->user()->role == UserType::USER ? 'user_id' : 'provider_id';
        $user = $request->user()->role == UserType::USER ? 'provider' : 'user';
        $serviceRequest = $this->_serviceRequest->has($user)->with($user)
            ->leftJoin('messages', 'service_requests.id', 'messages.service_request_id')
            ->where($userRole, auth()->user()->id)
            ->select(['service_requests.*'])
            ->has('message')->with('message')
            ->orderByDesc('messages.created_at')
            // ->groupBy('service_requests.id')
            ->get()->unique('id')->values();

        $messages = $this->_message->with(['sender', 'receiver'])->where('is_admin')->where(function($qry){
            return $qry->where('sender_id', auth()->user()->id)
                    ->orWhere('receiver_id', auth()->user()->id);
        })->get()->unique('id')->values();

        if ($serviceRequest !== null) {
            return response()->json([
                'error' => false,
                'messages' => $messages,
                'data' => $serviceRequest,
                'admin' => $this->_user->where('role', UserType::ADMIN)->first(),
                'message' => 'success'
            ], HttpStatusCode::OK);
        }
        return response()->json([
            'error' => true,
            'message' => HttpStatusCode::$statusTexts[HttpStatusCode::NOT_FOUND]
        ], HttpStatusCode::NOT_FOUND);
        // } catch (\Exception $e) {
        //     Log::error(['MessageController -> activeOrderChat ', $e->getMessage()]);
        //     return response()->json([
        //         'error' => true,
        //         'message' => HttpStatusCode::$statusTexts[HttpStatusCode::INTERNAL_SERVER_ERROR]
        //     ], HttpStatusCode::INTERNAL_SERVER_ERROR);
        // }
    }

    /**
     * Display the specified resource.
     *
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
     * @return \Illuminate\Http\Response|null
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response|null
     */
    public function destroy($id)
    {
        //
    }
}
