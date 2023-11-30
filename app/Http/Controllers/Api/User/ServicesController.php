<?php

namespace App\Http\Controllers\Api\User;

use App\Events\AlertEvent;
use Stripe\Token;
use Carbon\Carbon;
use Stripe\Stripe;
use App\Models\City;
use App\Models\Link;
use App\Models\User;
use App\Models\Media;
use App\Models\Country;
use App\Models\Message;
use App\Models\Service;
use App\Models\ZipCode;
use App\Utils\AppConst;
use App\Utils\UserType;
use App\Models\TimeSlot;
use App\Utils\MediaType;
use App\Http\Helpers\Fcm;
use App\Models\FrontPage;
use App\Models\RequestInfo;
use App\Models\ServiceArea;
use App\Models\Transaction;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\QuotationInfo;
use App\Utils\HttpStatusCode;
use App\Models\ServiceRequest;
use Illuminate\Validation\Rule;
use App\Models\ProviderSchedule;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Notifications\ServiceRequestNotification;
use App\Http\Requests\ServiceRequestCreateRequest;
use App\Models\Currency;

class ServicesController extends Controller
{

    /**
     *
     * @var User $_user
     * @var Service $_service
     * @var ProviderSchedule $_providerSchedule
     * @var ServiceRequest $_serviceRequest
     * @var TimeSlot $_timeSlot
     * @var ZipCode $_zipCode
     * @var Message $_message
     * @var QuotationInfo $_quotationInfo
     * @var RequestInfo $_requestInfo
     * @var Media $_media
     * @var Fcm $_fcm
     * @var Transaction $_transaction
     * @var Link $_link
     * @var Country $_country
     * @var City $_city
     * @var FrontPage $_frontPage
     * @access private
     */
    private $_user, $_service, $_providerSchedule, $_serviceRequest, $_timeSlot, $_zipCode, $_message, $_quotationInfo, $_requestInfo, $_media, $_fcm, $_transaction, $_link, $_country, $_city, $_frontPage;

    /**
     * Create a new controller instance.
     * @param \App\Models\User $user
     * @param \App\Models\Service $service
     * @return void
     */
    public function __construct(
        User             $user,
        Service          $service,
        ProviderSchedule $providerSchedule,
        ServiceRequest   $serviceRequest,
        TimeSlot         $timeSlot,
        ZipCode          $zipCode,
        Message          $message,
        QuotationInfo    $quotationInfo,
        RequestInfo      $requestInfo,
        Media            $media,
        Fcm              $fcm,
        Transaction      $transaction,
        Link             $link,
        Country          $country,
        City             $city,
        FrontPage        $frontPage
    )
    {
        $this->_user = $user;
        $this->_service = $service;
        $this->_providerSchedule = $providerSchedule;
        $this->_serviceRequest = $serviceRequest;
        $this->_timeSlot = $timeSlot;
        $this->_zipCode = $zipCode;
        $this->_message = $message;
        $this->_quotationInfo = $quotationInfo;
        $this->_requestInfo = $requestInfo;
        $this->_media = $media;
        $this->_fcm = $fcm;
        $this->_transaction = $transaction;
        $this->_link = $link;
        $this->_country = $country;
        $this->_city = $city;
        $this->_frontPage = $frontPage;
    }

