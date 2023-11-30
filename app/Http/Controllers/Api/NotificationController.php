<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Utils\AppConst;
use Illuminate\Http\Request;
use App\Utils\HttpStatusCode;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;
use App\Http\Resources\NotificationsResource;
use Illuminate\Notifications\Notification;

class NotificationController extends Controller
{

    /**
     *  @param Request $_request
     *  @var \App\Models\User $_user 
     *  @var Notification $_notification 
     *  @var string $_environment
     */
    private $_request, $_user, $_notification, $_environment;


    /**
     * Create a new controller instance.
     * @param Request $request
     * @param  \App\Models\User $user
     * @param  Notification $notification
     * @param  App $app
     * @return void
     */
    public function __construct(Request $request, User $user, Notification $notification, App $app)
    {
        $this->_request = $request;
        $this->_user = $user;
        $this->_notification = $notification;
        $this->_environment = $app::environment();
    }

    /**
     * Display a listing of the resource.
     *
     * @return NotificationsResource
     */
    public function index()
    {
        $notifications = $this->_request->user()->notifications()->paginate(AppConst::PAGE_SIZE - 80);
        return new NotificationsResource($notifications);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response|null
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response|null
     */
    public function show(Notification $notification)
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
    public function update(Request $request, Notification $notification)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response|null
     */
    public function destroy(Notification $notification)
    {
        //
    }
}
