<?php

namespace App\Http\Controllers\Api\Provider;

use App\Models\City;
use App\Models\User;
use App\Models\Country;
use App\Models\Service;
use App\Models\ZipCode;
use App\Utils\AppConst;
use App\Models\SubService;
use App\Utils\ServiceType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use App\Utils\HttpStatusCode;
use App\Models\ProviderService;
use App\Models\ProviderSchedule;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class ServicesController extends Controller
{
    /**
     * Private Variables
     *
     * @var ZipCode $_zipCode
     * @var User $_user
     * @var ProviderService $_providerService
     * @var ProviderSchedule $_providerSchedule
     * @var City $_city
     * @var Country $_country
     */
    private $_zipCode, $_user, $_providerService, $_providerSchedule, $_city, $_country;

    /**
     * Create a new controller instance.
     * @param \App\Models\ZipCode $zipCode
     * @param \App\Models\ProviderSchedule $providerSchedule
     * @param \App\Models\ProviderService $providerService
     * @param \App\Models\User $user
     * @param \App\Models\City $city
     * @param \App\Models\Country $country
     */
    public function __construct(User $user, ZipCode $zipCode, ProviderSchedule $providerSchedule, ProviderService $providerService, City $city, Country $country)
    {
        $this->_user = $user;
        $this->_zipCode = $zipCode;
        $this->_providerSchedule = $providerSchedule;
        $this->_providerService = $providerService;
        $this->_city = $city;
        $this->_country = $country;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getMain(Request $request): JsonResponse
    {
        $data = Service::with(['sub_services' => function ($query) {
            return $query->where('status', true);
        }])
            ->when($request->query('country_id'), function ($q) use ($request) {
                return $q->where('country_id', $request->query('country_id'));
            })
            ->where('status', true)
            ->orderBy('name')
            ->get();
        return response()->json([
            'error' => false,
            'message' => 'success',
            'data' => $data
        ]);
    }

    public function getSub($id)
    {
        $data = SubService::where('service_id', $id)->orderBy('name')->get();
        return response()->json(['error' => false, 'message' => 'success', 'data' => $data]);
    }

    public function providerServices()
    {
        $data = $this->_providerService->getProviderServices(auth()->user()->id);
        return response()->json(['error' => false, 'message' => 'success', 'data' => $data]);
    }

    public function updateStatusProviderService(Request $request)
    {
        $request->validate([
            'service_id' => 'requiredIf:sub_service_id,null',
            'sub_service_id' => 'requiredIf:service_id,null',
            'status' => 'required|boolean'
        ]);
        $data = $this->_providerService->updateStatusProviderService($request->all([
            'service_id',
            'sub_service_id',
            'status'
        ]));
        return response()->json($data['result'], $data['statusCode']);
    }

    public function post(Request $request)
    {
        $request->validate([
            // 'service_id' => 'required|numeric',
            // 'sub_services' => 'required|array',
            'services' => 'array|required_if:from_web,true',
            'services.*.serviceId' => 'required_if:from_web,true|numeric',
            'services.*.subServiceIds' => 'required_if:from_web,true',
            'zip_code.*.zipCode' => 'required',
            // 'zip_code.*.city' => 'required',
            'zip_code.*.state' => 'required',
            'zip_code.*.country' => 'required',
            'zip_code.*.place_id' => 'required',
        ]);
        DB::beginTransaction();
        try {
            $type = ServiceType::SERVICE;
            if ($request->has('from_web')) {
                $providerId = $request->user()->id;
                foreach ($request->services as $key => $service) {
                    if ($service['serviceId'] == 3 && $key == 0) {
                        $type = 'MOVING';
                    }
                    if ($key > 0) {
                        $type = "MULTIPLE";
                    }
                    foreach ($service['subServiceIds'] as $value) {
                        $this->_providerService->updateOrCreate([
                            'service_id' => $service['serviceId'],
                            'sub_service_id' => $value,
                            'provider_id' => $providerId
                        ]);
                    }
                }
            }
            $user = $request->user();
            $user->service_type = $type;
            $user->save();
            foreach ($request->zip_code as $data) {
                $this->handleData($data, $user);
            }
            DB::commit();
            return response()->json([
                'error' => false,
                'data' => $this->_user->providerProfile($request->user()->id, True, false),
                'message' => 'success'
            ]);
        } catch (\Exception $e) {
            Log::error('ServicesController -> post ', [$e->getMessage()]);
            DB::rollBack();
            return response()->json([
                'error' => true,
                'message' => 'something went wrong!'
            ], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * schedule list
     *
     * @return JsonResponse
     */
    public function scheduleList()
    {
        try {
            $data = $this->_providerSchedule->where('provider_id', '=', auth()->user()->id)
                ->with('time_slots')
                ->latest()
                ->paginate(AppConst::PAGE_SIZE);
            return response()->json([
                'error' => false,
                'message' => 'success',
                'data' => $data
            ], HttpStatusCode::OK);
        } catch (\Exception $e) {
            Log::error('ServicesController -> schedule ', [$e->getMessage()]);
            return response()->json([
                'error' => true,
                'message' => 'something went wrong!'
            ], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Service schedule
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function schedule(Request $request)
    {
        $request->validate([
            'dates' => 'required'
        ]);
        try {
            $this->_providerSchedule->createSchedule($request->all());
            return response()->json(['error' => false, 'message' => 'success'], HttpStatusCode::OK);
        } catch (\Exception $e) {
            Log::error('ServicesController -> schedule ', [$e->getMessage()]);
            return response()->json([
                'error' => true,
                'message' => 'something went wrong!'
            ], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }


    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function zipCodeList(Request $request): JsonResponse
    {
        try {
            $data = $this->_zipCode->whereHas('users', function ($q) {
                return $q->where('id', auth()->user()->id);
            })->with([
                'states' => function ($query) {
                    return $query->distinct('id');
                },
                'states.country',
                'service_areas:id,zip_code_id,place_id'
            ])->get();

            if (isset($data)) {
                return response()->json([
                    'error' => false,
                    'message' => 'success',
                    'data' => $data
                ], HttpStatusCode::OK);

            } else {
                return response()->json([
                    'error' => false,
                    'message' => 'not found'
                ], HttpStatusCode::NOT_FOUND);
            }
        } catch (\Exception $e) {
            Log::error('ServicesController -> zipCodeList ', [$e->getMessage()]);
            return response()->json([
                'error' => true,
                'message' => HttpStatusCode::$statusTexts[HttpStatusCode::INTERNAL_SERVER_ERROR]
            ], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Store provider zip Csde
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function storeZipCode(Request $request): JsonResponse
    {
        $request->validate([
            "data" => "required|array",
            "data.*.zipCode" => "required|min:3",
            // "data.*.city" => "required|string",
            "data.*.state" => "required|string",
            "data.*.country" => "required|string",
        ]);
        try {
            $user = $request->user();
            $userId = $user->id;
            foreach ($request->data as $data) {
                $this->handleData($data, $user);
            }
            $zipCode = $this->_zipCode->whereHas('users', function ($q) use ($userId) {
                return $q->where('id', $userId);
            })->get();
            return response()->json([
                'error' => false,
                'message' => 'success',
                'data' => $zipCode
            ], HttpStatusCode::OK);
        } catch (\Exception $e) {
            Log::error('ServicesController -> storeZipCode ', [$e->getMessage()]);
            return response()->json([
                'error' => true,
                'message' => HttpStatusCode::$statusTexts[HttpStatusCode::INTERNAL_SERVER_ERROR]
            ], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update provider Zip Code
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateZipCode(Request $request, $id)
    {
        $request->validate(['zip_code' => 'required']);

        $user = $request->user();
        $zipCode = $this->_zipCode->find($id);

        if ($zipCode !== null) {
            $checkZipCode = $this->_zipCode->where('code', '=', '' . $zipCode->code)->whereHas('users', function ($q) use ($user) {
                return $q->where('id', '=', $user->id);
            })->first();

            if ($checkZipCode === null) {
                return response()->json([
                    'error' => true,
                    'message' => HttpStatusCode::$statusTexts[HttpStatusCode::NOT_FOUND]
                ], HttpStatusCode::NOT_FOUND);
            } else {
                if ($checkZipCode->code === '' . $request->zip_code) {
                    return response()->json([
                        'error' => false,
                        'message' => 'success',
                        'data' => $checkZipCode
                    ], HttpStatusCode::OK);
                } else {
                    $code = $this->_zipCode->where('code', '=', '' . $request->zip_code)->first();
                    if ($code !== null) {
                        $user->zip_codes()->detach($checkZipCode->id);
                        $user->zip_codes()->attach($code->id);
                        return response()->json([
                            'error' => false,
                            'message' => 'success',
                            'data' => $code
                        ], HttpStatusCode::OK);
                    } else {
                        $user->zip_codes()->detach($checkZipCode->id);
                        $zip = $this->_zipCode->create(['code' => $request->zip_code]);
                        $user->zip_codes()->attach($zip->id);
                        return response()->json([
                            'error' => false,
                            'message' => 'success',
                            'data' => $code
                        ], HttpStatusCode::OK);
                    }
                }
            }
        } else {
            return response()->json([
                'error' => true,
                'message' => HttpStatusCode::$statusTexts[HttpStatusCode::NOT_FOUND]
            ], HttpStatusCode::NOT_FOUND);
        }
        try {
        } catch (\Exception $e) {
            Log::error(['ServicesController -> deleteZipCode ', $e->getMessage()]);
            return response()->json([
                'error' => true,
                'message' => HttpStatusCode::$statusTexts[HttpStatusCode::INTERNAL_SERVER_ERROR]
            ], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * delete Zip code
     *
     * @param int $id
     * @return JsonResponse
     */
    public function deleteZipCode($id)
    {
        try {
            $user = $this->_user->where('id', auth()->user()->id)->whereHas('zip_codes', function ($q) use ($id) {
                return $q->where('id', $id);
            })->first();

            if ($user !== null) {
                $user->zip_codes()->detach($id);
                return response()->json([
                    'error' => false,
                    'message' => 'success'
                ], HttpStatusCode::OK);
            } else {
                return response()->json([
                    'error' => true,
                    'message' => HttpStatusCode::$statusTexts[HttpStatusCode::NOT_FOUND]
                ], HttpStatusCode::NOT_FOUND);
            }
        } catch (\Exception $e) {
            Log::error('ServicesController -> deleteZipCode ', [$e->getMessage()]);
            return response()->json([
                'error' => true,
                'message' => HttpStatusCode::$statusTexts[HttpStatusCode::INTERNAL_SERVER_ERROR]
            ], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @param $data
     * @param $user
     * @return void
     */
    private function handleData($data, $user): void
    {
//        $zipcode = $this->_zipCode->query()
//            ->where('code', '=', $data['zipCode'])
//            ->first();

        $this->createNewZipCode($data, $user);
//        if (!isset($zipcode)) {
//            $this->createNewZipCode($data, $user);
//        } else {
//            $this->handlePlaceId($zipcode, $data);
//            $this->handleExistingZipCode($zipcode, $data, $user);
//        }
    }

    /**
     * @param $data
     * @param $user
     * @return void
     */
    private function createNewZipCode($data, $user): void
    {
        $zip = $this->_zipCode->create(['code' => $data['zipCode']]);
        $user->zip_codes()->attach($zip->id);
        $country = $this->_country->firstOrCreate(['name' => $data['country']]);
        $state = $country->states()->firstOrCreate(
            [
                'country_id' => $country->id,
                'name' => $data['state'],
            ],
            [
                'name' => $data['state'],
                'country_id' => $country->id,
                'country_code' => $country->iso2,
            ],
        );
        $state->zip_codes()->attach($zip->id);
        $zip->service_areas()->firstOrCreate(['place_id' => $data['place_id']]);
    }


    /**
     * @param $zip
     * @param $data
     * @return void
     */
    private function handlePlaceId($zip, $data): void
    {
        $zip->service_areas()->firstOrCreate(['place_id' => $data['place_id']]);
    }


    /**
     * @param $zipcode
     * @param $data
     * @param $user
     * @return void
     */
    private function handleExistingZipCode($zipcode, $data, $user): void
    {
        $stateZipCode = $this->_zipCode->query()
            ->where('code', '=', $data['zipCode'])
            ->whereHas('states', function ($query) use ($data) {
                return $query->where('name', $data['state']);
            })
            ->with(['states' => function ($query) use ($data) {
                return $query->where('name', $data['state']);
            }])
            ->first();
        if (!isset($stateZipCode)) {
            $this->handleNewState($zipcode, $data, $user);
        } else {
            $this->handleExistingState($stateZipCode, $data, $user);
        }
    }


    /**
     * @param $zipcode
     * @param $data
     * @param $user
     * @return void
     */
    private function handleNewState($zipcode, $data, $user): void
    {
        $country = $this->_country->firstOrCreate(['name' => $data['country']]);
        $state = $country->states()->firstOrCreate(
            [
                'country_id' => $country->id,
                'name' => $data['state'],
            ],
            [
                'name' => $data['state'],
                'country_id' => $country->id,
                'country_code' => $country->iso2,
            ],
        );
        $state->zip_codes()->attach($zipcode->id);
        $user->zip_codes()->attach($zipcode->id);
    }


    /**
     * @param $zipcode
     * @param $data
     * @param $user
     * @return void
     */
    private function handleExistingState($zipcode, $data, $user): void
    {
        $zipCode = $this->_zipCode->where('code', '=', $data['zipCode'])
            ->whereHas('states', function ($query) use ($data) {
                return $query->where('name', $data['state']);
            })
            ->with(['states' => function ($query) use ($data) {
                return $query->where('name', $data['state']);
            }])
            ->whereHas('users', function ($q) use ($user) {
                return $q->where('id', $user->id);
            })->first();

        if (!isset($zipCode)) {
            $user->zip_codes()->attach($zipcode->id);
        }
    }
}
