<?php

namespace App\Http\Controllers\Api\Provider;

use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleType;
use Illuminate\Http\Request;
use App\Utils\HttpStatusCode;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Resources\VehicleResource;
use App\Http\Resources\VehiclesResource;
use App\Http\Resources\VehicleTypesResource;
use App\Utils\UserType;
use Illuminate\Http\JsonResponse;

class VehicleController extends Controller
{
    /**
     *  @var \App\Models\User $_user 
     *  @var \App\Models\VehicleType $_vehicleType 
     *  @var \App\Models\Vehicle $_vehicle 
     */
    private $_user, $_vehicleType, $_vehicle;


    /**
     * Create a new controller instance.
     * @param  \App\Models\User $user
     * @param  \App\Models\VehicleType $vehicleType
     * @param  \App\Models\Vehicle $vehicle
     * @return void
     */
    public function __construct(User $user, VehicleType $vehicleType, Vehicle $vehicle)
    {
        $this->_user = $user;
        $this->_vehicleType = $vehicleType;
        $this->_vehicle = $vehicle;
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

            if (empty($vehicleType)) {
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

    public function store(Request $request)
    {
        $request->validate([
            '*.vehicle_type_id' => 'required',
            '*.name' => 'required',
            '*.model' => 'required',
            '*.number' => 'required',
            '*.condition' => 'required',
            '*.company_name' => 'required',
        ]);
        try {
            $vehicles = $this->_vehicle->storeVehicle($request->all());
            return new VehiclesResource($vehicles);
        } catch (\Exception $e) {
            Log::error('VehicleController -> store', [$e->getMessage()]);
            return response()->json([
                'error' => true,
                'message' => HttpStatusCode::$statusTexts[HttpStatusCode::INTERNAL_SERVER_ERROR]
            ], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'vehicle_type_id' => 'required',
            'name' => 'required',
            'model' => 'required',
            'number' => 'required',
            'condition' => 'required',
            'company_name' => 'required',
        ]);

        try {
            
            $vehicle = $this->_vehicle->find($id);
    
            if($vehicle == null)
                return response()->json([
                    'error' => true,
                    'message' => HttpStatusCode::$statusTexts[HttpStatusCode::NOT_FOUND]
                ], HttpStatusCode::NOT_FOUND);
    
            if($request->user()->id != $vehicle->provider_id)
                return response()->json([
                    'error' => true,
                    'message' => HttpStatusCode::$statusTexts[HttpStatusCode::FORBIDDEN]
                ], HttpStatusCode::FORBIDDEN);
            
            $vehicle->update($request->all());
            return new VehicleResource($vehicle);
            
        } catch (\Exception $e) {
            Log::error('VehicleController -> update', [$e->getMessage()]);
            return response()->json([
                'error' => true,
                'message' => HttpStatusCode::$statusTexts[HttpStatusCode::INTERNAL_SERVER_ERROR]
            ], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Delete vehicle
     *
     * @param Vehicle $id
     * @return JsonResponse
     */
    public function destroy($id)
    {
        $vehicle = $this->_vehicle->find($id);
        if($vehicle == null)
        return response()->json([
            'error' => true,
            'message' => HttpStatusCode::$statusTexts[HttpStatusCode::NOT_FOUND]
        ], HttpStatusCode::NOT_FOUND);

        if(auth()->user()->id != $vehicle->provider_id)
        return response()->json([
            'error' => true,
            'message' => HttpStatusCode::$statusTexts[HttpStatusCode::FORBIDDEN]
        ], HttpStatusCode::FORBIDDEN);

        $vehicle->delete();

        return response()->json([
            'error' => false,
            'message' => 'Successfully Deleted'
        ], HttpStatusCode::OK);
    }
}
