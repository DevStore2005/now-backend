<?php

namespace App\Http\Controllers\Api\Provider;

use App\Models\Address;
use Carbon\Carbon;
use App\Models\User;
use App\Utils\AppConst;
use App\Utils\UserType;
use Illuminate\Support\Str;
use App\Http\Helpers\Common;
use Illuminate\Http\Request;
use App\Jobs\SendEmailOtpJob;
use App\Jobs\UserRegisterJob;
use App\Utils\HttpStatusCode;
use App\Models\ProviderProfile;
use Illuminate\Validation\Rule;
use App\Models\PhoneVerification;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Password;
use App\Http\Requests\ProviderSignupEmailRequest;

class AuthController extends Controller
{

    /**
     * @var User $_user
     * @var Common $helper
     */
    private $_user, $helper;


    public function __construct(Common $helper, User $user)
    {
        $this->helper = $helper;
        $this->_user = $user;
    }

    /**
     * signup provider
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function signup(Request $request)
    {
        $request->validate([
            'email' => [
                'required', 'email:rfc,dns',
                Rule::unique('users')->where(function ($query) {
                    return $query->whereRole(UserType::PROVIDER)
                        ->where(function ($qry) {
                            return $qry->WhereNotNull('email_verified_at')
                                ->orWhereNotNull('social_id');
                        })
                        ->whereNull('deleted_at');
                }),
            ],
            'password' => 'required',
            'country_id' => 'required',
            // 'phone' => [
            //     'required',
            //     Rule::unique('users')->where(function ($query) {
            //         return $query->whereRole(UserType::PROVIDER)->where('phone_verification', true)->whereNull('deleted_at');
            //     })
            // ]
        ]);

        // $otp = rand(1000, 9999);
        // $ph_ver = PhoneVerification::updateOrCreate(['phone' => $request->phone], ['otp' => $otp]);

        // $message = 'Welcome to FareNow. Use this otp to verify your phone number. ' . $ph_ver->otp;

        // $res = $this->helper->send_sms($ph_ver->phone, $message);

        // if ($res['error']) {
        //     $ph_ver->delete();
        //     return response()->json(['error' => true, 'message' => $res['message']], 401);
        // }
        $createOtp = $this->_create_otp($request->email, "email");
        dispatch(new SendEmailOtpJob($createOtp['has']));
        $user = User::whereEmail($request->email)->whereRole(UserType::PROVIDER)->first();
        if ($user == null) {
            $user = new User();
        }
        $user->email = $request->email;
        $user->country_id = $request->country_id;
        $user->phone = 0000;
        $user->password = bcrypt($request->password);
        $user->role = UserType::PROVIDER;
        $user->status = AppConst::PENDING;
        $user->service_type = 'SERVICE';
        $this->_user->country_id = $request->country_id ?? null;
        $user->save();

        return response()->json([
            'error' => false,
            'message' => 'success',
            'opt' => $createOtp['has'],
            'data' => $user
        ]);
    }

    /**
     * User signup using email
     * @param ProviderSignupEmailRequest $request
     * @return JsonResponse
     */
    public function signupEmail(ProviderSignupEmailRequest $request)
    {
        try {
            $createOtp = $this->_create_otp($request->email, "email");
            dispatch(new SendEmailOtpJob($createOtp['has']));
            return response()->json(['error' => false, 'opt' => $createOtp['has'], 'message' => 'Otp has been sent on your Email. Please verify your phone number.'], 200);
        } catch (\Exception $e) {
            Log::error('Error in signupEmail: ' . [$e->getMessage(), $e->getLine(), $e->getFile()]);
            return response()->json([
                'error' => true,
                'message' => 'Something went wrong. Please try again later.'
            ], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * login provider with google or facebook
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function handleSocialLogin(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required',
            'social_id' => 'required',
            'social_type' => 'required',
            'country_id' => 'required',
        ]);

        $user = User::whereRole(UserType::PROVIDER)->whereEmail($request->email)->first();

        if ($user == null) {
            $user = new User();
            $user->first_name = $request->name;
            $user->last_name = $request->name;
            $user->email = $request->email;
            $user->role = UserType::PROVIDER;
            $user->status = AppConst::PENDING;
            $user->phone = 00000000000;
            $user->email_verified_at = Carbon::now();
            $user->phone_verification = false;
            $user->social_id = $request->social_id;
            $user->social_type = $request->social_type;
            $user->country_id = $request->country_id;
            $user->password = bcrypt(Str::random(10));
            $user->save();
        }
        if ($user->social_id != $request->social_id) {
            $user->social_id = $request->social_id;
            $user->social_type = $request->social_type;
            $user->save();
        }

        if ($user->status == AppConst::SUSPENDED) {
            return response()->json(['error' => true, 'message' => 'Your account is suspended please contect with Admin.'], 401);
        }

        $user->load(['provider_profile', 'zip_codes']);
        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->token;
        $token->expires_at = Carbon::now()->addYear();
        $token->save();
        return response()->json([
            'error' => false,
            'message' => 'success',
            'data' => [
                'user' => $user,
                'auth_token' => 'Bearer ' . $tokenResult->accessToken,
                'expires_at' => Carbon::parse($tokenResult->token->expires_at)->toDateTimeString()
            ]
        ]);
    }

    /**
     * Verify phone number
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function signupPhoneVerify(Request $request)
    {
        $request->validate([
            'phone' => ['required'],
            'otp' => ['required', 'min:4', 'max:4']
        ]);

        $check = PhoneVerification::where('phone', $request->phone)
            ->where('otp', $request->otp)
            ->when(isset($request->for_password) == false, function ($query) {
                return $query->where('is_verified', false);
            })->first();

        if (empty($check)) {
            return response()->json(['error' => true, 'message' => 'Invalid Otp.'], 401);
        }

        if ($request->for_password) {
            $check->is_verified = true;
            $check->save();
            $user = $this->_user->where('phone', $request->phone)->where('role', UserType::PROVIDER)->first();
            if ($user) {
                return response()->json(['error' => false, 'message' => 'Phone verified.', "token" => Password::getRepository()->create($user)], 200);
            } else {
                return response()->json(['error' => true, 'message' => 'User not found.'], 401);
            }
        }

        $user = User::where([
            'role' => UserType::PROVIDER,
            'phone' => $request->phone,
            'status' => 'PENDING',
        ])->first();

        $user->phone_verification = true;
        $user->save();
        $check->delete();

        Auth::loginUsingId($user->id);

        $user = $request->user();

        $tokenResult = $user->createToken('Personal Access Token');

        $token = $tokenResult->token;
        $token->expires_at = Carbon::now()->addYear();
        $token->save();

        return response()->json([
            'error' => false,
            'message' => 'success',
            'data' => [
                'user' => $user,
                'auth_token' => 'Bearer ' . $tokenResult->accessToken,
                'expires_at' => Carbon::parse($tokenResult->token->expires_at)->toDateTimeString()
            ]
        ]);
    }

    public function signupEmailVerify(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email:rfc,dns'],
            'otp' => ['required', 'min:4', 'max:4']
        ]);

        $check = PhoneVerification::where('email', $request->email)
            ->where('otp', $request->otp)
            ->when(isset($request->for_password) == false, function ($query) {
                return $query->where('is_verified', false);
            })->first();

        if (empty($check)) {
            return response()->json(['error' => true, 'message' => 'Invalid Otp.'], 401);
        }

        if ($request->for_password) {
            $check->is_verified = true;
            $check->save();
            $user = $this->_user->where('email', $request->email)->where('role', UserType::PROVIDER)->first();
            if ($user) {
                if ($request->password) {
                    $user->password = bcrypt($request->password);
                    $user->save();
                    return response()->json(['error' => false, 'message' => 'Password changed successfully.'], 200);
                }
                return response()->json(['error' => false, 'message' => 'Email verified.', "token" => Password::getRepository()->create($user)], 200);
            } else {
                return response()->json(['error' => true, 'message' => 'User not found.'], 401);
            }
        }

        $user = User::where([
            'role' => UserType::PROVIDER,
            'email' => $request->email,
            'status' => 'PENDING',
        ])->first();

        $user->email_verified_at = Carbon::now();

        $user->save();
        $check->delete();

        Auth::loginUsingId($user->id);

        $user = $request->user();

        $tokenResult = $user->createToken('Personal Access Token');

        $token = $tokenResult->token;
        $token->expires_at = Carbon::now()->addYear();
        $token->save();

        return response()->json([
            'error' => false,
            'message' => 'success',
            'data' => [
                'user' => $user,
                'auth_token' => 'Bearer ' . $tokenResult->accessToken,
                'expires_at' => Carbon::parse($tokenResult->token->expires_at)->toDateTimeString()
            ]
        ]);
    }

    /**
     * Resend OPT to the provider
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function signupPhoneVerifyResend(Request $request)
    {
        $request->validate([
            'phone' => 'required|exists:users,phone'
        ]);

        $otp = rand(1000, 9999);
        $ph_ver = PhoneVerification::updateOrCreate(['phone' => $request->phone], ['otp' => $otp]);

        $message = 'Welcome to FareNow. Use this otp to verify your phone number. ' . $ph_ver->otp;

        $res = $this->helper->send_sms($ph_ver->phone, $message);

        if ($res['error']) {
            $ph_ver->delete();
            return response()->json(['error' => true, 'message' => $res['message']], 401);
        }

        return response()->json(['error' => false, 'message' => 'success']);
    }

    /**
     * Resend OPT to the provider
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function signupEmailVerifyResend(Request $request)
    {
        $request->validate([
            'email' => 'required|exists:users,email|email:rfc,dns'
        ]);

        $createOtp = $this->_create_otp($request->email, "email");
        dispatch(new SendEmailOtpJob($createOtp['has']));
        return response()->json(['error' => false, 'opt' => $createOtp['has'], 'message' => 'Otp has been sent on your Email. Please verify your Email.'], 200);
    }

    /**
     * Provider login
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function login(Request $request)
    {
        $request->validate([
            'user_name' => 'required',
            'password' => 'required',
        ]);

        $params = [];

        if (is_numeric($request->get('user_name'))) {
            $params = ['phone' => $request->user_name, 'password' => $request->password];
            $user = User::where('phone', $request->user_name)->whereRole(UserType::PROVIDER)->with('provider_profile')->first();
            if ($user) {
                if (!$user->phone_verification) {
                    return response()->json(['error' => true, 'message' => 'Phone number is not verified', 'data' => $user], 401);
                }
            }
        } elseif (filter_var($request->user_name, FILTER_VALIDATE_EMAIL)) {
            $params = ['email' => $request->user_name, 'password' => $request->password];
            $user = User::where('email', $request->user_name)->whereRole(UserType::PROVIDER)->with('provider_profile')->first();
            if ($user) {
                if (!$user->email_verified_at) {
                    return response()->json(['error' => true, 'message' => 'email is not verified', 'data' => $user], 401);
                }
            }
        }

        // Verification

        $params['role'] = UserType::PROVIDER;

        if (!Auth::attempt($params)) {
            if ($user && !Hash::check($request->password, $user->password)) {
                return response()->json(['error' => true, 'message' => array('password' => "Password doesn't match")], 401);
            }
            if (isset($user) && $user->social_type) {
                return response()->json(['error' => true, 'message' => "You have logged in by {$user->social_type}. please login by {$user->social_type} or forgot your password"], 401);
            }
            return response()->json(['error' => true, 'message' => array('user_name' => 'Account not found')], 401);
        }

        $user = $request->user();

        if ($user->status == AppConst::SUSPENDED) {
            return response()->json(['error' => true, 'message' => 'Your account is suspended please contect with Admin.'], 401);
        }

        $tokenResult = $user->createToken('Personal Access Token');

        $token = $tokenResult->token;
        $token->expires_at = Carbon::now()->addYear();
        $token->save();

        return response()->json([
            'error' => false,
            'message' => 'success',
            'data' => [
                'provider' => $user,
                'auth_token' => 'Bearer ' . $tokenResult->accessToken,
                'expires_at' => Carbon::parse($tokenResult->token->expires_at)->toDateTimeString()
            ]
        ]);
    }

    /**
     * store provider name info
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function signup_name(Request $request)
    {
        $request->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'phone' => [
                'required',
                Rule::unique('users')->where(function ($query) {
                    return $query->whereRole(UserType::PROVIDER)->where('phone_verification', true)->whereNull('deleted_at');
                })
            ],
            'spend_each_month' => 'required'
        ]);

        $user = $request->user();
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->phone = $request->phone;
        $user->spend_each_month = $request->spend_each_month;
        $user->save();

        return response()->json([
            'error' => false,
            'data' => $this->_user->providerProfile($request->user()->id, True, false),
            'message' => 'success'
        ]);
    }

    /**
     * Provider get their profile and update
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function profile(Request $request)
    {

        switch ($request->method()) {
            case 'POST':
                $request->validate([
                    'type' => 'required',
                    'street_address' => 'required',
                    'state' => 'required',
                    'city' => 'required',
                    'bio' => 'min:20|max:500',
                    // 'hourly_rate' => 'max:3',
                ]);

                $user = $request->user();
                if (isset($request->image)) {
                    if (gettype($request->image) == 'string') {
                        $request->image && $user->image = $request->image;
                    } else {
                        $this->helper->delete_media($user->image);
                        $url = $this->helper->store_media($request->image, 'provider/profile');
                        $request->image && $user->image = $url;
                    }
                }
                $user->zip_code = $request->zip_code;
                $user->provider_type = $request->type;
                $request->bio && $user->bio = $request->bio;

                $profile = ProviderProfile::where('provider_id', $user->id)->first();
                if ($profile === null) {
                    $profile = new ProviderProfile();
                    dispatch(new UserRegisterJob($user));
                }
                $profile->provider_id = $user->id;
                $profile->street_address = $request->street_address;
                $profile->suite_number = $request->suite_number;
                $profile->country = $request->country;
                $profile->state = $request->state;
                $profile->city = $request->city;
                $profile->hourly_rate = $request->hourly_rate ?? null;

                if ($request->type == 'Individual') {
                    $request->validate([
                        'first_name' => 'required',
                        'last_name' => 'required',
                        'dob' => 'required',
                    ]);

                    $user->first_name = $request->first_name;
                    $user->last_name = $request->last_name;
                    $profile->dob = $request->dob;
                } else {
                    // $request->service_type = 'MULTIPLE';
                    $request->validate([
                        'business_name' => 'required',
                        'founded' => 'required',
                        'number_of_employees' => 'required',
                    ]);

                    $profile->business_name = $request->business_name;
                    $profile->founded_year = $request->founded;
                    $profile->number_of_employees = $request->number_of_employees;
                }

                $user->save();
                $profile->save();

                return response()->json(['error' => false, 'message' => 'success', 'data' => $profile]);

            case 'GET':
                $data = $this->_user->providerProfile($request->user()->id, True);
                if ($data !== false) {
                    return response()->json([
                        'error' => false,
                        'message' => HttpStatusCode::$statusTexts[HttpStatusCode::OK],
                        'data' => $data
                    ], HttpStatusCode::OK);
                } else {
                    return response()->json([
                        'error' => true,
                        'message' => HttpStatusCode::$statusTexts[HttpStatusCode::NOT_FOUND]
                    ], HttpStatusCode::NOT_FOUND);
                }
            default:
                return response()->json([
                    'error' => true,
                    'message' => HttpStatusCode::$statusTexts[HttpStatusCode::METHOD_NOT_ALLOWED]
                ]);
        }
    }

    /**
     * upload provider profile image
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function profileImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg|max:4096',
        ]);
        try {
            $image = $request->image;
            $image = $this->helper->store_media($image, 'provider/profile');
            return response()->json([
                'error' => false,
                'message' => 'success',
                'data' => $image
            ], HttpStatusCode::OK);
        } catch (\Exception $e) {
            Log::error('AuthController -> profileImage', [$e->getMessage()]);
            return response()->json([
                'error' => true,
                'message' => HttpStatusCode::$statusTexts[HttpStatusCode::INTERNAL_SERVER_ERROR]
            ], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * get device token for notification
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function deviceToken(Request $request)
    {
        $request->validate([
            'device_token' => 'required',
            'os_platform' => 'nullable|numeric|in:0,1,2',
        ]);

        try {
            $user = $request->user();
            $user->device_token = $request->device_token;
            $user->os_platform = $request->os_platform;
            $user->save();
            return response()->json([
                'error' => false,
                'data' => $user,
                'message' => HttpStatusCode::$statusTexts[HttpStatusCode::OK]
            ], HttpStatusCode::OK);
        } catch (\Exception $e) {
            Log::error('AuthController -> deviceToken', [$e->getMessage()]);
            return response()->json([
                'error' => true,
                'message' => HttpStatusCode::$statusTexts[HttpStatusCode::INTERNAL_SERVER_ERROR]
            ], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Forgot Password
     * @param Request $request
     */
    public function forgotPassword(Request $request)
    {
        if ($request->phone) {
            $request->validate(['phone' => 'required']);
            $user = User::where('phone', $request->phone)->provider()->first();
            // if (is_numeric($request->user_name)) {
            // } else {
            //     $user = User::where('email', $request->user_name)->first();
            // }
            if ($user === null) {
                return response()->json(['error' => true, 'message' => 'This phone number is not exist'], HttpStatusCode::NOT_FOUND);
            }

            $otp = rand(1000, 9999);
            $ph_ver = PhoneVerification::updateOrCreate(['phone' => $user->phone], ['otp' => $otp]);

            $message = 'Forget password. Use this otp to verify your phone number. ' . $ph_ver->otp;

            $res = $this->helper->send_sms($ph_ver->phone, $message);

            if ($res['error']) {
                $ph_ver->delete();
                return response()->json(['error' => true, 'message' => $res['message']], 401);
            }

            return response()->json(['error' => false, 'opt' => $otp, 'message' => 'Otp has been sent on your phone.'], 200);
        }
        if ($request->email) {
            $user = $this->_user->where('email', $request->email)->provider()->first();
            if (!$user) {
                return response()->json(['error' => true, 'message' => 'This email is not exist'], HttpStatusCode::NOT_FOUND);
            }
            $otp = rand(1000, 9999);
            $email_ver = PhoneVerification::updateOrCreate(['email' => $user->email], ['otp' => $otp]);
            dispatch(new SendEmailOtpJob($email_ver, "Forget"));
            return response()->json(['error' => false, 'opt' => $otp, 'message' => 'Otp has been sent on your email.'], 200);
        }
        return $this->error('please enter email or phone', HttpStatusCode::BAD_REQUEST);
    }

    /**
     * Change Password
     *
     * @param Request $request
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'password' => 'required|confirmed|min:6',
            "token" => "required",
        ]);
        try {
            $user = null;
            if ($request->phone) {
                $user = User::where('phone', $request->phone)->provider()->first();
            }
            if ($request->email) {
                $user = User::where('email', $request->email)->provider()->first();
            }

            if ($user === null) {
                return response()->json(['error' => true, 'message' => 'Provider is not exist'], HttpStatusCode::NOT_FOUND);
            }
            if (!Password::tokenExists($user, $request->token)) {
                return response()->json(['error' => true, 'message' => 'Invalid token'], HttpStatusCode::FORBIDDEN);
            }

            Password::reset(['email' => $user->email, 'role' => UserType::PROVIDER, 'token' => $request->token, 'password' => $request->password], function ($user, $password) {
                $user->password = bcrypt($password);
                $user->save();
            });

            Password::deleteToken($user);
            return response()->json([
                'error' => false,
                'message' => HttpStatusCode::$statusTexts[HttpStatusCode::OK]
            ], HttpStatusCode::OK);
        } catch (\Throwable $th) {
            Log::error('AuthController -> changePassword', [$th->getMessage()]);
            return response()->json([
                'error' => true,
                'message' => HttpStatusCode::$statusTexts[HttpStatusCode::INTERNAL_SERVER_ERROR]
            ], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Delete provider
     *
     * @param Request $request
     */
    public function delete(Request $request)
    {
        $request->validate([
            'password' => 'required|confirmed',
        ]);
        try {
            $user = $request->user();
            if (!Hash::check($request->password, $user->password)) {
                return response()->json(['error' => true, 'message' => 'Invalid password'], HttpStatusCode::FORBIDDEN);
            }
            $deleted = $user->delete();
            $user->token()->delete();
            if ($deleted) {
                return response()->json([
                    'error' => false,
                    'message' => HttpStatusCode::$statusTexts[HttpStatusCode::OK]
                ], HttpStatusCode::OK);
            } else {
                return response()->json([
                    'error' => true,
                    'message' => HttpStatusCode::$statusTexts[HttpStatusCode::NOT_FOUND]
                ], HttpStatusCode::NOT_FOUND);
            }
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => HttpStatusCode::$statusTexts[HttpStatusCode::INTERNAL_SERVER_ERROR]
            ], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }

    /**
     *  _create_otp function
     * @param string $phone
     * @param string $type
     * @return array
     */
    private function _create_otp(string $phone, string $type)
    {
        $otp = rand(1000, 9999);
        $has = PhoneVerification::updateOrCreate([$type => $phone], ['otp' => $otp, 'is_verified' => 0]);

        $message = "Welcome to FareNow. Use this otp to verify your {$type}. " . $has->otp;
        return [
            'mesage' => $message,
            'has' => $has
        ];
    }
}