    /**
     * list of services
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getMenu(Request $request)
    {
        try {
            $data = $this->_service->with([
                'sub_services' => function ($qry) {
                    return $qry->select([
                        'id',
                        'service_id',
                        'name',
                        'credit',
                        'image',
                        'view_type',
                        'show_in_the_footer',
                        'terms'
                    ])
                        ->where('status', true)
                        // ->with([
                        //     'provider_sub_services' => function ($qry) {
                        //         return $qry->whereStatus(1);
                        //     }
                        // ]);
                        ->withCount([
                            'provider_sub_services as total_provider' => function ($qry) {
                                return $qry->whereStatus(1)->has('provider');
                            }
                        ]);
                }
            ])
                ->when($request->query('country_id'), function ($q) use ($request) {
                    return $q->where('country_id', $request->query('country_id'));
                })
                ->where('status', true)
                ->orderBy('name')->get([
                    'id',
                    'name',
                    'image',
                    'og_title',
                    'og_description',
                ])->map(function ($item) {
                    $item['og_image'] = $item['og_image'];
                    return $item;
                });

            $links = $this->_link->when($request->query('country_id'), function ($q) use ($request) {
                return $q->where('country_id', $request->query('country_id'));
            })->get();

            $front_pages = $this->_frontPage->with([
                'app_urls',
                'extra_info'
            ])
                ->when($request->query('country_id'), function ($q) use ($request) {
                    return $q->where('country_id', $request->query('country_id'));
                })
                ->get();

            return response()->json([
                'error' => false,
                'message' => 'success',
                'data' => $data,
                'links' => $links,
                'front_page' => $front_pages,
                'country' => Currency::get(['id', 'country_currency', 'code'])
            ], HttpStatusCode::OK);
        } catch (\Exception $e) {
            $this->make_log(__METHOD__, $e, 'error');
            return response()->json([
                'error' => true,
                'message' => HttpStatusCode::$statusTexts[HttpStatusCode::INTERNAL_SERVER_ERROR]
            ], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * list of provider according to user ZipCode
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function providerList(Request $request)
    {
        try {
            $provider = $this->_user->providerList($request->query());
            if ($provider) {
                return response()->json([
                    'error' => false,
                    'message' => 'success',
                    'data' => $provider
                ], HttpStatusCode::OK);
            } else {
                return response()->json([
                    'error' => true,
                    'message' => "No provider found"
                ], HttpStatusCode::NOT_FOUND);
            }
        } catch (\Exception $e) {
            $this->make_log(__METHOD__, $e, 'error');
            return response()->json([
                'error' => true,
                'message' => HttpStatusCode::$statusTexts[HttpStatusCode::INTERNAL_SERVER_ERROR]
            ], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * get provider schedule
     *
     * @param $id
     * @return JsonResponse
     */
    public function providerSchedule($id)
    {
        /*
        $data = $this->_providerSchedule->where('provider_id', '=', $id)->with('time_slots')->WhereDoesntHave('time_slots.service_requests')->orWhereHas('time_slots.service_requests', function($q){
            return $q->Where('user_id', '!=', auth()->user()->id);
        })->paginate(AppConst::PAGE_SIZE);
        */
        try {
            $data = $this->_timeSlot->with('provider_schedule')->whereHas('provider_schedule', function ($q) use ($id) {
                return $q->whereProvider_id($id)->where('full_date', '>=', Carbon::now()->format('Y-m-d'));
            })->WhereDoesntHave('service_requests')
                // ->orWhereHas('service_requests', function ($q) {
                //     return $q->where('user_id', '!=', auth()->user()->id);
                // })
                ->paginate(AppConst::PAGE_SIZE);
            if ($data) {
                return response()->json([
                    'error' => false,
                    'message' => 'success',
                    'data' => $data
                ], HttpStatusCode::OK);
            } else {
                return response()->json([
                    'error' => true,
                    'message' => HttpStatusCode::$statusTexts[HttpStatusCode::NOT_FOUND]
                ], HttpStatusCode::NOT_FOUND);
            }
        } catch (\Exception $e) {
            $this->make_log(__METHOD__, $e, 'error');
            return response()->json([
                'error' => true,
                'message' => HttpStatusCode::$statusTexts[HttpStatusCode::INTERNAL_SERVER_ERROR]
            ], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * make a service request
     *
     * @param ServiceRequestCreateRequest $request
     * @return JsonResponse
     */
    public function serviceRequest(ServiceRequestCreateRequest $request): JsonResponse
    {
        try {
            $transaction = $this->_transaction->whereUser_id($request->user()->id)
                ->whereStatus(AppConst::PENDING)
                ->whereIs_payable(true)
                ->exists();
            if ((!$transaction && $request->is_hourly) || !$request->is_hourly) {
                $serviceRequest = $this->_serviceRequest->createRequest($request->validated(), $request->user());
                return response()->json($serviceRequest['data'], $serviceRequest['status']);
            } else {
                return response()->json([
                    'error' => true,
                    'data' => $transaction,
                    'message' => 'please pay your previous payable'
                ], HttpStatusCode::OK);
            }
        } catch (\Exception $e) {
            $this->make_log(__METHOD__, $e, 'error');
            return response()->json([
                'error' => true,
                'message' => HttpStatusCode::$statusTexts[HttpStatusCode::INTERNAL_SERVER_ERROR]
            ], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * User can diecly contact with provider as an service request
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function directContact(Request $request)
    {
        $request->validate(['provider_id' => 'required|exists:users,id',
            'message' => 'required',
            'questions' => 'required|array'
        ]);
        try {
            $serviceRequest = $this->_serviceRequest->create([
                'user_id' => auth()->user()->id,
                'provider_id' => $request->provider_id,
                "is_quotation" => true,
                'direct_contact' => true
            ]);

            $requestInfo = [];
            foreach ($request->questions as $key => $question) {
                if (gettype($question) == 'array') {
                    foreach ($question as $optionKey => $value) {
                        $requestInfo[] = [
                            'question_id' => Str::after($key, '_'),
                            'option_id' => $value
                        ];
                    }
                } else {
                    $requestInfo[] = [
                        'question_id' => Str::after($key, '_'),
                        'option_id' => $question
                    ];
                }
            }
            $serviceRequest->request_infos()->createMany($requestInfo);

            $message = $this->_message->create([
                'service_request_id' => $serviceRequest->id,
                'sender_id' => auth()->user()->id,
                'receiver_id' => $request->provider_id,
                'message' => $request->message
            ]);

            $provider = $this->_user->find($request->provider_id);
            $payload = [
                'title' => 'New Chat Request received',
                'body' => "New chat requested by " . auth()->user()->first_name,
                'user_id' => auth()->user()->id,
                'service_request_id' => $serviceRequest->id,
                'type' => 'CHAT_REQUEST'
            ];
            if ($provider->device_token !== null) {
                Fcm::push_notification($payload, [$provider->device_token], $provider->role, $provider->os_platform);
            }
            try {
                broadcast(new AlertEvent(['id' => $provider->id, 'payload' => $payload]));
            } catch (\Throwable $th) {
                //throw $th;
            }
            $provider->notify(new ServiceRequestNotification($payload));

            return response()->json([
                'error' => false,
                'message' => 'success',
                'data' => [
                    'service_request' => $serviceRequest,
                    'message' => $message
                ]
            ], HttpStatusCode::OK);
        } catch (\Exception $e) {
            Log::error($e->getMessage(), $request->all());
            $this->make_log(__METHOD__, $e, 'error');
            return response()->json([
                'error' => true,
                'message' => HttpStatusCode::$statusTexts[HttpStatusCode::INTERNAL_SERVER_ERROR]
            ], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }


    public function updateServiceRequest(Request $request, $id)
    {
        $request->validate([
            'address' => 'required|min:10|max:70',
            'detail' => 'required|min:20|max:200',
            'images.*' => 'image|mimes:jpeg,png,jpg,svg|max:4096',
        ]);

        try {
            $serviceRequest = $this->_serviceRequest->where('is_quotation', true)->find($id);
            if ($serviceRequest === null) {
                return response()->json([
                    'error' => true,
                    'message' => 'success',
                    'data' => [
                        'service_request' => $serviceRequest,
                        'message' => HttpStatusCode::$statusTexts[HttpStatusCode::NOT_FOUND]
                    ]
                ], HttpStatusCode::NOT_FOUND);
            }

            $quotationInfo = $this->_quotationInfo->create(['detail' => $request->detail]);

            if (isset($request->images)) {
                foreach ($request->images as $image) {
                    $name = uniqid() . '-' . time() . "." . $image->extension();
                    $path = '/public/user/quotation';
                    if (Storage::exists($path)) {
                        $url = Storage::url($image->storeAs($path, $name));
                        $this->_media->create([
                            'user_id' => auth()->user()->id,
                            'type' => MediaType::IMAGE,
                            'name' => $name,
                            'url' => $url,
                            'quotation_info_id' => $quotationInfo->id
                        ]);
                    } else {
                        Storage::makeDirectory($path);
                        $url = Storage::url($image->storeAs($path, $name));
                        $this->_media->create([
                            'user_id' => auth()->user()->id,
                            'type' => MediaType::IMAGE,
                            'name' => $name,
                            'url' => $url,
                            'quotation_info_id' => $quotationInfo->id
                        ]);
                    }
                }
            }
            $serviceRequest->quotation_info_id = $quotationInfo->id;
            $serviceRequest->address = $request->address;
            $serviceRequest->save();

            return response()->json([
                'error' => false,
                'data' => $serviceRequest,
                'message' => HttpStatusCode::$statusTexts[HttpStatusCode::OK]
            ], HttpStatusCode::OK);
        } catch (\Exception $e) {
            $this->make_log(__METHOD__, $e, 'error');
            return response()->json([
                'error' => true,
                'message' => HttpStatusCode::$statusTexts[HttpStatusCode::INTERNAL_SERVER_ERROR]
            ], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * list of country and city
     *
     * @return JsonResponse
     */
    public function getCountries(Request $request)
    {
        try {
            $data = $this->_country->whereHas('states')->with([
                'states:id,name,country_id',
                'states.zip_codes.service_areas:id,zip_code_id,place_id',
                'states.zip_codes' => function ($query) use ($request) {
                    return $query->select('id', 'code')
                        ->when($request->has('sub_service_id'), function ($qry) use ($request) {
                            return $qry->whereHas('users.provider_services', function ($qry) use ($request) {
                                return $qry->where('sub_service_id', $request->sub_service_id);
                            });
                        })->when($request->has('vehicle_type_id'), function ($qry) use ($request) {
                            return $qry->has('users.vehicles')->whereHas('users.vehicles', function ($qry) use ($request) {
                                return $qry->where('vehicle_type_id', $request->vehicle_type_id);
                            });
                        });
                },
                // 'cities:id,name,country_id',
                // 'cities.zip_codes' => function ($qry) use ($request) {
                //     return $qry->select('id', 'code')
                //         ->when($request->has('sub_service_id'), function ($qry) use ($request) {
                //             return $qry->whereHas('users.provider_services', function ($qry) use ($request) {
                //                 return $qry->where('sub_service_id', $request->sub_service_id);
                //             });
                //         })->when($request->has('vehicle_type_id'), function ($qry) use ($request) {
                //             return $qry->has('users.vehicles')->whereHas('users.vehicles', function ($qry) use ($request) {
                //                 return $qry->where('vehicle_type_id', $request->vehicle_type_id);
                //             });
                //         });
                // }
            ])->get();
            if (!$data->isEmpty()) {
                return response()->json([
                    'error' => false,
                    'message' => 'Success',
                    'data' => $data
                ], HttpStatusCode::OK);
            } else {
                return response()->json([
                    'error' => true,
                    'message' => 'List is empty'
                ], HttpStatusCode::NOT_FOUND);
            }
        } catch (\Exception $e) {
            $this->make_log(__METHOD__, $e, 'error');
            return response()->json([
                'error' => true,
                'message' => 'something went wrong!'
            ], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * check of hasPlaceId
     * @param Request $request
     * @param string $id
     * @return JsonResponse
     */
    public function hasPlaceId(Request $request, string $id): JsonResponse
    {
        try {
            $has = ServiceArea::Where('place_id', $id)
                ->whereHas('zipCode', function ($qry) use ($request) {
                    return $qry->when($request->has('sub_service_id'), function ($qry) use ($request) {
                        return $qry->whereHas('providers', function ($qry) use ($request) {
                            return $qry->whereHas('provider_services', function ($qry) use ($request) {
                                return $qry->where('sub_service_id', $request->sub_service_id)
                                    ->where('status', true);
                            });
                        });
                    })->when($request->has('vehicle_type_id'), function ($qry) use ($request) {
                        return $qry->whereHas('providers.vehicles', function ($qry) use ($request) {
                            return $qry->where('vehicle_type_id', $request->vehicle_type_id);
                        });
                    });
                })->exists();
            if ($has)
                return response()->json([
                    'error' => false,
                    'message' => 'Success',
                    'data' => true
                ], HttpStatusCode::OK);
            else
                return response()->json([
                    'error' => true,
                    'message' => 'Place id not found'
                ], HttpStatusCode::NOT_FOUND);
        } catch (\Exception $e) {
            $this->make_log(__METHOD__, $e, 'error');
            return response()->json([
                'error' => true,
                'message' => 'something went wrong!'
            ], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * return zipCode list
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function searchZipCode(Request $request)
    {
        $request->validate([
            'zipCode' => 'required|min:1|max:12',
        ]);

        try {
            if (isset($request->sub_service_id) == false && isset($request->vehicle_type_id) == false) {
                return response()->json([
                    'error' => true,
                    'message' => "Please provide vehicle type or sub service",
                ], HttpStatusCode::UNPROCESSABLE_ENTITY);
            }
            // when($request->sub_service_id || $request->vehicle_type_id, function ($query) use ($request) {$query->
            $data = $this->_zipCode->where(function ($query) use ($request) {
                return $query->when($request->sub_service_id, function ($qry) use ($request) {
                    return $qry->has('users.provider_services')->whereHas('users.provider_services', function ($qry) use ($request) {
                        return $qry->where('sub_service_id', $request->sub_service_id);
                    })->where('code', 'LIKE', "$request->zipCode%");
                    // ->where(function($q){
                    //     return $q->WhereHas('users.provider_schedules', function ($q) {
                    //         return $q->where('full_date', '>=', Carbon::now()->format('Y-m-d'));
                    //     })->orWhere(function($q){
                    //         return $q->WhereHas('users', function ($q) {
                    //             return $q->Where('account_type', 'PREMIUM');
                    //         });
                    //     });
                    // })
                })->when($request->vehicle_type_id, function ($qry) use ($request) {
                    return $qry->has('users.vehicles')->whereHas('users.vehicles', function ($qry) use ($request) {
                        return $qry->where('vehicle_type_id', $request->vehicle_type_id);
                    })->where('code', 'LIKE', "$request->zipCode%");
                });
            })->when($request->city, function ($qry) use ($request) {
                return $qry->whereHas('cities', function ($qry) use ($request) {
                    return $qry->where('id', $request->city);
                });
            })->paginate(20);
            // });
            if ($data->isEmpty()) {
                return response()->json([
                    'error' => true,
                    'message' => "Not provider exists on this zip code",
                ], HttpStatusCode::NOT_FOUND);
            } else {
                return response()->json([
                    'error' => false,
                    'message' => 'success',
                    'data' => $data
                ], HttpStatusCode::OK);
            }
        } catch (\Exception $e) {
            $this->make_log(__METHOD__, $e, 'error');
            return response()->json([
                'error' => true,
                'message' => HttpStatusCode::$statusTexts[HttpStatusCode::INTERNAL_SERVER_ERROR]
            ], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }
}
