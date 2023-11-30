<?php

namespace App\Http\Controllers\Admin;

use App\Models\Option;
use App\Models\SubService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class OptionController extends Controller
{
    /**
     * Undocumented variable
     *
     * @var Option $_option
     * @access private
     */
    private $_option;

    /**
     * Create a new controller instance.
     * @param Option $option
     * @return void
     */
    public function __construct(Option $option)
    {
        $this->_option = $option;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response|null
     */
    public function index($id)
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
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'option' => 'required',
            'question_id' => 'required',
        ]);

        try {
            $this->_option->createOption($request->all());
            return redirect()->back()->with('success_message', 'Option Added');
        } catch (\Exception $e) {
            $this->make_log(__METHOD__, $e, 'error');
            return  redirect()->back()->with('error_message', 'Something went wrong!');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  Option $option
     * @return \Illuminate\Http\Response|null
     */
    public function show(Option $option)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Option $option
     * @return \Illuminate\Http\Response|null
     */
    public function edit(Option $option)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Option $option
     * @return \Illuminate\Http\Response|null
     */
    public function update(Request $request, Option $option)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Option $option
     * @return \Illuminate\Http\Response|null
     */
    public function destroy(Option $option)
    {
        //
    }
}
