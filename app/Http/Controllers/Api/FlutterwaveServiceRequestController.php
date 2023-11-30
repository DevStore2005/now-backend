<?php

namespace App\Http\Controllers\Api;

use App\Events\AlertEvent;
use App\Http\Helpers\Common;
use App\Http\Helpers\Fcm;
use App\Http\Requests\FlutterwaveServiceRequestCreateRequest;
use App\Jobs\NewServiceRequestJob;
use App\Models\Media;
use App\Models\Plan;
use App\Models\Question;
use App\Models\QuotationInfo;
use App\Models\ServiceRequest;
use App\Models\Transaction;
use App\Models\User;
use App\Notifications\ServiceRequestNotification;
use App\Utils\MediaType;
use App\Utils\ServiceRequestType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Utils\HttpStatusCode;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;

class FlutterwaveServiceRequestController extends Controller
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
     * @param User $user
     * @param ServiceRequest $serviceRequest
     */
    public function __construct(User $user, ServiceRequest $serviceRequest)
    {
        $this->_user = $user;
        $this->_serviceRequest = $serviceRequest;
    }

    /**
     * @param FlutterwaveServiceRequestCreateRequest $request
     * @return array|JsonResponse
     * @throws \Throwable
     */
    public function store(FlutterwaveServiceRequestCreateRequest $request)
    {
        $service_request = $request->validated();
        DB::beginTransaction();
        try {
            if (!is_array($service_request['questions'])) $service_request['questions'] = json_decode($service_request['questions'], TRUE);
            $resp = $this->_questionSubService($service_request['questions']);
            if (isset($resp['data'])) return $resp;
            $provider = User::with('provider_profile:id,provider_id,hourly_rate')
                ->provider()
                ->find($service_request['provider_id'], ['id', 'first_name', 'last_name', 'email', 'device_token', 'role', 'os_platform']);
            if (!isset($provider) || !$provider->provider_profile) {
                return response()->json([
                    'error' => true,
                    'message' => 'provider profile not found'
                ], HttpStatusCode::NOT_FOUND);
            }
            $subService = $resp['sub_service'];
            switch ($service_request['is_hourly']) {
                case "1":
                    $serviceRequest = $this->_hourlyRequest($service_request, auth('api')->user(), $provider, $subService);
                    DB::commit();
                    return response()->json($serviceRequest['data'], $serviceRequest['status']);
                case "0":
                    $serviceRequest = $this->_quotationRequest($service_request, $subService, auth('api')->user());
                    DB::commit();
                    return response()->json($serviceRequest['data'], $serviceRequest['status']);
                default:
                    return response()->json([
                        'error' => true,
                        'message' => 'hourly may true or false'
                    ], HttpStatusCode::EXPECTATION_FAILED);
            }

        } catch (\Exception $exception) {
            DB::rollback();
            Log::error(['FlutterwaveServiceRequestController -> store ', $exception->getMessage()]);
            return response()->json([
                'error' => true,
                'message' => HttpStatusCode::$statusTexts[HttpStatusCode::INTERNAL_SERVER_ERROR]
            ], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @param $service_request
     * @param $user
     * @param $provider
     * @param $subService
     * @return array|mixed
     */
    private function _hourlyRequest($service_request, $user, $provider, $subService)
    {
        $data = Arr::only($service_request, ['address', 'provider_id', 'date']);
        $data['user_id'] = $user->id;
        $data['sub_service_id'] = $subService->id;
        $data['sub_service'] = $subService->name;
        $data['type'] = ServiceRequestType::SERVICE_REQUEST;
        if (isset($service_request['hours'])) {
            $data['hours'] = $service_request['hours'];
            $data['paid_amount'] = $provider->provider_profile->hourly_rate * $service_request['hours'];
        } else {
            $data['paid_amount'] = $provider->provider_profile->hourly_rate;
            $data['hours'] = 1;
        }
        $payAndCreateServiceRequest = $this->_payAndCreateServiceRequest($user, $data, $service_request);
        if (isset($payAndCreateServiceRequest['error'])) {
            return $payAndCreateServiceRequest['response'];
        }
        if ($payAndCreateServiceRequest && $payAndCreateServiceRequest['serviceRequest']) {
            $serviceRequest = $payAndCreateServiceRequest['serviceRequest'];
            $serviceRequest = $this->_storeQuestionAndAnswer($service_request['questions'], $payAndCreateServiceRequest['serviceRequest']);
            $serviceRequest->load([
                'user:id,first_name,last_name',
                'provider:id,first_name,last_name',
                'transaction',
                "book_time_slots",
                "transaction",
                "providers_subscription.subscription_histories"
            ]);
            dispatch(new NewServiceRequestJob($serviceRequest));

            $payload = [
                'title' => 'New request received',
                'body' => "New hourly request by " . $user->first_name,
                'user_id' => $user->id,
                'service_request_id' => $serviceRequest->id,
                'type' => 'SERVICE_REQUEST'
            ];
            $this->_createNotification($provider, $payload);
            return [
                'data' => [
                    'error' => false,
                    'message' => 'success',
                    'data' => $serviceRequest
                ],
                'status' => HttpStatusCode::OK
            ];
        }
        return [
            'data' => [
                'error' => true,
                'message' => 'Something went wrong'
            ],
            'status' => HttpStatusCode::INTERNAL_SERVER_ERROR
        ];
    }


    /**
     * @param $user
     * @param $data
     * @param $service_request
     * @return array
     */
    private function _payAndCreateServiceRequest($user, $data, $service_request): array
    {
        $subscription = null;
        if (isset($service_request['plan_id'])) {
            $res = $this->_selectedPlan($service_request, $user);
            if ($res['error']) return $res;
            $subscription = $res['subscription'];
            $discount = (($data['paid_amount'] * $subscription->off) / 100);
            $data['paid_amount'] = $data['paid_amount'] - $discount;
        }
        if ($data['paid_amount'] <= 0) {
            $data['paid_amount'] = 0;
        }
        $serviceRequest = ServiceRequest::create($data);
        $serviceRequest->book_time_slots()->createMany($service_request['book_time_slots']);

        if ($service_request['transaction_id']) {
            Transaction::query()->where('fw_transaction_id', $service_request['transaction_id'])->first()->update([
                'user_id' => $user->id,
                'service_request_id' => $serviceRequest->id,
            ]);
        }

        if ($subscription) {
            $subscription->update([
                'service_request_id' => $serviceRequest->id
            ]);
            $this->_createSubscriptionHistory($subscription, $transaction ?? null, $serviceRequest, $discount);
        }
        return [
            'serviceRequest' => $serviceRequest,
            'transaction' => $transaction ?? null
        ];
    }

    /**
     * @param $serviceRequest
     * @param $user
     * @return array
     */
    public function _selectedPlan($serviceRequest, $user): array
    {
        $token = isset($serviceRequest['token']) ? $serviceRequest['token'] : null;
        $card_id = isset($serviceRequest['card_id']) ? $serviceRequest['card_id'] : null;
        $plan = Plan::find($serviceRequest['plan_id']);
        $card = null;
        if ($token) {
            $card = Common::stripe_add_card($token);
            if ($card['error']) {
                return [
                    'error' => true,
                    'response' => [
                        'data' => [
                            'error' => true,
                            'message' => 'Something went wrong while adding card'
                        ],
                        'status' => HttpStatusCode::BAD_REQUEST
                    ]
                ];
            }
            $card = $card['data'];
        }
        if ($card_id) {
            $updated = Common::update_default_card($card_id);
            if (!$updated) {
                return [
                    'error' => true,
                    'response' => [
                        'data' => [
                            'error' => true,
                            'message' => 'Something went wrong while updating card'
                        ],
                        'status' => HttpStatusCode::INTERNAL_SERVER_ERROR
                    ]
                ];
            }
        }
        $providersSubscription = $user->hourly_subscriptions()->create([
            'plan_id' => $plan->id,
            'type' => $plan->type,
            'duration' => $plan->duration,
            'off' => $plan->off,
            'end_date' => $this->_getEndDate($plan->duration, $plan->type),
        ]);
        return [
            'error' => false,
            'subscription' => $providersSubscription
        ];
    }

    private function _getEndDate($duration, $type)
    {
        $date = \Carbon\Carbon::now();
        if ($type == 'Monthly') {
            $date->addMonths($duration);
        } else if ($type == 'Weekly') {
            $date->addWeeks($duration);
        } else if ($type == 'BiWeekly') {
            $date->addWeeks($duration)->subDays(3);
        }
        return $date->format('Y-m-d');
    }
    /**
     * @param $subscription
     * @param $transaction
     * @param $serviceRequest
     * @param $discount
     * @return void
     */
    private function _createSubscriptionHistory($subscription, $transaction, $serviceRequest, $discount)
    {
        $ary = [];
        $date = $this->_getDate();
        for ($idx = 0; $idx < $subscription->duration; $idx++) {
            $id = null;
            if ($idx == 0 && $transaction && $transaction->id) {
                $id = $transaction->id;
            }
            $ary[] = [
                'service_request_id' => $serviceRequest->id,
                'transaction_id' => $id,
                'discount' => $idx == 0 ? $discount : null,
                'deduction_date' => $date->format('Y-m-d'),
                'status' => $idx == 0 ? 'PAID' : null,
            ];
            if ($subscription->type == 'BiWeekly') {
                $date = $this->_getDate($date, $subscription->type, 4);
                $ary[] = [
                    'service_request_id' => $serviceRequest->id,
                    'transaction_id' => null,
                    'discount' => null,
                    'deduction_date' => $date->format('Y-m-d'),
                    'status' => null,
                ];
                $date = $this->_getDate($date, $subscription->type, 3);
            } else if (!$subscription->type == 'BiWeekly') {
                $date = $this->_getDate($date, $subscription->type);
            }
        }
        $subscription->subscription_histories()->createMany($ary);
    }

    /**
     * @param $date
     * @param string|null $type
     * @param int $add
     * @return Carbon|mixed|void
     */
    private function _getDate($date = null, string $type = null, int $add = 4)
    {
        if (!$date) return now();
        if ($type == 'Monthly') {
            return $date->addMonth();
        } else if ($type == 'Weekly') {
            return $date->addWeek();
        } else if ($type == 'BiWeekly') {
            return $date->addDays($add);
        }
    }

    /**
     * @param $service_request
     * @param $subService
     * @param $user
     * @return array
     * @throws \Throwable
     */
    private function _quotationRequest($service_request, $subService, $user): array
    {
        $first_name = $service_request['first_name'] ?? "";
        $last_name = $service_request['last_name'] ?? "";
        $name = "{$first_name} {$last_name}";
        $info = [
            'detail' => $service_request['detail'] ?? null,
            'name' => !ctype_space($name) ? trim($name) : null,
            'email' => $service_request['email'] ?? null,
            'phone' => $service_request['phone'] ?? null,
        ];

        $quotationInfo = QuotationInfo::create($info);

        if (isset($service_request['images'])) {
            foreach ($service_request['images'] as $image) {
                $common = new Common();
                $url = $common->store_media($image, 'user/quotation');
                $this->_storeInMedia($url, uniqid() . '-' . time(), $quotationInfo->id);
            }
        }

        if (isset($service_request['files'])) {
            foreach ($service_request['files'] as $file) {
                $common = new Common();
                $url = $common->store_media($file, 'user/quotation');
                $this->_storeInMedia($url, uniqid() . '-' . time(), $quotationInfo->id, MediaType::FILE);
            }
        }

        $data = Arr::only($service_request, ['address', 'provider_id',]);
        $data['sub_service'] = $subService->name;
        $data['user_id'] = $user->id;
        $data['sub_service_id'] = $subService->id;
        $data['quotation_info_id'] = $quotationInfo->id;
        $data['is_quotation'] = true;
        $serviceRequest = ServiceRequest::create($data);

        $serviceRequest = $this->_storeQuestionAndAnswer($service_request['questions'], $serviceRequest);
        $provider = User::provider()->find($service_request['provider_id']);

        $payload = [
            'title' => 'New request received',
            'body' => "New Quotation by " . $user->first_name,
            'user_id' => $user->id,
            'service_request_id' => $serviceRequest->id,
            'type' => 'SERVICE_REQUEST'
        ];
        $this->_createNotification($provider, $payload);
        $serviceRequest->load([
            'user:id,first_name,last_name',
            'provider' => function ($query) {
                $query->with('provider_profile')->select('id', 'first_name', 'last_name');
            },
            'quotation_info'
        ]);
        dispatch(new NewServiceRequestJob($serviceRequest));
        return [
            'data' => [
                'error' => false,
                'message' => 'success',
                'data' => $serviceRequest
            ],
            'status' => HttpStatusCode::OK
        ];
    }

    /**
     * @param $provider
     * @param $payload
     * @return void
     */
    private function _createNotification($provider, $payload)
    {
        if (isset($provider->device_token)) {
            Fcm::push_notification($payload, [$provider->device_token], $provider->role, $provider->os_platform);
        }
        try {
            broadcast(new AlertEvent(['id' => $provider->id, 'payload' => $payload]));
        } catch (\Throwable $th) {
            //throw $th;
        }
        $provider->notify(new ServiceRequestNotification($payload));
    }

    /**
     * @param $questions
     * @param $serviceRequest
     * @return mixed
     */
    private function _storeQuestionAndAnswer($questions, $serviceRequest)
    {
        $requestInfo = [];
        foreach ($questions as $key => $question) {
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
        return $serviceRequest;
    }

    /**
     * @param string $url
     * @param string $name
     * @param int $id
     * @param $type
     * @return void
     */
    private function _storeInMedia(string $url, string $name, int $id, $type = null): void
    {
        Media::create([
            'user_id' => auth()->user()->id,
            'type' => $type ?? MediaType::IMAGE,
            'name' => $name,
            'url' => $url,
            'quotation_info_id' => $id
        ]);
    }

    /**
     * @param $questions
     * @return array
     */
    public function _questionSubService($questions): array
    {
        $question = Question::with('sub_service')
            ->find(Str::after(array_key_first($questions), '_'), ['id', 'sub_service_id']);
        if (!$question) {
            return [
                'data' => [
                    'error' => true,
                    'message' => 'sub_service not found'
                ],
                'status' => HttpStatusCode::NOT_FOUND
            ];
        }
        $subService = $question->sub_service()->first(['id', 'service_id', 'name']);
        if (!$subService) {
            return [
                'data' => [
                    'error' => true,
                    'message' => 'sub_service not found'
                ],
                'status' => HttpStatusCode::NOT_FOUND
            ];
        }
        return [
            'question' => $question,
            'sub_service' => $subService
        ];
    }
}
