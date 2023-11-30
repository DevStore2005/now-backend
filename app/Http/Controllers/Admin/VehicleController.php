<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Vehicle;
use App\Utils\MyAppEnv;
use Illuminate\View\View;
use App\Models\VehicleType;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Events\VehicleTypeEvent;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class VehicleController extends Controller
{

    /**
     *  @var User $_user 
     *  @var Vehicle $_vehicle 
     *  @var string $_environment
     * 
     */
    private $_user, $_environment, $_vehicleType;


    /**
     * Create a new controller instance.
     * @param  User $user
     * @param  VehicleType $vehicleType
     * @param  App  $app
     *
     * @return void
     */
    public function __construct(User $user, VehicleType $vehicleType, App $app)
    {
        $this->_user = $user;
        $this->_vehicleType = $vehicleType;
        $this->_environment = $app::environment();
    }

    /**
     * User list
     *
     * @param Request $request
     * @return Response|View
     */
    public function index(Request $request)
    {
        try {
            $data = $this->_vehicleType->get();
            return view('admin.vehicle_types.index', compact('data'));
        } catch (\Exception $e) {
            $this->make_log(__METHOD__, $e, 'error');
            return redirect()->back()->with('error_message', $this->_environment === MyAppEnv::LOCAL ? $e->getMessage() : 'something went wrong!');
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'type' => 'string|min:1|max:10',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
        try {
            $vehicleType = $this->_vehicleType->addNewVehicleType($request->all());
            if ($vehicleType) {
                event(new VehicleTypeEvent($vehicleType, 'new'));
                return redirect()->back()->with('success_message', 'Vehicale Added');
            }
            return redirect()->back()->with('error_message', 'Vehicle not added');
        } catch (\Exception $e) {
            $this->make_log(__METHOD__, $e, 'error');
            return  redirect()->back()->with('error_message', $this->_environment === MyAppEnv::LOCAL ? $e->getMessage() : 'something went wrong!');
        }
    }

    /**
     * update vahiicle type
     * @param Request $request
     * @param VehicleType $vehicleType
     */
    public function update(Request $request, VehicleType $vehicleType){
        $request->validate([
            'title' => 'required',
            'type' => 'string|min:1|max:10',
            'image' => 'required_if:isChange,1|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
        try {
            $vehicleType = $vehicleType->updateVehicleType($request->all(), $vehicleType);
            if ($vehicleType) {
                event(new VehicleTypeEvent($vehicleType, "update"));
                return redirect()->back()->with('success_message', 'Vehicale Updated');
            }
            return redirect()->back()->with('error_message', "Vehicle not update");
        } catch (\Exception $e) {
            $this->make_log(__METHOD__, $e, 'error');
            return  redirect()->back()->with('error_message', $this->_environment === MyAppEnv::LOCAL ? $e->getMessage() : 'something went wrong!');
        }
    }

    /**
     * destroy vehicle type
     * @param Request $request
     * @param VehicleType $vehicleType
     */
    public function destroy(VehicleType $vehicleType)
    {
        try {
            $vehicleId = $vehicleType->id;
            $res = $vehicleType->delete();
            if ($res) {
                event(new VehicleTypeEvent($vehicleId, "delete"));
                return redirect()->back()->with('success_message', 'Vehicale Deleted');
            }
        } catch (\Exception $e) {
            $this->make_log(__METHOD__, $e, 'error');
            return  redirect()->back()->with('error_message', $this->_environment === MyAppEnv::LOCAL ? $e->getMessage() : 'something went wrong!');
        }
    }


}
