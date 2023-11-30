<?php

namespace App\Http\Controllers\Api\Provider;

use App\Utils\MyAppEnv;
use App\Http\Helpers\Common;
use Illuminate\Http\Request;
use App\Models\PaymentMethod;
use App\Utils\HttpStatusCode;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;

class PaymentMethodController extends Controller
{
    /**
     *  @var PaymentMethod $_paymentMethod
     *  @var Common $_common
     *  @var string $_environment
     * 
     */
    private $_paymentMethod, $_common, $_environment;

    /**
     * Create a new controller instance.
     * @param  PaymentMethod $paymentMethod
     * @param  Common $common
     * @param  App  $app
     *
     * @return void
     */
    public function __construct(PaymentMethod $paymentMethod, Common $common, App $app)
    {
        $this->_paymentMethod = $paymentMethod;
        $this->_common = $common;
        $this->_environment = $app::environment();
    }

    /**
     * Display a listing of the resource.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse|null
     */
    public function index(Request $request)
    {
        try {
            // $user = $request->user();
            $paymentMethod = $this->_paymentMethod
                // ->with(['providers' => function ($qry) use ($user) {
                //     return $qry->where('provider_id', $user->id)->exists();
                // }])
                // ->WhereHas('providers', function ($qry) use ($user) {
                //     return $qry->orWhere('provider_id', $user->id);
                // })
                ->get();
            // $paymentMethod = $request->user()->payment_methods()->get();
            if ($paymentMethod->isEmpty()) {
                return $this->error('No PaymentMethod found!', HttpStatusCode::NOT_FOUND);
            }
            return $this->success($paymentMethod, 'PaymentMethod retrieved successfully!');
        } catch (\Exception $e) {
            $this->make_log(__METHOD__, $e, 'error');
            return $this->error($this->_environment === MyAppEnv::LOCAL ? $e->getMessage() : 'something went wrong!');
        }
    }


    /**
     * toggle PaymentMethod.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse|null
     */
    public function toggle(Request $request)
    {
        $request->validate([
            'payment_method_id' => 'required|integer|exists:payment_methods,id',
        ]);
        try {
            $provider = $request->user();
            $paymentMethod = $this->_paymentMethod->find($request->payment_method_id);
            if ($provider->payment_methods()->where('payment_method_id', $paymentMethod->id)->exists()) {
                $provider->payment_methods()->detach($paymentMethod->id);
                return $this->success($paymentMethod, 'PaymentMethod removed successfully!');
            }
            $provider->payment_methods()->attach($paymentMethod->id);
            return $this->success($paymentMethod, 'PaymentMethod added successfully!');
        } catch (\Exception $e) {
            $this->make_log(__METHOD__, $e, 'error');
            return $this->error($this->_environment === MyAppEnv::LOCAL ? $e->getMessage() : 'something went wrong!');
        }
    }
}
