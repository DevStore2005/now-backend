<?php

namespace App\Http\Controllers\Admin;

use App\Models\Portfolio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;
use App\Utils\HttpStatusCode;

class PortfolioController extends Controller
{
    /**
     * @var \App\Models\Portfolio $_portfolio
     * @var string $_environment
     */
    private $_portfolio, $_environment;


    /**
     * Create a new controller instance.
     * 
     * @param  \App\Models\Portfolio $portfolio
     * @param  App $app
     *
     * @return void
     */
    public function __construct(Portfolio $portfolio, App $app)
    {
        $this->_portfolio = $portfolio;
        $this->_environment = $app::environment();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response|null
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response|null
     */
    public function create()
    {
        //
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

    /**
     * Chnage status of the posrtfolio
     *
     * @param string $type
     * @param int $id
     * @param string $status
     * @return \Illuminate\Http\Response
     */
    public function changeStatus($type, $id, $status)
    {
        $res = $this->_portfolio->changeStatus($type, $id, $status ?? true);
        if ($res) {
            return redirect()->back()->with('success_message', 'Status Updated');
        } else {
            return redirect()->back()->with('error_message', 'Something went wrong!');
        }
    }
}
