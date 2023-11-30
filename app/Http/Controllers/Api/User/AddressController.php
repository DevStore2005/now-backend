<?php

namespace App\Http\Controllers\Api\User;

use App\Models\User;
use App\Models\Address;
use App\Utils\MyAppEnv;
use Illuminate\Http\Request;
use App\Utils\HttpStatusCode;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Resources\AddressResource;
use App\Http\Resources\AddressesResource;

class AddressController extends Controller
{
    /**
     *  @var User $_user 
     *  @var Address $_address
     *  @var string $_environment
     */
    private $_user, $_address, $_environment;



    /**
     *  @var User $_user 
     *  @var Address $_address
     *  @var string $_environment
     *  @param App $app
     */
    public function __construct(User $user, Address $address, App $app)
    {
        $this->_user = $user;
        $this->_address = $address;
        $this->_environment = $app::environment();
    }


    /**
     * Display a listing of the resource.
     *
     * @return AddressesResource|JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $addresses = $this->_address->where('user_id', $request->user()->id)->get();
            return new AddressesResource($addresses);
        } catch (\Exception $e) {
            Log::error('AddressController -> index', [$e->getMessage()]);
            return response()->json(['message' => $this->_environment === MyAppEnv::LOCAL ? $e->getMessage() : HttpStatusCode::$statusTexts[HttpStatusCode::INTERNAL_SERVER_ERROR]], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return JsonResponse|AddressResource
     */
    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:HOME,OFFICE,OTHER',
            'address' => 'required|string',
        ]);
        try {
            $address = $this->_address->createAddress($request->all(['type', 'address', 'flat_no', 'zip_code']), $request->user()->id);
            return new AddressResource($address);
        } catch (\Exception $e) {
            Log::error('AddressController -> store', [$e->getMessage()]);
            return response()->json([
                'error' => true,
                'message' => $this->_environment == MyAppEnv::LOCAL ? $e->getMessage() : HttpStatusCode::$statusTexts[HttpStatusCode::INTERNAL_SERVER_ERROR]
            ], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  Address  $address
     * @return JsonResponse|null
     */
    public function show(Address $address)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Address  $address
     * @return JsonResponse|null
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  Address  $address
     * @return JsonResponse|null
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Address  $address
     * @return JsonResponse
     */
    public function destroy(Address $address)
    {
        try {
            $result = $address->delete();
            if ($result) {
                return response()->json([
                    'error' => false,
                    'data' => $address,
                    'message' => 'Address deleted successfully'
                ], HttpStatusCode::OK);
            } else {
                return response()->json([
                    'error' => true,
                    'message' => 'Address not deleted'
                ], HttpStatusCode::CONFLICT);
            }
        } catch (\Exception $e) {
            Log::error('AddressController -> destroy', [$e->getMessage()]);
            return response()->json([
                'error' => true,
                'message' => $this->_environment == MyAppEnv::LOCAL ? $e->getMessage() : HttpStatusCode::$statusTexts[HttpStatusCode::INTERNAL_SERVER_ERROR]
            ], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }
}
