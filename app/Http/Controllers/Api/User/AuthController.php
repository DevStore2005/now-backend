<?php

namespace App\Http\Controllers\Api\User;

use App\Models\Address;
use Carbon\Carbon;
use App\Models\User;
use App\Utils\AppConst;
use App\Utils\UserType;
use App\Http\Helpers\Common;
use Illuminate\Http\Request;
use App\Jobs\SendEmailOtpJob;
use App\Jobs\UserRegisterJob;
use App\Utils\HttpStatusCode;
use Illuminate\Validation\Rule;
use App\Models\PhoneVerification;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Password;
use App\Http\Requests\UserSignupEmailRequest;

class AuthController extends Controller
{

    /**
     * @var Common $_helper
     * @var User $_user
     * @var PhoneVerification $_phoneVerification
     * @var Rule $_rule
     * @var string $_environment
     */
    private $_user, $_vehicleType, $_vehicle, $_phoneVerification, $_rule, $_helper, $_environment;

    /**
     * Create a new controller instance.
     * @param Common $helper
     * @param PhoneVerification $phoneVerification
     * @param Rule $rule
     * @param User $user
     * @param Storage $storage
     * @param App $app
     * @return void
     */
    public function __construct(Common $helper, PhoneVerification $phoneVerification, Rule $rule, User $user, Storage $storage, App $app)
    {
        $this->_helper = $helper;
        $this->_phoneVerification = $phoneVerification;
        $this->_rule = $rule;
        $this->_user = $user;
        $this->_environment = $app::environment();
    }

