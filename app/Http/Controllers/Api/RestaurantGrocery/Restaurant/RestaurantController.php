<?php

namespace App\Http\Controllers\Api\RestaurantGrocery\Restaurant;

use App\Restaurant;
use App\Utils\AppConst;
use App\Utils\MyAppEnv;
use App\Utils\UserType;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Utils\HttpStatusCode;
use App\Models\BusinessProfile;
use App\Utils\BusinessProfileType;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Resources\RestaurantResource;
use App\Http\Resources\RestaurantsResource;
use App\Http\Resources\BusinessProfileResource;

class RestaurantController extends Controller
{
    /**
     * @var Product $_businessProfile
     * @var String $_environment
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
     * get list of restaurnat
     *
     * @return RestaurantsResource
     */
    public function index(Request $request)
    {
        try {
            $restaurants = $this->_businessProfile
                ->where('type', BusinessProfileType::RESTAURANT)
                ->when($request->has('restaurant_type') && $request->restaurant_type !== null, function ($query) use ($request) {
                    if (gettype(json_decode($request->restaurant_type)) === 'array') {
                        $types = json_decode($request->restaurant_type);
                        return $query->whereIn('restaurant_type', array_values($types));
                    } else {
                        return $query->where('restaurant_type', $request->restaurant_type);
                    }
                })
                ->with('user')
                ->latest()
                ->paginate(AppConst::PAGE_SIZE)
                ->withPath('');
            // ->withQueryString();

            if ($restaurants->isEmpty() == true) {
                return response()->json([
                    'error' => true,
                    'message' => 'No resturants found'
                ], HttpStatusCode::NOT_FOUND);
            }
            return new RestaurantsResource($restaurants);
        } catch (\Exception $e) {
            Log::error(['ProductController -> index', $e->getMessage()]);
            return response()->json([
                'error' => true,
                'message' => $this->_environment == MyAppEnv::LOCAL ? $e->getMessage() : HttpStatusCode::$statusTexts[HttpStatusCode::INTERNAL_SERVER_ERROR]
            ], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return RestaurantResource
     */
    public function show($id)
    {
        try {
            $restaurant = $this->_businessProfile->where('type', BusinessProfileType::RESTAURANT)->find($id);

            if ($restaurant == null) {
                return response()->json([
                    'error' => true,
                    'message' => 'No resturant found'
                ], HttpStatusCode::NOT_FOUND);
            }
            return new BusinessProfileResource($restaurant);
        } catch (\Exception $e) {
            Log::error(['ProductController -> restaurants', $e->getMessage()]);
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
     */
    public function destroy($id)
    {
        //
    }
}
