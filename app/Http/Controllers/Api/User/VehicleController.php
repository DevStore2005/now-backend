<?php

namespace App\Http\Controllers\Api\User;

use App\Models\User;
use App\Models\VehicleType;
use Illuminate\Http\Request;
use App\Utils\HttpStatusCode;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Resources\VehicleTypesResource;
use Illuminate\Http\JsonResponse;

class VehicleController extends Controller
{

    /**
     *  @var \App\Models\User $_user 
     *  @var \App\Models\Vehicle $_vehicle 
     */
    private $_user, $_vehicleType;


    /**
     * Create a new controller instance.
     * @param  \App\Models\User $user
     * @param  \App\Models\VehicleType $vehicleType
     * @return void
     */
    public function __construct(User $user, VehicleType $vehicleType)
    {
        $this->_user = $user;
        $this->_vehicleType = $vehicleType;
    }

    /**
     * User list
     *
     * @param Request $request
     * @return JsonResponse|VehicleTypesResource
     */
    public function index()
    {
        try {
            $vehicleType = $this->_vehicleType->get();

            if(empty($vehicleType)){
                return response()->json([
                    'error' => true,
                    'message' => HttpStatusCode::$statusTexts[HttpStatusCode::NOT_FOUND]
                ], HttpStatusCode::NOT_FOUND);
            }

            return new VehicleTypesResource($vehicleType);

        } catch (\Exception $e) {
            Log::error('VehicleController -> index', [$e->getMessage()]);
            return response()->json([
                'error' => true,
                'message' => HttpStatusCode::$statusTexts[HttpStatusCode::INTERNAL_SERVER_ERROR]
            ], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }
}
