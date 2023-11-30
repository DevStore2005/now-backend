<?php

namespace App\Http\Controllers\Admin;

use App\Utils\MyAppEnv;
use Cookie;
use Illuminate\View\View;
use App\Http\Helpers\Common;
use Illuminate\Http\Request;
use App\Models\PaymentMethod;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;
use App\Http\Requests\PaymentMethodStoreRequest;
use Illuminate\Http\Response;
class PaymentMethodController extends Controller
{

    /**
     * @var PaymentMethod $_paymentMethod
     * @var Common $_common
     * @var string $_environment
     *
     */
    private $_paymentMethod, $_common, $_environment;

    /**
     * Create a new controller instance.
     * @param PaymentMethod $paymentMethod
     * @param Common $common
     * @param App $app
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
     *
     * @return \Illuminate\Http\Response|View
     */
    public function index(Request $request)
    {
        try {
            if ($request->query('locale') == 'all' || $request->query('locale') == null) {
                $paymentMethods = $this->_paymentMethod->get();
                return view('admin.payment_methods.index', compact('paymentMethods'));
            } else {
                $paymentMethods = $this->_paymentMethod->where('country_id', $request->default_country->id)->get();
                return view('admin.payment_methods.index', compact('paymentMethods'));
            }
        } catch (\Exception $e) {
            $this->make_log(__METHOD__, $e, 'error');
            return redirect()
                ->route('admin.dashboard')
                ->with('error_message', $this->_environment === MyAppEnv::LOCAL ? $e->getMessage() : 'something went wrong!');
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param PaymentMethodStoreRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(PaymentMethodStoreRequest $request)
    {
        try {
            $data = $request->validated();
            $url = $this->_common->store_media($data['icon'], 'icons');
            $data['icon'] = $url;
            $data['country_id'] = isset($request['default_country']) ? $request['default_country']['id'] : null;
            $payment = $this->_paymentMethod->create($data);
            if ($payment) {
                return redirect()
                    ->back()
                    ->with('success_message', 'Payment method created successfully!');
            }
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
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\PaymentMethod $paymentMethod
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, PaymentMethod $paymentMethod)
    {
        $request->validate([
            'name' => 'required|unique:payment_methods,name,' . $paymentMethod->id,
            'icon' => 'image|mimes:jpeg,png,jpg,svg|max:2048'
        ]);
        try {
            $data = $request->all(['name', 'icon']);
            if ($data['icon']) {
                if ($paymentMethod->icon) {
                    $this->_common->delete_media($paymentMethod->icon);
                }
                $url = $this->_common->store_media($data['icon'], 'icons');
                $paymentMethod->icon = $url;
            }
            $paymentMethod->country_id = isset($request['default_country']) ? $request['default_country']['id'] : $paymentMethod->country_id;
            $paymentMethod->name = $data['name'];
            $payment = $paymentMethod->save();
            if ($payment) {
                return redirect()
                    ->back()
                    ->with('success_message', 'Payment method updated successfully!');
            }
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
     * @param \App\Models\PaymentMethod $paymentMethod
     * @return \Illuminate\Http\Response
     */
    public function destroy(PaymentMethod $paymentMethod)
    {
        try {
            if ($paymentMethod->icon) {
                $this->_common->delete_media($paymentMethod->icon);
            }
            $paymentMethod->delete();
            return redirect()
                ->back()
                ->with('success_message', 'Payment method deleted successfully!');
        } catch (\Exception $e) {
            $this->make_log(__METHOD__, $e, 'error');
            return redirect()
                ->back()
                ->with('error_message', $this->_environment === MyAppEnv::LOCAL ? $e->getMessage() : 'something went wrong!');
        }
    }
}
