<?php

namespace App\Http\Controllers\Admin;

use App\Models\Plan;
use App\Utils\MyAppEnv;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Requests\PlanStoreRequest;
use App\Http\Requests\PlanUpdateRequest;

class PlanController extends Controller
{

    /**
     *  @var Plan $_plan
     *  @var string $_environment
     * 
     */
    private $_plan, $_environment;

    /**
     * Create a new controller instance.
     * @param  Plan $plan
     * @param  App  $app
     *
     * @return void
     */
    public function __construct(Plan $plan, App $app)
    {
        $this->_plan = $plan;
        $this->_environment = $app::environment();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response|View
     */
    public function index()
    {
        try {
            $data = $this->_plan->whereNull('Provider_id')->get();
            return view('admin.plans.index', compact('data'));
        } catch (\Exception $e) {
            $this->make_log(__METHOD__, $e, 'error');
            return redirect()
                ->back()
                ->with('error_message', $this->_environment === MyAppEnv::LOCAL ? $e->getMessage() : 'something went wrong!');
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  PlanStoreRequest  $request
     * @return \Illuminate\Http\Response|null
     */
    public function store(PlanStoreRequest $request)
    {
        try {
            $plan = $this->_plan->create($request->validated());
            if ($plan)
                return redirect()
                    ->back()
                    ->with('success_message', 'Plan created successfully!');
            else
                return redirect()
                    ->back()
                    ->with('error_message', 'something went wrong!');
        } catch (\Exception $e) {
            $this->make_log(__METHOD__, $e, 'error');
            return redirect()
                ->back()
                ->with('error_message', $this->_environment === MyAppEnv::LOCAL ? $e->getMessage() : 'something went wrong!');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  PlanUpdateRequest  $request
     * @param  Plan  $plan
     * @return \Illuminate\Http\Response|null
     */
    public function update(PlanUpdateRequest $request, Plan $plan)
    {
        try {
            $plan = $plan->update($request->validated());
            if ($plan)
                return redirect()
                    ->back()
                    ->with('success_message', 'Plan updated successfully!');
            else
                return redirect()
                    ->back()
                    ->with('error_message', 'something went wrong!');
        } catch (\Exception $e) {
            $this->make_log(__METHOD__, $e, 'error');
            return redirect()
                ->back()
                ->with('error_message', $this->_environment === MyAppEnv::LOCAL ? $e->getMessage() : 'something went wrong!');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Plan  $plan
     * @return \Illuminate\Http\Response|null
     */
    public function destroy(Plan $plan)
    {
        try {
            $plan = $plan->delete();
            if ($plan)
                return redirect()
                    ->back()
                    ->with('success_message', 'Plan deleted successfully!');
            else
                return redirect()
                    ->back()
                    ->with('error_message', 'something went wrong!');
        } catch (\Exception $e) {
            $this->make_log(__METHOD__, $e, 'error');
            return redirect()
                ->back()
                ->with('error_message', $this->_environment === MyAppEnv::LOCAL ? $e->getMessage() : 'something went wrong!');
        }
    }
}
