<?php

namespace App\Http\Controllers\Admin;

use Stripe\Stripe;
use Stripe\Balance;
use App\Models\User;
use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Models\ServiceRequest;
use App\Http\Controllers\Controller;
use App\Models\Transaction;

class AdminController extends Controller
{

    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response|View
     */
    public function dashboard(Request $request)
    {
        $balance = 0;

        $totalUser = User::user()
            ->when($request->query('locale') !== 'all' && !empty($request->query('locale')), function ($query) use ($request) {
                return $query->where('country_id', $request->default_country->id);
            })
            ->count();

        $individualProvider = User::individualProvider()
            ->when($request->query('locale') !== 'all' && !empty($request->query('locale')), function ($query) use ($request) {
                return $query->where('country_id', $request->default_country->id);
            })
            ->count();
        $companyProvider = User::companyProvider()
            ->when($request->query('locale') !== 'all' && !empty($request->query('locale')), function ($query) use ($request) {
                return $query->where('country_id', $request->default_country->id);
            })
            ->count();

        $totalServiceRequest = ServiceRequest::count();
        $acceptedRequest = ServiceRequest::accepted()->count();
        $pendingRequest = ServiceRequest::pending()->count();
        $completedRequest = ServiceRequest::completed()->count();
        $rejectedRequest = ServiceRequest::rejected()->count();
        $cancelledRequest = ServiceRequest::cancelled()->count();

        $now = now();
        $week1Date = $now->subWeek()->format('Y-m-d H:i:s');
        $week2Date = $now->subWeeks(2)->format('Y-m-d H:i:s');
        $week3Date = $now->subWeeks(3)->format('Y-m-d H:i:s');
        $week4Date = $now->subWeeks(4)->format('Y-m-d H:i:s');

        $week1 = ServiceRequest::whereBetween('created_at', [$week1Date, now()->format('Y-m-d H:i:s')])->count();
        $week2 = ServiceRequest::whereBetween('created_at', [$week2Date, $week1Date])->count();
        $week3 = ServiceRequest::whereBetween('created_at', [$week3Date, $week2Date])->count();
        $week4 = ServiceRequest::whereBetween('created_at', [$week4Date, $week3Date])->count();

        $bonus = Transaction::where('type', 'BONUS')->sum('amount_captured');
        $commission = Transaction::where('type', 'COMMISSION')->sum('amount');
        $credit = Transaction::where([
            ['payment_id', '!=', null],
            ['service_request_id', "!=", 'CREDIT'],
            ['is_credit', true]
        ])->sum('amount_captured');
        $refund = Transaction::whereNotNull('refund_id')->sum('amount_captured');

        try {
            $balance = Balance::retrieve();
        } catch (\Throwable $th) {
            //throw $th;
        }
        return view('admin.dashboard', compact(
            'balance',
            'totalUser',
            'individualProvider',
            'companyProvider',
            'totalServiceRequest',
            'acceptedRequest',
            'pendingRequest',
            'completedRequest',
            'rejectedRequest',
            'cancelledRequest',
            'week1',
            'week2',
            'week3',
            'week4',
            'bonus',
            'commission',
            'credit',
            'refund'
        ));
    }


    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\RedirectResponse|View
     */
    public function profile(Request $request)
    {
        switch ($request->method()) {
            case 'GET':
                $admin = $request->user();
                return view('admin.profiles.admin-profile', compact('admin'));
            case 'POST':
                $admin = $request->user();
                $admin->first_name = $request->first_name;
                $admin->last_name = $request->last_name;
                $admin->save();
                return redirect()->back()->with('success_message', "Profile updated successfully!");
            default:
                return redirect()->back()->with('error_message', "Invalid request method!");
        }
    }
}
