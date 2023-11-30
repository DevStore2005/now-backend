<?php

namespace App\Http\Controllers\Api\Provider;

use App\Models\User;
use App\Models\Feedback;
use Illuminate\Http\Request;
use App\Utils\HttpStatusCode;
use App\Models\ServiceRequest;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class FeedbackController extends Controller
{
    /**
     * @var Feedback $feedback
     * @var ServiceRequest $serviceRequest
     * @var User $user
     */
    private $_feedback, $_serviceRequest, $_user;

    /**
     * Create a new controller instance.
     * @param  \App\Http\Helpers\Common $helper
     * @return void
     */
    public function __construct(Feedback $feedback, User $user, ServiceRequest $serviceRequest)
    {
        $this->_feedback = $feedback;
        $this->_user = $user;
        $this->_serviceRequest = $serviceRequest;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response|null
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'service_request_id' => 'required',
            'user_id' => 'required',
            'comment' => 'required|min:1|max:40',
            'rating' => 'required|min:1|max:1'
        ]);
        try {
            $provider = $request->user();
            $serviceRequest = $this->_serviceRequest->where('user_id', $request->user_id)
            ->where('provider_id', $provider->id)
            ->find($request->service_request_id);
            
            if ($serviceRequest === null) {
                return response()->json([
                    'error' => true,
                    'message' => HttpStatusCode::$statusTexts[HttpStatusCode::NOT_FOUND]
                ], HttpStatusCode::NOT_FOUND);
            }

            if ($serviceRequest->provider_id != $provider->id) {
                return response()->json([
                    'error' => true,
                    'message' => HttpStatusCode::$statusTexts[HttpStatusCode::FORBIDDEN]
                ], HttpStatusCode::FORBIDDEN);
            }

            if ($serviceRequest->is_completed == false) {
                return response()->json([
                    'error' => true,
                    'message' => 'Service is not completed',
                    'data' => $serviceRequest
                ], HttpStatusCode::OK);
            }

            $feedback = $this->_feedback->where('provider_id', $provider->id)
            ->where('service_request_id', $request->service_request_id)
            ->first();
            
            if ($feedback !== null) {
                return response()->json([
                    'error' => true,
                    'message' => 'Already have feedback about this service',
                    'data' => $feedback
                ], HttpStatusCode::OK);
            }
            
            $feedback = $this->_feedback->create([
                'service_request_id' => $request->service_request_id,
                'provider_id' => $provider->id,
                'comment' => $request->comment,
                'rating' => $request->rating
            ]);
            
            $rating = $this->_feedback->select('rating')->whereHas('service_request', function ($q) use ($request) {
                return $q->where('user_id', $request->user_id);
            })->avg('rating');
            
            $user = $this->_user->find($request->user_id);
            $user->rating = $rating;
            $user->save();
            
            return response()->json([
                'error' => false,
                'data' => $feedback,
                'message' => HttpStatusCode::$statusTexts[HttpStatusCode::OK]
            ], HttpStatusCode::OK);
        } catch (\Exception $e) {
            Log::error('FeedbackController -> store', [$e->getMessage()]);
            return response()->json([
                'error' => true,
                'message' => HttpStatusCode::$statusTexts[HttpStatusCode::INTERNAL_SERVER_ERROR]
            ], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Feedback  $feedback
     * @return \Illuminate\Http\Response|null
     */
    public function show(Feedback $feedback)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Feedback  $feedback
     * @return \Illuminate\Http\Response|null
     */
    public function edit(Feedback $feedback)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Feedback  $feedback
     * @return \Illuminate\Http\Response|null
     */
    public function update(Request $request, Feedback $feedback)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Feedback  $feedback
     * @return \Illuminate\Http\Response|null
     */
    public function destroy(Feedback $feedback)
    {
        //
    }
}
