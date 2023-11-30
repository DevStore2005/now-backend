<?php

namespace App\Http\Controllers\Admin;

use Stripe\Stripe;
use Stripe\Balance;
use App\Models\User;
use Illuminate\View\View;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Utils\TransactionType;
use App\Http\Controllers\Controller;

class TransactionController extends Controller
{

    /**
     *  @var Transaction $_transaction 
     * 
     */
    private $_transaction;


    /**
     * Create a new controller instance.
     * @param  Transaction $transaction
     */
    public function __construct(Transaction $transaction)
    {
        $this->_transaction = $transaction;
        Stripe::setApiKey(config('services.stripe.secret'));
    }


    /**
     * Display a listing of the resource.
     *
     * @return View
     */
    public function index()
    {
        $balance = Balance::retrieve();
        $transactions = $this->_transaction->get();
        return view('admin.transactions.index', compact('balance', 'transactions'));
    }


    /**
     * Display a listing of the resource.
     * @param User $user
     *
     * @return \Illuminate\Http\Response
     */
    public function pay(User $user)
    {
        if (!$user->provider()->exists()) {
            return redirect()->back()->with('error_message', 'User is not a provider');
        }
        $user->load('provider_profile');
        if (!$user->provider_profile->earn) {
            return redirect()->back()->with('error_message', 'Provider does not have earn');
        }

        $this->_transaction->create([
            'provider_id' => $user->id,
            'amount' => $user->provider_profile->earn,
            'type' => TransactionType::EARN,
            'is_credit' => false,
            'status' => "succeeded",
        ]);

        $user->provider_profile->earn = 0;
        $user->provider_profile->commission = 0;
        $user->provider_profile->save();

        return redirect()->back()->with('success_message', 'Provider has been paid');
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
}
