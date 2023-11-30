<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Resources\ProviderResource;
use App\Http\Resources\ProvidersResource;
use App\Models\User;
use App\Utils\UserType;
use App\Models\Feedback;
use Illuminate\Http\Request;
use App\Utils\HttpStatusCode;
use App\Models\ProviderProfile;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Utils\MyAppEnv;

class ProviderController extends Controller
{
    /**
     *
     * @var \App\Models\User $_user
     * @var \App\Models\ProviderProfile $_roviderProfile
     * @var \App\Models\Feedback $_feedback
     * @var string $_environment
     */
    private $_user, $_providerProfile, $_feedback, $_environment;

    /**
     * Create a new controller instance.
     * @param  \App\Models\User $user
     * @param  App $app
     * @return void
     */
    public function __construct(User $user, ProviderProfile $providerProfile, Feedback $feedback, App $app)
    {
        $this->_user = $user;
        $this->_providerProfile = $providerProfile;
        $this->_feedback = $feedback;
        $this->_environment = $app::environment();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse|ProvidersResource
     */
    public function index(Request $request)
    {
        try {
            $provider = $this->_user->providerList($request->query());
            if ($provider && $provider->isNotEmpty()) {
                return new ProvidersResource($provider);
            } else {
                return response()->json([
                    'error' => true,
                    'message' => "No provider found"
                ], HttpStatusCode::NOT_FOUND);
            }
        } catch (\Exception $e) {
            Log::error('ProviderController -> index', [$e->getMessage()]);
            return response()->json([
                'error' => true,
                'message' => $this->_environment === MyAppEnv::LOCAL ? $e->getMessage() : HttpStatusCode::$statusTexts[HttpStatusCode::INTERNAL_SERVER_ERROR]
            ], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
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
     * @return \Illuminate\Http\JsonResponse|ProviderResource
     */
    public function show($id)
    {
        try {
            $profile = $this->_user->providerProfile(intVal($id));
            if (isset($profile['provider']))
                return new ProviderResource($profile);
            else
                return response()->json([
                    'error' => true,
                    'message' => HttpStatusCode::$statusTexts[HttpStatusCode::NOT_FOUND]
                ], HttpStatusCode::NOT_FOUND);
        } catch (\Exception $e) {
            Log::error('ProviderController -> show', [$e->getMessage()]);
            return response()->json([
                'error' => true,
                'message' => $this->_environment === MyAppEnv::LOCAL ? $e->getMessage() : HttpStatusCode::$statusTexts[HttpStatusCode::INTERNAL_SERVER_ERROR]
            ], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\JsonResponse|ProviderResource
     */
    public function showByUsername($username)
    {
        $user = User::query()->where('username', $username)->first('id');
        if (!$user) {
            return response()->json([
                'error' => true,
                'message' => HttpStatusCode::$statusTexts[HttpStatusCode::NOT_FOUND]
            ], HttpStatusCode::NOT_FOUND);
        }
        return $this->show($user->id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\User $user
     * @return \Illuminate\Http\Response|null
     */
    public function edit(User $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User $user
     * @return \Illuminate\Http\Response|null
     */
    public function update(Request $request, User $user)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Question  $question
     * @return \Illuminate\Http\Response|null
     */
    public function destroy(User $user)
    {
        //
    }
}
