<?php

namespace App\Http\Controllers\Admin;

use App\Events\AlertEvent;
use App\Models\Country;
use App\Models\ZipCode;
use Carbon\Carbon;
use App\Models\User;
use App\Utils\AppConst;
use App\Utils\UserType;
use App\Http\Helpers\Fcm;
use App\Models\Portfolio;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\View\View;
use App\Models\Transaction;
use App\Utils\ProviderType;
use Illuminate\Support\Str;
use App\Exports\UsersExport;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\ServiceRequest;
use App\Utils\TransactionType;
use App\Models\ProviderProfile;
use App\Models\ProviderService;
use App\Models\PhoneVerification;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Events\UserStatusChangeEvent;
use Illuminate\Http\RedirectResponse;
use App\Notifications\UserNotification;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class UserController extends Controller
{
    /**
     * User list
     *
     * @param Request $request
     * @return RedirectResponse|View
     */
    public function index(Request $request)
    {
        try {
            $data['data'] = User::whereRole(UserType::USER)
                ->when($request->query('locale') !== 'all' && !empty($request->query('locale')), function ($query) use ($request) {
                    return $query->where('country_id', $request->default_country->id);
                })
                ->when($request->query('type') == "PENDING", function ($query) {
                    return $query->where('status', AppConst::PENDING);
                })
                ->when($request->query('type') == "ACTIVE", function ($query) {
                    return $query->where('status', AppConst::ACTIVE);
                })
                ->when($request->query('type') == "SUSPENDED", function ($query) {
                    return $query->where('status', AppConst::SUSPENDED);
                })
                ->when($request->query('type') == "VERIFIED", function ($query) {
                    return $query->whereNotNull('email_verified_at');
                })
                ->when($request->query('type') == 'OLDEST', function ($query) {
                    return $query->oldest();
                }, function ($query) {
                    return $query->latest();
                })
                ->paginate(AppConst::PAGE_SIZE)->withQueryString();
            return view('admin.users.index', $data);
        } catch (\Exception $e) {
            $this->make_log(__METHOD__, $e, 'error');
            return redirect()->back()->with('error_message', 'something went wrong!');
        }
    }

    /**
     * Provider list
     *
     * @param Request $request
     * @return View|RedirectResponse
     */
    public function providers(Request $request)
    {
        try {
            $data['data'] = User::whereRole(UserType::PROVIDER)
                ->when($request->query('locale') !== 'all' && !empty($request->query('locale')), function ($query) use ($request) {
                    return $query->where('country_id', $request->default_country->id);
                })
                ->when($request->query('type') == "PENDING", function ($query) {
                    return $query->where('status', AppConst::PENDING);
                })
                ->when($request->query('type') == "ACTIVE", function ($query) {
                    return $query->where('status', AppConst::ACTIVE);
                })
                ->when($request->query('type') == "SUSPENDED", function ($query) {
                    return $query->where('status', AppConst::SUSPENDED);
                })
                ->when($request->query('type') == "VERIFIED", function ($query) {
                    return $query->whereNotNull('email_verified_at');
                })
                ->when($request->query('type') == 'OLDEST', function ($query) {
                    return $query->oldest();
                }, function ($query) {
                    return $query->latest();
                })
                ->paginate(AppConst::PAGE_SIZE)->withQueryString();
            return view('admin.providers.index', $data);
        } catch (\Exception $e) {
            $this->make_log(__METHOD__, $e, 'error');
            return redirect()->back()->with('error_message', 'something went wrong!');
        }
    }

    /**
     * Restaurant list
     *
     * @param Request $request
     * @return View|RedirectResponse
     */
    public function restaurants()
    {
        try {
            $data['data'] = User::whereRole(UserType::RESTAURANT_OWNER)->latest()->paginate(AppConst::PAGE_SIZE);
            return view('admin.restaurants.index', $data);
        } catch (\Exception $e) {
            $this->make_log(__METHOD__, $e, 'error');
            return redirect()->back()->with('error_message', 'something went wrong!');
        }
    }

    /**
     * Restaurant list
     *
     * @param Request $request
     * @return View|RedirectResponse
     */
    public function groceryStores()
    {
        try {
            $data['data'] = User::whereRole(UserType::GROCERY_OWNER)->latest()->paginate(AppConst::PAGE_SIZE);
            return view('admin.restaurants.index', $data);
        } catch (\Exception $e) {
            $this->make_log(__METHOD__, $e, 'error');
            return redirect()->back()->with('error_message', 'something went wrong!');
        }
    }

    /**
     * User status change
     */
    public function user_update_status(Request $request, $status, $id)
    {
        $user = User::find($id);
        if ($status == 'inactive') {
            $user->status = AppConst::PENDING;
        } else if ($status == 'active') {
            if ($user->role == UserType::PROVIDER && $user->provider_type == ProviderType::INDIVIDUAL) {
                $user->load('schedules');
                if (!$user->schedules->count()) {
                    $notification = [
                        'title' => 'You have not added your Time slots yet',
                        'body' => 'Please submit your time slots to get approved by admin',
                        'user_id' => $user->id,
                        'type' => 'Schedule'
                    ];
                    broadcast(new AlertEvent(['id' => $user->id, 'payload' => $notification]));
                    if ($user->device_token) Fcm::push_notification($notification, [$user->device_token], $user->role, $user->os_platform);
                    $user->notify(new UserNotification($notification));
                    return redirect()->back()->with('error_message', 'provider has no schedule yet');
                }
            }
            $user->status = AppConst::ACTIVE;
        } elseif ($status == 'suspended') {
            $user->status = AppConst::SUSPENDED;
        }
        $user->save();
        $payload = [
            'title' => "Account {$status}",
            'body' => "Account status changed to {$status}",
            'user_id' => $user->id,
            'type' => 'Status'
        ];
        broadcast(new AlertEvent(['id' => $user->id, 'payload' => $payload]));
        broadcast(new UserStatusChangeEvent($user));
        $user->notify(new UserNotification($payload));
        if (isset($user->device_token)) {
            Fcm::push_notification($payload, [$user->device_token], $user->role, $user->os_platform);
        }
        return redirect()->back()->with('success_message', 'Change Status updated');
    }

    public function user_update_verified(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:users,id',
            'verified' => 'required|in:1,0'
        ]);
        try {
            return response()->json([
                'message', "User " . $request->verified ? 'verified' : 'unverified',
                'status' => User::where('id', $request->id)->update(['verified_at' => $request->verified ? now() : null])
            ]);
        } catch (\Exception $e) {
            $this->make_log(__METHOD__, $e, 'error');
            return response()->json(['message' => 'Something went wrong'], 500);
        }
    }


    /**
     * @param User $user
     * @return Application|Factory|\Illuminate\Contracts\View\View|\Illuminate\Foundation\Application
     */
    public function profile(User $user): \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|Factory|Application
    {
        $profit = [];
        if ($user->role === "PROVIDER") {
            $user->load(['provider_profile', 'schedules', 'medias', 'provider_services.sub_service', 'provider_services.service', 'portfolios']);
            $profit['this month'] = ServiceRequest::where('provider_id', $user->id)->whereDate('created_at', '>=', Carbon::now()->subMonth()->format('Y-m-d'))->sum('paid_amount');
            $profit['this week'] = ServiceRequest::where('provider_id', $user->id)->whereDate('created_at', '>=', Carbon::now()->subWeek()->format('Y-m-d'))->sum('paid_amount');
            $profit['today'] = ServiceRequest::where('provider_id', $user->id)->whereDate('created_at', '>=', Carbon::now()->today()->format('Y-m-d'))->sum('paid_amount');
        }

        $service_area = ZipCode::query()->whereHas('users', function ($q) use ($user) {
            return $q->where('id', $user->id);
        })->with([
            'states' => function ($query) {
                return $query->distinct('id');
            },
            'states.country',
            'service_areas:id,zip_code_id,place_id'
        ])->get();
        $countries = Country::query()->where('is_active', 1)->get();
        return view('admin.profiles.profile', compact('user', 'profit', 'countries', 'service_area'));
    }

    /**
     * delete user
     *
     * @param Request $request
     * @param int $id
     * @return RedirectResponse
     */
    public function delete(Request $request, $id)
    {
        try {
            $data = User::find($id);
            if (!$data) {
                return redirect()->back()->with('error_message', 'User not found');
            }
            $payload = [
                'title' => 'Account Deleted',
                'body' => 'Your account has been deleted by admin',
                'user_id' => $data->id,
                'type' => 'Account Deleted'
            ];
            if ($data->device_token) Fcm::push_notification($payload, [$data->device_token], $data->role, $data->os_platform);
            broadcast(new AlertEvent(['id' => $data->id, 'payload' => $payload]));
            $data->notify(new UserNotification($payload));

            $data->zip_codes()->detach();
            Portfolio::where('provider_id', $id)->delete();
            PhoneVerification::where('phone', $data->phone)->delete();
            ProviderProfile::where('provider_id', $data->id)->delete();
            ProviderService::where('provider_id', $data->id)->delete();
            $data->tokens()->delete();
            $data->delete();

            return redirect()->back()->with('success_message', 'User deleted');
        } catch (\Exception $e) {
            $this->make_log(__METHOD__, $e, 'error');
            return redirect()->back()->with(['error_message' => 'something went wrong!']);
        }
    }

    /**
     * Add credit to provider
     *
     * @param Request $request
     * @param int $id
     */
    public function credit(Request $request, $id)
    {
        try {
            $data = User::provider()->find($id);
            if ($data == null) {
                return redirect()->back()->with('error_message', 'Provider not found');
            }
            $data->credit = $data->credit + $request->credit;
            $data->save();
            Transaction::create([
                'provider_id' => $data->id,
                'amount' => "+{$request->credit}",
                'type' => TransactionType::BONUS,
                'amount_captured' => $request->credit,
                'status' => "succeeded"
            ]);
            return redirect()->back()->with('success_message', 'Credit added');
        } catch (\Exception $e) {
            $this->make_log(__METHOD__, $e, 'error');
            return redirect()->back()->with(['error_message' => 'something went wrong!']);
        }
    }

    /**
     * Download User Info as CSV
     *
     * @param string $role
     *
     * @return RedirectResponse|BinaryFileResponse
     */
    public function download($role)
    {
        try {
            if ($role == 'provider' || $role == 'user') {
                return Excel::download(new UsersExport($role), $role . '-profiles.csv');
            } else {
                return redirect()->back()->with('error_message', 'something went wrong!');
            }
        } catch (\Exception $e) {
            $this->make_log(__METHOD__, $e, 'error');
            return redirect()->back()->with(['error_message' => 'something went wrong!']);
        }
    }
}
