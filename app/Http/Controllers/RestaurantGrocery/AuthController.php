<?php

namespace App\Http\Controllers\RestaurantGrocery;

use Session;
use App\Models\User;
use App\Utils\AppConst;
use App\Utils\UserType;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {

        if (auth()->check()) {
            $user = auth()->user();
            if ($user->role == UserType::RESTAURANT_OWNER) {
                return redirect()->route('restaurant.dashboard');
            } elseif ($user->role == UserType::GROCERY_OWNER) {
                return redirect()->route('grocer.dashboard');
            }
        }

        switch ($request->method()) {
            case 'POST':
                $request->validate([
                    // 'role' => 'required',
                    'email' => 'required',
                    'password' => 'required'
                ]);
                $role = $request->route()->action['subdomain'] == 'grocer' ? UserType::GROCERY_OWNER :  UserType::RESTAURANT_OWNER;
                $user = User::whereEmail($request->email)->whereRole($role)->first();
                if ($user) {
                    if ($user->status == AppConst::PENDING) {
                        return redirect()->back()->with('message', 'Your profile is not approved yet..!');
                    }
                    if ($user->status == 'ACTIVE') {
                        $params = [
                            'email' => $request->email,
                            'password' => $request->password,
                            'role' => $role,
                            'status' => 'ACTIVE',
                        ];
                        if (!Auth::attempt($params)) {
                            return redirect()->back()->with('error_message', 'Email or password do not match.');
                        }
                        if (Auth::user()->role == UserType::RESTAURANT_OWNER) {
                            return redirect()->route($user->business_profile ? 'restaurant.dashboard' : 'restaurant.profileSetting');
                        } else {
                            return redirect()->route($user->business_profile ? 'grocer.dashboard' : 'grocer.profileSetting');
                        }
                    }

                } else {
                    return redirect()->back()->with('error_message', 'Email or password do not match.');
                }

            case 'GET':
                return view('restaurant_grocery.auth.login', ['subdomain' => $request->route()->action['subdomain']]);
            default:
                // invalid request
                break;
        }
    }


    public function signup(Request $request)
    {


        switch ($request->method()) {
            case 'POST':

                $already = User::whereEmail($request->email)->first();
                if ($already) {
                    return back()->with('error_message', 'Email already register');
                }

                $role = '';
                if ($request->category == 'restaurant') {
                    $role = UserType::RESTAURANT_OWNER;
                } else {
                    $role = UserType::GROCERY_OWNER;
                }

                User::create([
                    'phone' =>  $request->phone,
                    'email' =>  $request->email,
                    'password' => bcrypt($request->password),
                    'role' => $role,
                    'status' => 'PENDING',
                ]);

                return redirect()->route($request->route()->action['subdomain'] == 'grocer' ? 'grocer.login' : 'restaurant.login')->with('message', 'Please wait for Admin Approval');


            case 'GET':
                return view('restaurant_grocery.auth.signup', ['subdomain' => $request->route()->action['subdomain']]);
            default:
                // invalid request
                break;
        }
    }


    public function logout()
    {   $auth = auth();
        if($auth->user()->role == UserType::RESTAURANT_OWNER){
            $auth->logout();
            return redirect()->route('restaurant.login');
        }else{
            $auth->logout();
            return redirect()->route('grocer.login');
        }
    }
}
