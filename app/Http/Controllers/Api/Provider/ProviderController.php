<?php

namespace App\Http\Controllers\Api\Provider;

use App\Models\User;
use App\Models\Schedule;
use Illuminate\Http\Request;
use App\Utils\HttpStatusCode;
use App\Models\ProviderProfile;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Requests\ScheduleStoreRequest;
use phpDocumentor\Reflection\Types\Boolean;

class ProviderController extends Controller
{

    /**
     * @var User $_user
     * @var Schedule $_schedule
     * @var ProviderProfile $_providerProfile
     * @access private
     */
    private $_user, $_schedule, $_providerProfile;


    /**
     * Create a new controller instance.
     * @param  \App\Models\User $user
     * @param  \App\Models\Schedule $schedule
     * @param  \App\Models\ProviderProfile $providerProfile
     * @return void
     */
    public function __construct(User $user, Schedule $schedule, ProviderProfile $providerProfile)
    {
        $this->_user = $user;
        $this->_schedule = $schedule;
        $this->_providerProfile = $providerProfile;
    }

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $provider = $this->_user->providerList($request->all());

            if ($provider) 
                return response()->json([
                    'error' => false,
                    'message' => 'success',
                    'data' => $provider
                ], HttpStatusCode::OK);
            else 
                return response()->json([
                    'error' => true,
                    'message' => HttpStatusCode::$statusTexts[HttpStatusCode::NOT_FOUND]
                ], HttpStatusCode::NOT_FOUND);
            
        } catch (\Exception $e) {
            Log::error('Provider:ProviderController -> index', [$e->getMessage()]);
            return response()->json([
                'error' => true,
                'message' => HttpStatusCode::$statusTexts[HttpStatusCode::INTERNAL_SERVER_ERROR]
            ], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * provider payment change
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return JsonResponse
     */
    public function paymentUpdate(Request $request): JsonResponse
    {
        $request->validate([
            'hourly_rate' => 'max:3',
            'starting_rate' => 'max:3',
        ]);
        try {
            $user = $request->user();
            $providerProfile = $this->_providerProfile->where('provider_id', $user->id)->first();
            if ($providerProfile) {
                if (isset($request->type)) {
                    $providerProfile->starting_rate = $request->starting_rate;
                } else {
                    $request->hourly_rate ? $user->account_type = 'PREMIUM' : $user->account_type = 'BASIC';
                    $user->save();
                    $providerProfile->hourly_rate = $request->hourly_rate ? $request->hourly_rate : null;
                }
                $providerProfile->save();
                return response()->json([
                    'error' => false,
                    'message' => 'success', 'data' => $providerProfile
                ], HttpStatusCode::OK);
            } else {
                return response()->json([
                    'error' => true,
                    'message' => HttpStatusCode::$statusTexts[HttpStatusCode::NOT_FOUND]
                ], HttpStatusCode::NOT_FOUND);
            }
        } catch (\Exception $e) {
            Log::error('Provider:ProviderController -> paymentUpdate', [$e->getMessage()]);
            return response()->json([
                'error' => true,
                'message' => HttpStatusCode::$statusTexts[HttpStatusCode::INTERNAL_SERVER_ERROR]
            ], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * showCredit
     * @return JsonResponse
     */
    public function showCredit(): JsonResponse
    {
        try {
            $user = $this->_user->find(auth()->user()->id);
            if ($user !== null && isset($user->credit)) {
                return response()->json([
                    'error' => false,
                    'message' => 'success', 'data' => $user->credit
                ], HttpStatusCode::OK);
            } else {
                return response()->json([
                    'error' => true,
                    'message' => HttpStatusCode::$statusTexts[HttpStatusCode::NOT_FOUND]
                ], HttpStatusCode::NOT_FOUND);
            }
        } catch (\Exception $e) {
            Log::error('Provider:ProviderController -> showRequest', [$e->getMessage()]);
            return response()->json([
                'error' => true,
                'message' => HttpStatusCode::$statusTexts[HttpStatusCode::INTERNAL_SERVER_ERROR]
            ], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse|null
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        try {

            $data = $this->_user->providerProfile($id);
            if ($data)
                return response()->json([
                    'error' => false,
                    'message' => HttpStatusCode::$statusTexts[HttpStatusCode::OK],
                    'data' => $data
                ], HttpStatusCode::OK);
            else
                return response()->json([
                    'error' => true,
                    'message' => HttpStatusCode::$statusTexts[HttpStatusCode::NOT_FOUND]
                ], HttpStatusCode::NOT_FOUND);
        } catch (\Exception $e) {
            Log::error('Provider:ProviderController -> show', [$e->getMessage()]);
            return response()->json([
                'error' => true,
                'message' => HttpStatusCode::$statusTexts[HttpStatusCode::INTERNAL_SERVER_ERROR]
            ], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\User $user
     * @return JsonResponse|null
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
     * @return JsonResponse|null
     */
    public function update(Request $request, User $user)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Question  $question
     * @return JsonResponse|null
     */
    public function destroy(User $user)
    {
        //
    }
}
