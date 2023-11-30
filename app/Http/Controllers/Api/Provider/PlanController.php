<?php

namespace App\Http\Controllers\Api\Provider;

use Exception;
use App\Models\Plan;
use Illuminate\Http\Request;
use App\Utils\HttpStatusCode;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;
use App\Models\ProvidersSubscription;
use App\Http\Requests\ProviderPlanStoreRequest;
use App\Http\Requests\ProviderPlanUpdateRequest;
use App\Http\Resources\UserSubscriptionsResource;

class PlanController extends Controller
{
    /**
     *  @var Plan $_plan
     *  @var ProvidersSubscription $_providersSubscription
     *  @var string $_environment
     *  @access private
     */
    private $_plan, $_environment, $_providersSubscription;

    /**
     * Create a new controller instance.
     * @param  Plan $plan
     * @param  ProvidersSubscription $providersSubscription
     * @param  App  $app
     *
     * @return void
     */
    public function __construct(Plan $plan, App $app, ProvidersSubscription $providersSubscription)
    {
        $this->_plan = $plan;
        $this->_providersSubscription = $providersSubscription;
        $this->_environment = $app::environment();
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $plans = $request->user()->plans()->get();
            if ($plans->isNotEmpty())
                return $this->success($plans, 'Plans retrieved successfully');
            else
                return $this->error('No plans found', HttpStatusCode::NOT_FOUND);
        } catch (Exception $e) {
            $this->make_log(__METHOD__, $e, 'error');
            return $this->error(
                'Something went wrong',
                HttpStatusCode::INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Store a newly created resource in storage.
     * @param  ProviderPlanStoreRequest $request
     *
     * @return JsonResponse|UserSubscriptionsResource
     */
    public function usersSubscriptions(Request $request)
    {
        $user = $request->user();
        try {
            $providersSubscription = $this->_providersSubscription->whereHas('plan', function ($qry) use ($user) {
                return $qry->whereProvider_id($user->id);
            })
                ->with(['user', 'service_request', 'subscription_histories'])
                ->paginate()
                ->withPath("")
                ->withQueryString();
            if ($providersSubscription->isNotEmpty()) {
                return new UserSubscriptionsResource($providersSubscription);
            }
            return $this->error('Not have any subscription', HttpStatusCode::NOT_FOUND);
        } catch (Exception $e) {
            $this->make_log(__METHOD__, $e);
            return $this->error(
                'Something went wrong',
                HttpStatusCode::INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  ProviderPlanStoreRequest  $request
     * @return \Illuminate\Http\Response|null
     */
    public function store(ProviderPlanStoreRequest $request): JsonResponse
    {
        try {
            $plan = $request->user()->plans()->updateOrCreate($request->validated());
            if ($plan)
                return $this->success(
                    $plan,
                    'Plan created successfully',
                    HttpStatusCode::CREATED
                );
            else
                return $this->error(
                    'Conflict while creating plan',
                    HttpStatusCode::CONFLICT
                );
        } catch (Exception $e) {
            $this->make_log(__METHOD__, $e, 'error');
            return $this->error(
                'Something went wrong',
                HttpStatusCode::INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Plan  $plan
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Plan $plan): JsonResponse
    {
        try {
            return $this->success($plan, 'Plan retrieved successfully');
        } catch (Exception $e) {
            $this->make_log(__METHOD__, $e, 'error');
            return $this->error('Something went wrong', HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  ProviderPlanUpdateRequest  $request
     * @param  \App\Models\Plan  $plan
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(ProviderPlanUpdateRequest $request, Plan $plan): JsonResponse
    {
        try {
            $plan->update($request->validated());
            if ($plan)
                return $this->success(
                    $plan,
                    'Plan updated successfully'
                );
            else
                return $this->error(
                    'Conflict while updating plan',
                    HttpStatusCode::CONFLICT
                );
        } catch (Exception $e) {
            $this->make_log(__METHOD__, $e, 'error');
            return $this->error(
                'Something went wrong',
                HttpStatusCode::INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Plan  $plan
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Plan $plan)
    {
        try {
            if ($plan->delete())
                return $this->success(
                    $plan,
                    'Plan deleted successfully'
                );
            else
                return $this->error(
                    'Conflict while deleting plan',
                    HttpStatusCode::CONFLICT
                );
        } catch (Exception $e) {
            $this->make_log(__METHOD__, $e, 'error');
            return $this->error(
                'Something went wrong',
                HttpStatusCode::INTERNAL_SERVER_ERROR
            );
        }
    }
}
