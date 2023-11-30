<?php

namespace App\Http\Controllers\Api;

use App\Models\Subscribers;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Utils\HttpStatusCode;

class SubscribersController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email:rfc,dns|unique:subscribers,email',
        ]);
        try {
            $subscribers = Subscribers::create($request->all('email'));
            if ($subscribers) return $this->success($subscribers, 'Subscribed successfully');
            return $this->error('Something went wrong', HttpStatusCode::INTERNAL_SERVER_ERROR);
        } catch (\Exception $e) {
            $this->make_log(__METHOD__, $e);
            return $this->error('Something went wrong', HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Subscribers  $subscribers
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Subscribers $subscribers)
    {
        $subscribers->delete();
        return $this->success($subscribers, 'Unsubscribed successfully');
    }
}
