<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Utils\AppConst;
use Illuminate\View\View;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Utils\HttpStatusCode;
use App\Utils\TransactionType;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\ServiceRequest;

class HistoryController extends Controller
{
    /**
     *  @var User $_user
     *  @var ServiceRequest $_serviceRequest
     *  @var Transaction $_transction
     *  @var string $_environment
     */
    private $_user, $_transaction, $_environment, $_serviceRequest;


    /**
     * Create a new controller instance.
     * @param  User $user
     * @param  ServiceRequest $serviceRequest
     * @param  Transaction $transaction
     * @param  App  $app
     *
     * @return void
     */
    public function __construct(User $user, Transaction $transaction, App $app, ServiceRequest $serviceRequest)
    {
        $this->_user = $user;
        $this->_serviceRequest = $serviceRequest;
        $this->_transaction = $transaction;
        $this->_environment = $app::environment();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response|View
     */
    public function index($type, $provider_id)
    {
        try {
            $provider = $this->_user->provider()->find($provider_id);
            if (!$provider) return redirect()->back()->with('error_message', 'provider not found');
            switch ($type) {
                case 'commission':
                    $commissionHistory = $this->_transaction->query()
                        ->whereType(TransactionType::COMMISSION)
                        ->whereProvider_id($provider->id)
                        ->latest()
                        ->simplePaginate(AppConst::PAGE_SIZE - 60);
                    return view('admin.histories.index', compact('commissionHistory', 'type'));
                case 'credit':
                    $transactionHistory = $this->_transaction->query()
                        ->whereIs_credit(1)
                        ->where(function ($qry) {
                        return $qry->whereType(TransactionType::BONUS)
                                ->orWhere('type', null);
                        })
                        ->whereProvider_id($provider->id)
                        ->latest()
                        ->simplePaginate(AppConst::PAGE_SIZE - 60);
                    return view('admin.histories.index', compact('transactionHistory', 'type'));
                case 'pay':
                    $transactionHistory = $this->_transaction->query()
                        ->whereType(TransactionType::EARN)
                        ->whereProvider_id($provider->id)
                        ->select(['id', 'provider_id', 'amount', 'created_at'])
                        ->with('provider:id,first_name,last_name')
                        ->latest()
                        ->simplePaginate(AppConst::PAGE_SIZE - 60);
                    return view('admin.histories.index', compact('transactionHistory', 'type'));
                default:
                    return redirect()->back()->with(['error_message' => "Not Found"]);
            }
        } catch (\Exception $e) {
            $this->make_log(__METHOD__, $e, 'error');
            return redirect()->back()->with('error_message', 'something went wrong!');
        }
    }

    public function providerServices($provider_id)
    {
        try {
            $type = 'Provider History';
            $serviceRequest = $this->_serviceRequest->query()
                ->whereProvider_id($provider_id)
                ->latest()
                ->simplePaginate(AppConst::PAGE_SIZE - 60);
            if ($serviceRequest->isEmpty()) {
                return redirect()->back()->with('error_message', 'No service request found');
            }
            return view('admin.histories.index', compact('serviceRequest', 'type'));
        } catch (\Exception $e) {
            $this->make_log(__METHOD__, $e, 'error');
            return redirect()->back()->with('error_message', 'something went wrong!');
        }
    }
    public function userServices($user_id)
    {
        try {
            $type = 'User History';
            $serviceRequest = $this->_serviceRequest->query()
                ->whereUser_id($user_id)
                ->with([
                    'provider:id,first_name,last_name',
                    'user:id,first_name,last_name',
                    'requested_sub_service:id,name'
                ])
                ->latest()
                ->simplePaginate(AppConst::PAGE_SIZE - 60);
            if ($serviceRequest->isEmpty()) {
                return redirect()->back()->with('error_message', 'No service request found');
            }
            return view('admin.histories.index', compact('serviceRequest', 'type'));
        } catch (\Exception $e) {
            $this->make_log(__METHOD__, $e, 'error');
            return redirect()->back()->with('error_message', 'something went wrong!');
        }
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
