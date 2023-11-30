<?php

namespace App\Http\Controllers\Admin;

use App\Utils\MyAppEnv;
use Illuminate\View\View;
use App\Models\Commission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Requests\CommissionStoreRequest;

class CommissionController extends Controller
{
    /**
     * @var Commission $_commission
     * @var string $_environment
     */
    private $_commission, $_environment;

    /**
     * Create a new Controller instance.
     * 
     * @param Commission $commission
     * @param App $app
     *
     * @return void
     */
    public function __construct(Commission $commission, App $app)
    {
        $this->_commission = $commission;
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
            $data = $this->_commission->first();
            return view('admin.commissions.index', compact('data'));
        } catch (\Exception $e) {
            $this->make_log(__METHOD__, $e, 'error');
            return redirect()->back()->with('error_message', $this->_environment === MyAppEnv::LOCAL ? $e->getMessage() : 'something went wrong!');
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response|View|null
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  CommissionStoreRequest  $request
     * @return \Illuminate\Http\Response|View
     */
    public function store(CommissionStoreRequest $request)
    {
        try {
            $isUpdate = null;
            $data = $this->_commission->first();
            if ($data) {
                $isUpdate = true;
                $data->update($request->validated());
            } else {
                $data = $this->_commission->create($request->validated());
            }
            if ($data) return redirect()->route('admin.commissions.index')->with('success_message', $isUpdate ? 'Commission update successfully!' : 'Commission create successfully!');
            else return redirect()->back()->with('error_message', 'Something went wrong!');
        } catch (\Exception $e) {
            $this->make_log(__METHOD__, $e, 'error');
            return redirect()->back()->with('error_message', $this->_environment === MyAppEnv::LOCAL ? $e->getMessage() : 'something went wrong!');
        }
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
