<?php

namespace App\Http\Controllers\Api\User;

use App\Utils\AppConst;
use Illuminate\Http\Request;
use App\Utils\HttpStatusCode;
use App\Http\Controllers\Controller;
use App\Http\Resources\UsersPlansResource;

class PlanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse|UsersPlansResource
     */
    public function index(Request $request)
    {
        $plans = $request->user()->hourly_subscriptions()->latest()->with([
            'plan:id,title,description',
            'subscription_histories',
            'service_request' => fn ($query) => $query->select([
                'id',
                'provider_id',
                'sub_service_id',
                'is_completed',
                'working_status',
                'paid_amount',
                'status',
            ])->with(['provider:id,first_name,last_name,image', 'requested_sub_service:id,name'])
        ])->paginate(10);
        if ($plans->isNotEmpty()) return new UsersPlansResource($plans);
        return $this->error('No plans found', HttpStatusCode::NOT_FOUND);
    }

    public function cancel(Request $request, $plan)
    {
        try {
            $subscription = $request->user()->hourly_subscriptions()->active()->find($plan);
            if ($subscription) {
                $subscription->update(['status' => AppConst::CANCEL]);
                $subscription->future_subscriptions()->update(['status' => AppConst::CANCEL]);
                return $this->success($subscription, 'Plan cancelled successfully');
            }
            return $this->error('Plan not found or already cancelled', HttpStatusCode::NOT_FOUND);
        } catch (\Exception $e) {
            $this->make_log(__METHOD__, $e);
            return $this->error(HttpStatusCode::$statusTexts[HttpStatusCode::INTERNAL_SERVER_ERROR], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    // public function store(Request $request)
    // {
    //     //
    // }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // public function show($id)
    // {
    //     //
    // }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // public function update(Request $request, $id)
    // {
    //     //
    // }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // public function destroy($id)
    // {
    //     //
    // }
}
