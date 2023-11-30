<?php

namespace App\Http\Controllers\Admin;

use App\Models\Country;
use App\Utils\MyAppEnv;
use App\Models\Currency;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class CountryController extends Controller
{

    /**
     * @var string $_environment
     * @var Currency $_currency
     */
    private $_environment;

    /**
     * Create a new controller instance.
     * @param App $app
     * @return Currency $currency
     * @return void
     */
    public function __construct(App $app)
    {
        $this->_environment = $app::environment();
    }

    public function index(Request $request)
    {
        $countries = Country::query()
            ->orderByDesc('is_active')
            ->orderByDesc('is_default')
            ->get();
        return \view('admin.countries.index', compact('countries'));
    }

    public function create()
    {
        return \view('admin.countries.create');
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required',
            'iso2' => 'required|min:2|max:2',
            'iso3' => 'required|min:3|max:3',
            'currency' => 'nullable|max:3',
        ]);

        $request['is_default'] = filter_var($request->is_default, FILTER_VALIDATE_BOOLEAN);
        $request['is_active'] = filter_var($request->is_active, FILTER_VALIDATE_BOOLEAN);
        $request['stripe_enable'] = filter_var($request->stripe_enable, FILTER_VALIDATE_BOOLEAN);

        DB::beginTransaction();
        try {
            Country::create($request->all());
            DB::commit();
            return redirect()->back()->with('success_message', 'Country added successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->make_log(__METHOD__, $e, 'error');
            return redirect()->back()->with('error_message', $this->_environment === MyAppEnv::LOCAL ? $e->getMessage() : 'something went wrong!');
        }
    }


    public function edit(Country $country)
    {
        return \view('admin.countries.edit', compact('country'));
    }

    public function update(Request $request, Country $country)
    {
        $request->validate([
            'name' => 'required',
            'iso2' => 'required|min:2|max:2',
            'iso3' => 'required|min:3|max:3',
            'currency' => 'nullable|max:3',
        ]);

        $request['is_default'] = filter_var($request->is_default, FILTER_VALIDATE_BOOLEAN);
        $request['is_active'] = filter_var($request->is_active, FILTER_VALIDATE_BOOLEAN);
        $request['stripe_enable'] = filter_var($request->stripe_enable, FILTER_VALIDATE_BOOLEAN);

        DB::beginTransaction();
        try {
            $country->update($request->all());
            DB::commit();
            return redirect()->back()->with('success_message', 'Country updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->make_log(__METHOD__, $e, 'error');
            return redirect()->back()->with('error_message', $this->_environment === MyAppEnv::LOCAL ? $e->getMessage() : 'something went wrong!');
        }
    }

}
