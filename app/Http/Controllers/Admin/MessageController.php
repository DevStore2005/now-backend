<?php

namespace App\Http\Controllers\Admin;

use App\Models\Message;
use Illuminate\View\View;
use App\Events\MessageEvent;
use Illuminate\Http\Request;
use App\Utils\HttpStatusCode;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Resources\ChatResource;

class MessageController extends Controller
{
    /**
     * private variable
     * @var Message $_message
     *
     * @access private
     */
    private $_message;

    /**
     * Create a new controller instance.
     * 
     * @param Message $message
     * @return void
     */
    public function __construct(Message $message)
    {
        $this->_message = $message;
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response|View
     */
    public function index()
    {
        $list = $this->_message->with(['sender', 'receiver'])->where('is_admin', true)->where(function($qry){
            return $qry->where('sender_id', auth()->user()->id)
                    ->orWhere('receiver_id', auth()->user()->id);
        })->has('sender')->has('receiver')->latest()->get()->unique('sender_id')->values();
        return view('admin.chats.index', compact('list'));
    }

    /**
     * get chat with spescific users or provider
     *
     * @param int $id, Request $request
     * @return JsonResponse|ChatResource
     */
    public function chat(Request $request, $id)
    {
        try {
            $messages = $this->_message->where('is_admin', true)->where(function ($query) use ($id) {
                return $query->Where(function ($query) use ($id) {
                    return $query->where('sender_id', '=', auth()->user()->id)
                        ->Where('receiver_id', '=', $id);
                })->orWhere(function ($query) use ($id) {
                    return $query->where('sender_id', '=', $id)
                        ->Where('receiver_id', '=', auth()->user()->id);
                });
            })->with([
                'sender' => function ($q) {
                    return $q->select('id', 'first_name', 'last_name', 'image');
                }, 'receiver' => function ($q) {
                    return $q->select('id', 'first_name', 'last_name', 'image');
                }
            ])->latest()->paginate(30);

            if ($messages->isEmpty() === false) {
                return new ChatResource($messages);
            } else {
                return response()->json([
                    'message' => "Not found messages"
                ], HttpStatusCode::NOT_FOUND);
            }
        } catch (\Exception $e) {
            $this->make_log(__METHOD__, $e, 'error');
            return response()->json([
                'message' => HttpStatusCode::$statusTexts[HttpStatusCode::INTERNAL_SERVER_ERROR]
            ], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response|null
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $message = $this->_message->create([
            'sender_id' => auth()->user()->id,
            'receiver_id' => $request->receiver_id,
            'message' => $request->message,
            'is_admin' => true,
        ]);

        broadcast(new MessageEvent($message));

        return response()->json([
            'status' => 'success',
            'message' => 'Message sent successfully'
        ], HttpStatusCode::OK);
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
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response|null
     */
    public function edit($id)
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
     * @param  int  $id
     * @return \Illuminate\Http\Response|null
     */
    public function destroy($id)
    {
        //
    }
}
