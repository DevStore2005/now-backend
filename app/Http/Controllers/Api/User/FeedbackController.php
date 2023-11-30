<?php

namespace App\Http\Controllers\Api\User;

use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use App\Models\Feedback;
use Illuminate\Http\Request;
use App\Utils\HttpStatusCode;
use App\Models\ServiceRequest;
use App\Models\BusinessProfile;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Resources\FeedbacksResource;

class FeedbackController extends Controller
{
    private $_order, $_feedback, $_serviceRequest, $_user, $_product, $_business_profile;
    /**
     * Create a new controller instance.
     * @param Feedback $feedback
     * @param User $user
     * @param ServiceRequest $serviceRequest
     * @param Order $order
     * @param Product $product
     * @param BusinessProfile $businessProfile
     * @return void
     */
    public function __construct(Feedback $feedback, User $user, ServiceRequest $serviceRequest, Order $order, Product $product, BusinessProfile $businessProfile)
    {
        $this->_feedback = $feedback;
        $this->_user = $user;
        $this->_serviceRequest = $serviceRequest;
        $this->_order = $order;
        $this->_product = $product;
        $this->_business_profile = $businessProfile;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse|FeedbacksResource
     */
    public function index(Request $request)
    {
        $request->validate([
            'provider_id' => 'required|exists:users,id',
        ]);
        try {

            $ratings = $this->_feedback->ratings($request->provider_id);
            $feedbacks = $this->_feedback
                ->where('for_user_id', $request->provider_id)
                ->with('user:id,first_name,last_name,image')
                ->paginate(10);
            if ($feedbacks->isNotEmpty()) {
                $feedbacks->ratings = $ratings;
                return new FeedbacksResource($feedbacks);
            } else {
                return response()->json([
                    'error' => true,
                    'message' => 'No feedbacks found',
                ], HttpStatusCode::NOT_FOUND);
            }
        } catch (\Exception $e) {
            Log::error('FeedbackController::index: ' . $e->getMessage());
            return response()->json([
                'error' => true,
                'message' => 'Something went wrong',
            ], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $user = $request->user();
        if ($request->has('order_id')) {
            $request->validate([
                'comment' => 'min:1',
                'rating' => 'required|min:1|max:1'
            ]);
            $order = $this->_order->find($request->order_id);
            $type = $order->food_id !== null ? 'food_id' : 'product_id';
            if (!$order) {
                return response()->json([
                    'error' => true,
                    'message' => 'Order not found',
                ], HttpStatusCode::NOT_FOUND);
            }
            if ($order->user_id != $user->id) {
                return response()->json([
                    'error' => true,
                    'message' => 'You are not allowed to rate this order',
                ], HttpStatusCode::FORBIDDEN);
            }
            $feedback = $this->_feedback->where('order_id', $order->id)->first();
            if ($feedback) {
                return response()->json([
                    'error' => true,
                    'message' => 'You already rated this order',
                ], HttpStatusCode::CONFLICT);
            }
            $feedback = $this->_feedback->create([
                'order_id' => $order->id,
                'user_id' => $user->id,
                'rating' => $request->rating,
                'comment' => isset($request->comment) ? $request->comment : null,
                $type => $order->$type
            ]);
            $rating = $this->_feedback->select('rating')->where($type, $order->$type)->avg('rating');
            $product = $this->_product->find($order->$type)->update(['rating' => $rating]);
            $findProducts = $type == 'food_id' ? ['restaurant_id' => $product->restaurant_id] : ['grocer_id' => $product->grocer_id];
            $avgRating = $this->_product->select('rating')->where($findProducts)->avg('rating');
            $this->_business_profile->find($type == 'food_id' ? $product->restaurant_id : $product->grocer_id)->update(['rating' => $avgRating]);
            return response()->json([
                'error' => false,
                'message' => 'Feedback created successfully',
                'data' => $feedback
            ], HttpStatusCode::CREATED);
        }

        if ($request->has('service_request_id') || $request->has('provider_id')) {
            $request->validate([
                'service_request_id' => 'required',
                'provider_id' => 'required',
                'comment' => 'min:1',
                'rating' => 'required|min:1|max:1'
            ]);
            try {

                $serviceRequest = $this->_serviceRequest->where('user_id', $user->id)
                    ->where('provider_id', $request->provider_id)->find($request->service_request_id);

                if ($serviceRequest === null) {
                    return response()->json([
                        'error' => true,
                        'message' => HttpStatusCode::$statusTexts[HttpStatusCode::NOT_FOUND]
                    ], HttpStatusCode::NOT_FOUND);
                }

                if ($serviceRequest->user_id != $user->id) {
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

                $feedback = $this->_feedback->where('user_id', $user->id)
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
                    'user_id' => $user->id,
                    'for_user_id' => $serviceRequest->provider_id,
                    'comment' => $request->comment,
                    'rating' => $request->rating
                ]);
                $rating = $this->_feedback->select('rating')->whereHas('service_request', function ($q) use ($request) {
                    return $q->where('provider_id', $request->provider_id);
                })->avg('rating');

                $provider = $this->_user->find($request->provider_id);
                $provider->rating = $rating;
                $provider->save();
                $feedback->load('service_request', 'service_request.provider', 'service_request.user_feeback');
                return response()->json([
                    'error' => false,
                    'data' => $feedback,
                    'message' => 'Feedback created successfully',
                    'status' => HttpStatusCode::$statusTexts[HttpStatusCode::OK]
                ], HttpStatusCode::OK);
            } catch (\Exception $e) {
                Log::error('FeedbackController -> store', [$e->getMessage()]);
                return response()->json([
                    'error' => true,
                    'message' => HttpStatusCode::$statusTexts[HttpStatusCode::INTERNAL_SERVER_ERROR]
                ], HttpStatusCode::INTERNAL_SERVER_ERROR);
            }
        }
        return response()->json([
            'error' => true,
            'message' => HttpStatusCode::$statusTexts[HttpStatusCode::BAD_REQUEST]
        ], HttpStatusCode::BAD_REQUEST);
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
