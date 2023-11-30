<?php

namespace App\Http\Controllers\Api\User;

use App\Models\User;
use Illuminate\Http\Request;
use App\Utils\HttpStatusCode;
use App\Models\ServiceRequest;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Resources\ServiceRequestsResource;

class ServiceRequestController extends Controller
{

    /**
     *
     * @var User $_user
     * @var ServiceRequest $_serviceRequest
     * @access private
     */
    private $_user, $_serviceRequest;

    /**
     * Create a new controller instance.
     * @param  \App\Models\User  $user
     * @param  \App\Models\Service $service
     * @return void
     */
    public function __construct(User $user, ServiceRequest $serviceRequest)
    {
        $this->_user = $user;
        $this->_serviceRequest = $serviceRequest;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse|ServiceRequestsResource
     */
    public function index(Request $request)
    {
        $request->validate([
            'provider_id' => 'integer|required_without:user_id',
            'user_id' => 'integer|required_without:provider_id',
            'type' => 'string|in:completed,ongoing',
        ]);

        try {
            $serviceRequests = $this->_serviceRequest->of_provider($request->provider_id)
                ->when($request->type == 'completed', function ($query) {
                    return $query->completed();
                })
                ->when($request->type == 'ongoing', function ($query) {
                    return $query->ongoing();
                })
                ->with([
                    $request->user_id ? 'user:id,first_name,last_name' :
                        'provider' => function ($query) {
                        $query->select('id', 'first_name', 'last_name')->with([
                            'provider_profile:id,provider_id,hourly_rate',
                        ]);
                    },
                    'request_infos' => function ($qry) {
                        return $qry->select([
                            "id",
                            "service_request_id",
                            "question_id",
                            "option_id"
                    ])->with([
                            'question:id,question',
                            'option:id,option'
                        ]);
                    },
                    'quotation_info',
                    'worked_times'
                ])
                ->paginate(5)
                ->withQueryString();
            if ($serviceRequests->isEmpty()) {
                return response()->json([
                    'error' => true,
                    'message' => 'No Service Requests Found'
                ], HttpStatusCode::NOT_FOUND);
            }
            return new ServiceRequestsResource($serviceRequests);
        } catch (\Exception $te) {
            Log::error('Error in ServiceRequestController@index: ' . $te->getMessage());
            return response()->json([
                'error' => true,
                'message' => 'Something went wrong'
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
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse|null
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse|null
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse|null
     */
    public function destroy($id)
    {
        //
    }
}
