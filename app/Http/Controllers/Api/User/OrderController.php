<?php

namespace App\Http\Controllers\Api\User;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Order;
use App\Models\Credit;
use App\Utils\AppConst;
use App\Utils\MyAppEnv;
use App\Models\Transaction;
use App\Models\VehicleType;
use Illuminate\Http\Request;
use App\Utils\HttpStatusCode;
use App\Models\ServiceRequest;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Http\Resources\OrdersResource;
use App\Http\Resources\ServiceRequestResource;

class OrderController extends Controller
{

    /**
     *  @var \App\Models\User $_user 
     *  @var \App\Models\VehicleType $_vehicleType 
     *  @var \App\Models\ServiceRequest $_serviceRequest 
     *  @var \App\Models\Transaction $_transaction 
     *  @var $_environment
     *  @var Order $_order
     */
    private $_user, $_vehicleType, $_serviceRequest, $_transaction, $_environment, $_order;



    /**
     * Create a new controller instance.
     * @param  \App\Models\User $user
     * @param  \App\Models\VehicleType $vehicleType
     * @param  \App\Models\Transaction $transaction
     * @param  \App\Models\Transaction $transaction
     * @param  App $app
     * @param  Order $order
     * @return void
     */
    public function __construct(User $user, VehicleType $vehicleType, ServiceRequest $serviceRequest, Transaction $transaction, App $app, Order $order)
    {
        $this->_user = $user;
        $this->_vehicleType = $vehicleType;
        $this->_serviceRequest = $serviceRequest;
        $this->_transaction = $transaction;
        $this->_environment = $app::environment();
        $this->_order = $order;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try {
            $order = $this->_serviceRequest->userOrders();
            if ($order->isNotEmpty()) {
                return response()->json([
                    'error' => false,
                    'message' => 'success',
                    'data' => $order
                ], HttpStatusCode::OK);
            } else {
                return response()->json([
                    'error' => true,
                    'message' => "Not found Service history"
                ], HttpStatusCode::NOT_FOUND);
            }
        } catch (\Exception $e) {
            Log::error('OrderController -> index', [$e->getMessage()]);
            return response()->json([
                'error' => true,
                'message' => $this->_environment == MyAppEnv::LOCAL ? $e->getMessage() : HttpStatusCode::$statusTexts[HttpStatusCode::INTERNAL_SERVER_ERROR]
            ], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * show service request details
     * @param ServiceRequest $serviceRequest
     */
    public function showServiceRequest(ServiceRequest $serviceRequest)
    {
        try {
            return new ServiceRequestResource($serviceRequest);
        } catch (\Exception $e) {
            Log::error('OrderController -> showServiceRequest', [$e->getMessage(), $e->getLine()]);
            return response()->json([
                'error' => true,
                'message' => $this->_environment == MyAppEnv::LOCAL ? $e->getMessage() : HttpStatusCode::$statusTexts[HttpStatusCode::INTERNAL_SERVER_ERROR]
            ], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * 
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function acceptOrReject(Request $request, $id)
    {
        $request->validate(['status' => 'required|in:ACCEPTED,REJECTED']);
        try {
            $order = $this->_serviceRequest->whereUser_id(auth()->user()->id)
                ->where('quotation_info_id', '!=', null)
                ->where('direct_contact', false)
                ->find($id);

            if ($order === null) {
                return response()->json([
                    'error' => true,
                    'message' => HttpStatusCode::$statusTexts[HttpStatusCode::NOT_FOUND]
                ], HttpStatusCode::NOT_FOUND);
            }

            if ($order) {
                switch ($order->status) {
                    case  AppConst::ACCEPTED:
                        return response()->json([
                            'error' => true,
                            'data' => $order,
                            'message' => "Offer already Accepted"
                        ], HttpStatusCode::OK);

                    case AppConst::REJECTED:
                        return response()->json([
                            'error' => true,
                            'data' => $order,
                            'message' => "Offer already Rejected"
                        ], HttpStatusCode::OK);

                    default:
                        $user = $request->user();
                        if ($request->status === 'ACCEPTED' && $user->credit > 0) {
                            $user->credit = $user->credit - 1;
                            $user->save();
                            $this->_transaction->create(['provider_id' => $order->provider_id, 'service_request_id' => $order->id, 'amount' => -1]);
                        }
                }
                $order->status = $request->status;
                $order->save();
                return response()->json(['error' => false, 'message' => 'success', 'data' => $order], HttpStatusCode::OK);
            }
            return response()->json(['error' => true, 'message' => HttpStatusCode::$statusTexts[HttpStatusCode::NOT_FOUND]], HttpStatusCode::NOT_FOUND);
        } catch (\EXception $e) {
            Log::error('OrderController -> acceptOrReject', [$e->getMessage()]);
            return response()->json([
                'error' => true,
                'message' => $this->_environment == MyAppEnv::LOCAL ? $e->getMessage() : HttpStatusCode::$statusTexts[HttpStatusCode::INTERNAL_SERVER_ERROR]
            ], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }

    public function cancelRequest($id)
    {
        try {
            $this->_serviceRequest = $this->_serviceRequest->where('user_id', auth()->user()->id)->find($id);

            if ($this->_serviceRequest === null) {
                return response()->json([
                    'error' => true,
                    'message' => HttpStatusCode::$statusTexts[HttpStatusCode::NOT_FOUND]
                ], HttpStatusCode::NOT_FOUND);
            }

            if ($this->_serviceRequest->status === AppConst::PENDING) {
                $this->_serviceRequest->status = AppConst::CANCEL;
                $this->_serviceRequest->save();
                return response()->json([
                    'error' => false,
                    'data' => $this->_serviceRequest,
                    'message' => HttpStatusCode::$statusTexts[HttpStatusCode::OK]
                ], HttpStatusCode::OK);
            }

            return response()->json([
                'error' => true,
                'data' => $this->_serviceRequest,
                'message' => "you can't cahnge service request status"
            ], HttpStatusCode::OK);
        } catch (\Exception $e) {
            Log::error('OrderController -> cancelRequest', [$e->getMessage()]);
            return response()->json([
                'error' => true,
                'message' => $this->_environment == MyAppEnv::LOCAL ? $e->getMessage() : HttpStatusCode::$statusTexts[HttpStatusCode::INTERNAL_SERVER_ERROR]
            ], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * create a new mmove request
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|ServiceRequestResource
     */
    public function moveRequest(Request $request)
    {
        $request->validate([
            'vehicle_type_id'   => 'required',
            'sub_service_id'    => 'required',
            'from_address'      => 'required',
            'provider_id'       => 'required',
            'to_address'        => 'required',
            'questions'         => 'nullable',
            'start_lat'         => 'required',
            'start_lng'         => 'required',
            'end_lat'           => 'required',
            'end_lng'           => 'required',
            'date'              => 'required',
        ]);

        try {
            $provider = $this->_user->provider()->find($request->provider_id);

            if ($provider === null) {
                return response()->json([
                    'error' => true,
                    'message' => 'Provider not found'
                ], HttpStatusCode::NOT_FOUND);
            }

            $vehicleType = $this->_vehicleType->find($request->vehicle_type_id);

            if ($vehicleType === null) {
                return response()->json([
                    'error' => true,
                    'message' => 'Vehicle Type not found'
                ], HttpStatusCode::NOT_FOUND);
            }

            $serviceRequest = $this->_serviceRequest->makeMovingRequest($request->all());
            if ($serviceRequest) {
                return new ServiceRequestResource($serviceRequest);
            }

            return response()->json([
                'error' => true,
                'message' => 'Not Created request'
            ], HttpStatusCode::OK);
        } catch (\Exception $e) {
            Log::error('OrderController -> moveRequest', [$e->getMessage()]);
            return response()->json([
                'error' => true,
                'message' => $this->_environment == MyAppEnv::LOCAL ? $e->getMessage() : HttpStatusCode::$statusTexts[HttpStatusCode::INTERNAL_SERVER_ERROR]
            ], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Create new order
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request)
    {
        $request->validate([
            'cart_ids' => 'required|array',
        ]);

        try {
            $response = $this->_order->createOrder($request->all());
            if ($response !== null && $response['error'] === true) {
                return response()->json($response, HttpStatusCode::NOT_FOUND);
            }
            return response()->json($response, HttpStatusCode::CREATED);
        } catch (\Exception $e) {
            Log::error('OrderController -> create', [$e->getMessage()]);
            return response()->json([
                'error' => true,
                'message' => $this->_environment == MyAppEnv::LOCAL ? $e->getMessage() : HttpStatusCode::$statusTexts[HttpStatusCode::INTERNAL_SERVER_ERROR]
            ], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get all orders
     */
    public function getOrder(Request $request)
    {
        try {
            $orders = $this->_order->where(function ($query) {
                $query->where('user_id', auth()->user()->id);
            })->when($request->type, function ($query) use ($request) {
                return $query->where('type', $request->type);
            })->with(['food', 'restaurant', 'product', 'grocery_store'])->latest()
                ->paginate(AppConst::PAGE_SIZE)->withPath('')->withQueryString();
            return new OrdersResource($orders);
        } catch (\Exception $e) {
            Log::error('OrderController -> getOrder', [$e->getMessage()]);
            return response()->json([
                'error' => true,
                'message' => $this->_environment == MyAppEnv::LOCAL ? $e->getMessage() : HttpStatusCode::$statusTexts[HttpStatusCode::INTERNAL_SERVER_ERROR]
            ], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Show order details
     */
    public function show(Order $order)
    {
        try {
            if ($order->user_id !== auth()->user()->id) {
                return response()->json([
                    'error' => true,
                    'message' => HttpStatusCode::$statusTexts[HttpStatusCode::NOT_FOUND]
                ], HttpStatusCode::NOT_FOUND);
            }
            return new OrderResource($order);
        } catch (\Exception $e) {
            Log::error('OrderController -> show', [$e->getMessage()]);
            return response()->json([
                'error' => true,
                'message' => $this->_environment == MyAppEnv::LOCAL ? $e->getMessage() : HttpStatusCode::$statusTexts[HttpStatusCode::INTERNAL_SERVER_ERROR]
            ], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * get service request working status
     * @param Request $request
     */
    public function workingStatus(Request $request)
    {
        $request->validate([
            'service_request_id' => 'required',
        ]);

        $this->_serviceRequest = $this->_serviceRequest->with('worked_times')
            ->where('user_id', $request->user()->id)
            ->find($request->service_request_id);

        if ($this->_serviceRequest === null) {
            return response()->json([
                'error' => true,
                'message' => HttpStatusCode::$statusTexts[HttpStatusCode::NOT_FOUND]
            ], HttpStatusCode::NOT_FOUND);
        }

        return response()->json([
            'error' => false,
            'data' => $this->_serviceRequest,
            'message' => HttpStatusCode::$statusTexts[HttpStatusCode::OK]
        ], HttpStatusCode::OK);
    }
}
