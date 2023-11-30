<?php

namespace App\Http\Controllers\Admin;

use App\Utils\MyAppEnv;
use App\Models\Currency;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class CurrencyController extends Controller
{

    /**
     *  @var string $_environment
     *  @var Currency $_currency
     */
    private $_currency, $_environment;


    /**
     * Create a new controller instance.
     * @param  App $app
     * @return Currency $currency
     * @return void
     */
    public function __construct(Currency $currency, App $app)
    {
        $this->_currency = $currency;
        $this->_environment = $app::environment();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\RedirectResponse|View
     */
    public function index()
    {

        try {
            $data = $this->_currency->get();
            if (!$data->isEmpty()) {
                return view('admin.currencies.index', compact('data'));
            } else {
                return view('admin.currencies.index',compact('data'))->with('error_message', 'No data found');
            }
        } catch (\Exception $e) {
            $this->make_log(__METHOD__, $e, 'error');
            return redirect()->back()->with('error_message', $this->_environment === MyAppEnv::LOCAL ? $e->getMessage() : 'something went wrong!');
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'country_currency' => 'required|string|max:255',
            'code' => 'required|string|max:255',
        ]);
        try {
            $this->_currency->create($request->all(['country_currency', 'code']));
            return redirect()->back()->with('success_message', 'Currency added successfully');
        } catch (\Exception $e) {
            $this->make_log(__METHOD__, $e, 'error');
            return redirect()->back()->with('error_message', $this->_environment === MyAppEnv::LOCAL ? $e->getMessage() : 'something went wrong!');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  Currency  $currency
     * @return \Illuminate\Http\Response|null
     */
    public function show(Currency $currency)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Currency  $currency
     * @return \Illuminate\Http\Response|null
     */
    public function edit(Currency $currency)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Currency  $currency
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Currency $currency)
    {
        try {
            $currency->update($request->all(['country_currency', 'code']));
            return redirect()->back()->with('success_message', 'Currency added successfully');
        } catch (\Exception $e) {
            $this->make_log(__METHOD__, $e, 'error');
            return redirect()->back()->with('error_message', $this->_environment === MyAppEnv::LOCAL ? $e->getMessage() : 'something went wrong!');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Currency  $currency
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Currency $currency)
    {
        try {
            $currency = $currency->delete();
            if($currency){
                return redirect()->back()->with('success_message', 'Currency deleted successfully');
            } else {
                return redirect()->back()->with('error_message', 'Something went wrong!');
            }
        } catch (\Exception $e) {
            $this->make_log(__METHOD__, $e, 'error');
            return redirect()->back()->with('error_message', $this->_environment === MyAppEnv::LOCAL ? $e->getMessage() : 'something went wrong!');
        }
    }
}
