<?php

namespace App\Http\Controllers\Api\RestaurantGrocery\Grocery;

use Exception;
use App\Utils\AppConst;
use App\Utils\MyAppEnv;
use App\Utils\UserType;
use Illuminate\Http\Request;
use App\Utils\HttpStatusCode;
use App\Models\BusinessProfile;
use Illuminate\Http\JsonResponse;
use App\Utils\BusinessProfileType;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Resources\GroceryStoreResource;
use App\Http\Resources\GroceryStoresResource;
use App\Http\Resources\BusinessProfileResource;

class GroceryController extends Controller
{
    /**
     * @var $_businessProfile
     * @var string $_environment
     */
    private $_businessProfile, $_environment;

    /**
     * Create a new controller instance.
     * @param BusinessProfile $businessProfile
     * @param App $app
     * @return void
     */
    public function __construct(BusinessProfile $businessProfile, App $app)
    {
        $this->_businessProfile = $businessProfile;
        $this->_environment = $app::environment();
    }

    /**
     * Display a listing of the resource.
     *
     * @return GroceryStoresResource|JsonResponse
     */
    public function index()
    {
        try {
            $groceryStore = $this->_businessProfile->where('type', BusinessProfileType::GROCERY)
                ->with('user')
                ->orderBy('total_order', 'DESC')
                ->paginate(AppConst::PAGE_SIZE)
                ->withPath('')
                ->withQueryString();
            if ($groceryStore->isEmpty() == true) {
                return response()->json([
                    'error' => true,
                    'message' => 'No groceryStore found'
                ], HttpStatusCode::NOT_FOUND);
            }
            return new GroceryStoresResource($groceryStore);
        } catch (Exception $e) {
            $this->make_log(__METHOD__, $e, 'error');
            return response()->json([
                'error' => true,
                'message' => $this->_environment == MyAppEnv::LOCAL ? $e->getMessage() : HttpStatusCode::$statusTexts[HttpStatusCode::INTERNAL_SERVER_ERROR]
            ], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\JsonResponse|BusinessProfileResource
     */
    public function show($id)
    {
        try {
            $restaurant = $this->_businessProfile->where('type', BusinessProfileType::GROCERY)->find($id);

            if ($restaurant == null) {
                return response()->json([
                    'error' => true,
                    'message' => 'No grocery store found'
                ], HttpStatusCode::NOT_FOUND);
            }
            return new BusinessProfileResource($restaurant);
        } catch (Exception $e) {
            $this->make_log(__METHOD__, $e, 'error');
            return response()->json([
                'error' => true,
                'message' => $this->_environment == MyAppEnv::LOCAL ? $e->getMessage() : HttpStatusCode::$statusTexts[HttpStatusCode::INTERNAL_SERVER_ERROR]
            ], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response|null
     */
    public function destroy($id)
    {
        //
    }
}