    /**
     * User signup using phone number
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function signupPhone(Request $request)
    {
        $request->validate([
            'phone' => [
                'required',
                Rule::unique('users')->where(function ($query) {
                    return $query->whereRole(UserType::USER)
                        ->where('phone_verification', true)
                        ->whereNull('deleted_at');
                })
            ]
        ]);

        $createOtp = $this->_create_otp($request->phone, "phone");

        $res = $this->_helper->send_sms($createOtp['has']->phone, $createOtp['message']);

        if ($res['error']) {
            $createOtp['has']->delete();
            return response()->json(['error' => true, 'message' => $res['message']], 401);
        }

        return response()->json(['error' => false, 'opt' => $createOtp['has'], 'message' => 'Otp has been sent on your phone. Please verify your phone number.'], 200);
    }

    /**
     * User signup using email
     * @param UserSignupEmailRequest $request
     * @return JsonResponse
     */
    public function signupEmail(UserSignupEmailRequest $request)
    {
        try {
            $createOtp = $this->_create_otp($request->email, "email");
            dispatch(new SendEmailOtpJob($createOtp['has']));
            return response()->json(['error' => false, 'opt' => $createOtp['has'], 'message' => 'Otp has been sent on your Email. Please verify your Email.'], 200);
        } catch (\Exception $e) {
            Log::error('Error in signupEmail: ' . [$e->getMessage(), $e->getLine(), $e->getFile()]);
            return response()->json([
                'error' => true,
                'message' => 'Something went wrong. Please try again later.'
            ], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * send otp to user phone number to verify phone number
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function sendOtp(Request $request)
    {
        $request->validate([
            'phone' => [
                'required',
                Rule::unique('users')->where(function ($query) {
                    return $query->whereRole(UserType::USER)->where('phone_verification', true)->whereNull('deleted_at');
                })
            ],
            'email' => [
                'required', 'email:rfc,dns'
            ]
        ]);

        $user = $this->_user->where('email', $request->email)->user()->first();
        if (!$user) {
            return response()->json(['error' => true, 'message' => 'User not found.'], 401);
        }

        $otp = rand(1000, 9999);
        $ph_ver = $this->_phoneVerification->updateOrCreate(['phone' => $request->phone], ['otp' => $otp]);

        $message = 'FareNow. Use this otp to verify your phone number. ' . $ph_ver->otp;

        $res = $this->_helper->send_sms($ph_ver->phone, $message);

        if ($res['error']) {
            $ph_ver->delete();
            return response()->json(['error' => true, 'message' => $res['message']], 401);
        }

        return response()->json(['error' => false, 'opt' => $otp, 'message' => 'Otp has been sent on your phone. Please verify your phone number.'], 200);
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
     * Verify phone number
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function signupPhoneVerify(Request $request)
    {
        $request->validate([
            'email' => ['required_if:for_verification,true', 'email:rfc,dns'],
            'phone' => ['required'],
            'otp' => ['required', 'min:4', 'max:4']
        ]);

        $check = $this->_phoneVerification->where('phone', $request->phone)
            ->where('otp', $request->otp)
            ->first();
        $token = [];
        if (!empty($check)) {
            $check->is_verified = true;
            $check->save();
            if ($request->for_password) {
                $user = $this->_user->where('phone', $request->phone)->where('role', UserType::USER)->first();
                if ($user) {
                    $token = ["token" => Password::getRepository()->create($user)];
                }
            }
            if ($request->for_verification) {
                $user = $this->_user->query()
                    ->where('email', $request->email)
                    ->where('phone_verification', false)
                    ->where('role', UserType::USER)->first();
                if ($user) {
                    $user->phone = $request->phone;
                    $user->phone_verification = true;
                    $user->save();
                    $tokenResult = $user->createToken('Personal Access Token');
                    $token = $tokenResult->token;
                    $token->expires_at = Carbon::now()->addYear();
                    $token->save();
                    dispatch(new UserRegisterJob($user));
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
            }
            return response()->json(['error' => false, 'message' => 'Phone verified.'] + $token, 200);
        } else {
            return response()->json(['error' => true, 'message' => 'Invalid Otp.'], 401);
        }
    }

    public function signupEmailVerify(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email:rfc,dns'],
            'otp' => ['required', 'min:4', 'max:4']
        ]);

        $check = $this->_phoneVerification->where('email', $request->email)
            ->where('otp', $request->otp)
            ->first();
        $token = [];
        if (!empty($check)) {
            $check->is_verified = true;
            $check->save();
            if ($request->for_password) {
                $user = $this->_user->where('email', $request->email)->where('role', UserType::USER)->first();
                if ($user) {
                    $token = ["token" => Password::getRepository()->create($user)];
                }
            }
            if ($request->for_verification) {
                $user = $this->_user->query()
                    ->where('email', $request->email)
                    ->whereNull('email_verified_at')
                    ->where('role', UserType::USER)->first();
                if ($user) {
                    $user->email = $request->email;
                    $user->email_verified_at = Carbon::now();
                    $user->save();
                    $tokenResult = $user->createToken('Personal Access Token');
                    $token = $tokenResult->token;
                    $token->expires_at = Carbon::now()->addYear();
                    $token->save();
                    dispatch(new UserRegisterJob($user));
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
            }
            return response()->json(['error' => false, 'message' => 'Email verified.'] + $token, 200);
        } else {
            return response()->json(['error' => true, 'message' => 'Invalid Otp.'], 401);
        }
    }

    /**
     * Craete new user after phone verification
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function signup(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:25|min:2',
            'last_name' => 'required|string|max:25|min:2',
            'email' => [
                'required', 'email:rfc,dns',
                Rule::unique('users')->where(function ($query) {
                    return $query->whereRole(UserType::USER)->where('phone_verification', true)->whereNull('deleted_at');
                }),
                Rule::exists('phone_verifications')->where(function ($query) {
                    return $query->where('is_verified', true);
                })
            ],
            'password' => 'required|confirmed|min:6',
            'phone' => [
                'required',
                $this->_rule->unique('users')->where(function ($query) {
                    return $query->whereRole(UserType::USER)->where('phone_verification', true)->whereNull('deleted_at');
                }),
            ],
            'image' => 'regex:/^(.+)\/([^\/]+)$/',
            'bio' => 'min:20|max:100'
        ]);


        $this->_user->first_name = $request->first_name;
        $this->_user->last_name = $request->last_name;
        $this->_user->zip_code = $request->zip_code ?? null;
        $this->_user->email = $request->email;
        $this->_user->country_id = $request->country_id ?? null;
        $this->_user->phone = $request->phone ?? 0;
        $this->_user->email_verified_at = Carbon::now();
        if (isset($request->image)) {
            $this->_user->image = $request->image;
        }
        if (isset($request->bio)) {
            $this->_user->bio = $request->bio;
        }
        $this->_user->password = bcrypt($request->password);
        $this->_user->role = UserType::USER;
        $this->_user->status = AppConst::ACTIVE;
        $this->_user->save();

        Auth::loginUsingId($this->_user->id);

        $user = $request->user();

        if ($request->address) {
            Address::create([
                'user_id' => $user->id,
                'type' => "OTHER",
                'address' => $request->address,
                'flat_no' => null,
                'zip_code' => $request->zip_code ?? null,
                'state' => $request->state ?? null,
                'city' => $request->zip_code ?? null,
            ]);
        }

        $tokenResult = $user->createToken('Personal Access Token');

        $token = $tokenResult->token;
        $token->expires_at = Carbon::now()->addYear();
        $token->save();

        $this->_phoneVerification->where('phone', $request->phone)->delete();
        // dispatch(new UserRegisterJob($user));
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
     * User Login
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

        $user_name = $request->user_name;

        $params = [];

        if (is_numeric($request->get('user_name'))) {
            $params = ['phone' => $user_name, 'password' => $request->password];
        } elseif (filter_var($user_name, FILTER_VALIDATE_EMAIL)) {
            $params = ['email' => $user_name, 'password' => $request->password];
        } else {
            return response()->json(['error' => true, 'message' => 'Invalid user name.'], 401);
        }

        $params['role'] = UserType::USER;

        if (!Auth::attempt($params)) {
            $user = $this->_user->where('email', $user_name)->where('role', UserType::USER)->first();
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
                'user' => $user,
                'auth_token' => 'Bearer ' . $tokenResult->accessToken,
                'expires_at' => Carbon::parse($tokenResult->token->expires_at)->toDateTimeString()
            ]
        ]);
    }

    /**
     * Obtain the user information from Google.
     *
     * @param Request $request
     * @param string $provider
     *
     * @return JsonResponse
     */
    public function handleProviderCallback($provider, Request $request)
    {
        try {
            // $request->validate([

            // ])
            $token = $request->input('token');
            $country_id = $request->input('country_id', null);
            $response = null;
            if ($token) {
                $curl_handle = curl_init();
                if ($provider == 'google') {
                    curl_setopt($curl_handle, CURLOPT_URL, 'https://oauth2.googleapis.com/tokeninfo?id_token=' . $token);
                }
                if ($provider == 'facebook') {
                    curl_setopt($curl_handle, CURLOPT_URL, "https://graph.facebook.com/v14.0/me?fields=name,email,first_name,last_name&access_token=" . $token);
                }
                curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 0);
                curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
                $response = json_decode(curl_exec($curl_handle));
                curl_close($curl_handle);
                if (!isset($response->id) && !isset($response->sub)) {
                    return response()->json(['error' => true, "message" => 'wrong ' . $provider . ' token / this ' . $provider . ' token is already expired.'], HttpStatusCode::UNAUTHORIZED);
                }
            }
            $id = null;
            if ($token) {
                $id ??= isset($response->sub) == true ? $response->sub : null;
                $id ??= isset($response->id) == true ? $response->id : null;
            } else {
                $id = $request->id;
                if (!$id) {
                    return response()->json(['error' => true, "message" => 'wrong ' . $provider . ' token / this ' . $provider . ' token is already expired.'], HttpStatusCode::UNAUTHORIZED);
                }
            }
            $user = User::where('social_id', $id)->whereRole(UserType::USER)->first();
            if ($user == null) {
                $userData = [
                    'role' => UserType::USER,
                    'status' => AppConst::ACTIVE,
                    'phone' => 000000000000,
                    'phone_verification' => false,
                    'password' => bcrypt($id . '@' . $provider . '.com')
                ];
                if ($provider == 'google' && $request->has('token')) {
                    $userData = [
                            'first_name' => $response->given_name,
                            'last_name' => $response->family_name,
                            'phone_verification' => false,
                            'email' => isset($response->email) ? $response->email : null,
                            'image' => $response->picture,
                        ] + $userData;
                } else if ($provider == 'facebook' && $request->has('token')) {
                    $userData = [
                            'first_name' => $response->first_name,
                            'last_name' => $response->last_name,
                            'email' => isset($response->email) ? $response->email : null,
                        ] + $userData;
                } else {
                    $userData = [
                            'first_name' => $request->first_name,
                            'last_name' => $request->last_name,
                            'phone' => 000000000000,
                            'email' => isset($request->email) ? $request->email : null,
                        ] + $userData;
                }
                $userData = [
                        'email_verified_at' => Carbon::now(),
                        'country_id' => $country_id ?? null,
                    ] + $userData;

                $user = User::create($userData);
            }
            if ($id) {
                if (($user->social_id != $id) || !$user->social_id) {
                    $user->social_id = $id;
                    $user->social_type = $provider;
                    $user->save();
                }
            }

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
                    'user' => $user,
                    'auth_token' => 'Bearer ' . $tokenResult->accessToken,
                    'expires_at' => Carbon::parse($tokenResult->token->expires_at)->toDateTimeString()
                ]
            ]);
        } catch (\Exception $e) {
            Log::error("AuthController->handleProviderCallback", [$e->getMessage()]);
            return response()->json([
                'error' => true,
                'message' => 'Something went wrong.'
            ], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * User profile
     *
     * @param int $id
     * @return JsonResponse
     */
    public function profile($id)
    {
        try {
            $profile = $this->_user->user()->find($id);
            if ($profile !== null) {
                return response()->json(['error' => false, 'message' => HttpStatusCode::$statusTexts[HttpStatusCode::OK], 'data' => $profile], HttpStatusCode::OK);
            } else {
                return response()->json(['error' => true, 'message' => HttpStatusCode::$statusTexts[HttpStatusCode::NOT_FOUND]], HttpStatusCode::NOT_FOUND);
            }
        } catch (\Exception $e) {
            Log::error('AuthController -> profile', [$e->getMessage()]);
            return response()->json(['error' => true, 'message' => HttpStatusCode::$statusTexts[HttpStatusCode::INTERNAL_SERVER_ERROR]], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * User can update profile
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function updateProfile(Request $request, $id)
    {
        $request->validate([
            'first_name' => 'required|string|max:25|min:2',
            'last_name' => 'required|string|max:25|min:2',
            // 'image' => 'regex:/^(.+)\/([^\/]+)$/',
            'bio' => 'min:20|max:100'
        ]);
        try {
            $profile = $this->_user->whereRole(UserType::USER)->find($id);

            if ($profile === null) {
                return response()->json([
                    'error' => true,
                    'message' => HttpStatusCode::$statusTexts[HttpStatusCode::NOT_FOUND]
                ], HttpStatusCode::NOT_FOUND);
            }

            if ($request->user()->can('update', $profile)) {
                $profile->first_name = $request->first_name;
                $profile->last_name = $request->last_name;
                $profile->zip_code = $request->zip_code ?? $profile->zip_code;
                $profile->image = isset($request->image) ? $request->image : $profile->image;
                $profile->bio = $request->bio;
                $profile->save();
                return response()->json([
                    'error' => false,
                    'data' => $profile,
                    'message' => HttpStatusCode::$statusTexts[HttpStatusCode::OK]
                ], HttpStatusCode::OK);
            }

            return response()->json([
                'error' => true,
                'message' => HttpStatusCode::$statusTexts[HttpStatusCode::FORBIDDEN]
            ], HttpStatusCode::FORBIDDEN);
        } catch (\Exception $e) {
            Log::error('AuthController -> updateProfile', [$e->getMessage()]);
            return response()->json(['error' => true, 'message' => HttpStatusCode::$statusTexts[HttpStatusCode::INTERNAL_SERVER_ERROR]], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * upload user profile image
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function profileImage(Request $request)
    {
        $request->validate([
            'image' => 'image|mimes:jpeg,png,jpg|max:4096',
        ]);
        try {
            switch ($request->method()) {
                case 'PATCH':
                    $user = $request->user();
                    $delete = null;
                    if ($user->image) {
                        $delete = $this->_helper->delete_media($user->image);
                    }
                    $path = 'user/profile';
                    $imagePath = $this->_helper->store_media($request->image, $path);
                    $user->image = $imagePath;
                    $res = $user->save();
                    return response()->json([
                        'error' => false,
                        'delete' => $delete,
                        'message' => HttpStatusCode::$statusTexts[HttpStatusCode::OK],
                        'data' => $imagePath
                    ], HttpStatusCode::OK);
                case 'POST':
                    $path = 'user/profile';
                    $imagePath = $this->_helper->store_media($request->image, $path);
                    return response()->json([
                        'error' => false,
                        'message' => HttpStatusCode::$statusTexts[HttpStatusCode::OK],
                        'data' => $imagePath
                    ], HttpStatusCode::OK);
                default:
                    return response()->json([
                        'error' => true,
                        'message' => HttpStatusCode::$statusTexts[HttpStatusCode::METHOD_NOT_ALLOWED]
                    ], HttpStatusCode::METHOD_NOT_ALLOWED);
            }
        } catch (\Exception $e) {
            Log::error('AuthController -> profileImage', [$e->getMessage()]);
            return response()->json(['error' => true, 'message' => HttpStatusCode::$statusTexts[HttpStatusCode::INTERNAL_SERVER_ERROR]], HttpStatusCode::INTERNAL_SERVER_ERROR);
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
            'os_platform' => 'nullable|numeric|in:0,1,2'
        ]);

        try {
            $user = $request->user();
            $user->device_token = $request->device_token;
            $user->os_platform = $request->os_platform;
            $user->save();
            return response()->json(['error' => false, 'data' => $user, 'message' => HttpStatusCode::$statusTexts[HttpStatusCode::OK]], HttpStatusCode::OK);
        } catch (\Exception $e) {
            Log::error('AuthController -> deviceToken', [$e->getMessage()]);
            return response()->json(['error' => true, 'message' => HttpStatusCode::$statusTexts[HttpStatusCode::INTERNAL_SERVER_ERROR]], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Change User Status
     * @param Request $request
     * @param User $user
     */
    public function changeStatus(Request $request, User $user)
    {
        $request->validate([
            'status' => 'required|in:' . AppConst::ACTIVE . ',' . AppConst::INACTIVE
        ]);
        try {
            $user = $user->find($request->id);
            if ($user === null) {
                return response()->json(['error' => true, 'message' => HttpStatusCode::$statusTexts[HttpStatusCode::NOT_FOUND]], HttpStatusCode::NOT_FOUND);
            }
            $user->status = $request->status;
            $user->save();
            return response()->json(['error' => true, 'message' => HttpStatusCode::$statusTexts[HttpStatusCode::FORBIDDEN]], HttpStatusCode::FORBIDDEN);
        } catch (\Exception $e) {
            Log::error('AuthController -> changeStatus', [$e->getMessage()]);
            return response()->json(['error' => true, 'message' => HttpStatusCode::$statusTexts[HttpStatusCode::INTERNAL_SERVER_ERROR]], HttpStatusCode::INTERNAL_SERVER_ERROR);
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
            $user = User::where('phone', $request->phone)->user()->first();
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

            $res = $this->_helper->send_sms($ph_ver->phone, $message);

            if ($res['error']) {
                $ph_ver->delete();
                return response()->json(['error' => true, 'message' => $res['message']], 401);
            }

            return response()->json(['error' => false, 'opt' => $otp, 'message' => 'Otp has been sent on your phone.'], 200);
        }
        if ($request->email) {
            $user = $this->_user->where('email', $request->email)->user()->first();
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
                $user = User::where('phone', $request->phone)->user()->first();
            }
            if ($request->email) {
                $user = User::where('email', $request->email)->user()->first();
            }

            if ($user === null) {
                return response()->json(['error' => true, 'message' => 'User is not exist'], HttpStatusCode::NOT_FOUND);
            }
            if (!Password::tokenExists($user, $request->token)) {
                return response()->json(['error' => true, 'message' => 'Invalid token'], HttpStatusCode::FORBIDDEN);
            }

            Password::reset(['email' => $user->email, 'role' => UserType::USER, 'token' => $request->token, 'password' => $request->password], function ($user, $password) {
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
     * Delete User
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
        $has = $this->_phoneVerification->updateOrCreate([$type => $phone], ['otp' => $otp, 'is_verified' => 0]);

        $message = "Welcome to FareNow. Use this otp to verify your {$type}. " . $has->otp;
        return [
            'message' => $message,
            'has' => $has
        ];
    }
}
