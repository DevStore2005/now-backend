<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Utils\UserType;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;

class AuthController extends Controller
{
    /**
     * Private variable to store the model
     *
     * @var User $_user
     * @access private
     */
    private $_user;

    /**
     * Create a new controller instance.
     * @param  User $user
     * @return void
     */
    public function __construct(User $user)
    {
        $this->_user = $user;
    }

	public function login(Request $request){
        if (Auth::check() && Auth::user()->role == UserType::ADMIN) {
            return redirect()->route('admin.dashboard');
        }
		switch ($request->method()) {
            case 'POST':
                $request->validate([
                    'email' => 'required',
                    'password' => 'required'
                ]);

                $params = [
                	'email' => $request->email, 
                	'password'=>$request->password,
                	'status' => 'ACTIVE',
                	'role' => UserType::ADMIN
                ];

                if (!Auth::attempt($params)) return redirect()->back()->with('error_message', 'Email or password do not match.');
                
		        return redirect()->route('admin.dashboard');

            case 'GET':
                return view('admin.auth.login');
            default:
                // invalid request
                break;
        }
	}

    public function logout(Request $request) {
        Auth::logout();
        return redirect()->route('admin.auth.login');
    }

    /**
     * forgotPassword
     * @param Request $request
     * @return mixed
     */
    public function forgotPassword(Request $request)
    {
        switch ($request->method()) {
            case 'POST':
                $request->validate([
                    'email' => 'required|email:rfc,dns'
                ]);

                $user = $this->_user->where('email', $request->email)->admin()->first();
                if (!$user) return redirect()->back()->with('error_message', 'Email not found.');
                $forget = $this->_user->forgotPassword($user);
                if ($forget)
                    return redirect()->route('admin.auth.login')->with('success_message', 'Password reset link sent to your email.');
                else
                    return redirect()->back()->with('error_message', 'Something went wrong.');
            default:
                return view('admin.auth.forgot-password');
        }
    }

    /**
     * 
     * @param Request $request
     * @param mixed $token
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function resetPassword(Request $request, $token)
    {
        $request->validate(['email' => 'required|email:rfc,dns',
        ]);

        $email = $request->email;

        return view('admin.auth.reset-password', compact('email', 'token'));
    }

    /**
     * updatePassword
     * @param Request $request
     * @return mixed
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'token' => 'required_if:reset,null',
            'email' => 'required_if:reset,null|email:rfc,dns',
            'password' => 'required|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill(['password' => Hash::make($password)]);

                $user->save();

                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET)
            return redirect()->route('admin.auth.login')->with('success_message', 'Password updated successfully.');
        else if ($status === Password::INVALID_TOKEN)
            return redirect()->back()->with('error_message', 'Invalid token.');
        else
            return redirect()->back()->with('error_message', 'Something went wrong.');
    }

    /**
     * changePassword
     * @param Request $request
     * @return mixed
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'old_password' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);
        $user = $request->user();
        if (!Hash::check($request->old_password, $user->password)) {
            return redirect()->back()->with('error_message', 'Old password does not match.');
        }
        $user->password = Hash::make($request->password);
        $user->save();
        return redirect()->back()->with('success_message', 'Password updated successfully.');
    }
}