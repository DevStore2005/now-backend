<?php

namespace App\Models;

use App\Events\AlertEvent;
use Carbon\Carbon;
use Stripe\Charge;
use Stripe\Stripe;
use App\Models\User;
use Stripe\Customer;
use App\Models\Message;
use App\Utils\AppConst;
use App\Utils\UserType;
use App\Models\Feedback;
use App\Models\Question;
use App\Models\TimeSlot;
use App\Utils\MediaType;
use App\Http\Helpers\Fcm;
use App\Models\SubService;
use App\Models\WorkedTime;
use App\Utils\ServiceType;
use App\Models\RequestInfo;
use App\Models\Transaction;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use App\Http\Helpers\Common;
use App\Utils\WorkingStatus;
use App\Models\QuotationInfo;
use App\Utils\HttpStatusCode;
use App\Utils\ServiceRequestType;
use Spatie\MediaLibrary\HasMedia;
use App\Jobs\NewServiceRequestJob;
use Illuminate\Support\Facades\Log;
use App\Models\ProvidersSubscription;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Builder;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Collection;
use App\Notifications\ServiceRequestNotification;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServiceRequest extends Model implements HasMedia
{
    use InteractsWithMedia;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'sub_service_id',
        'provider_id',
        'address',
        'quotation_info_id',
        'hours',
        'is_quotation',
        'direct_contact',
        'is_replied',
        'sub_service',
        'payment_status',
        'paid_amount',
        'payable_amount',
        'type',
        'date'
    ];

    /**
     * Summary of scopeAccepted
     * @param mixed $qry
     * @return mixed
     */
    public function scopeAccepted($qry)
    {
        return $qry->where('status', AppConst::ACCEPTED);
    }

    /**
     * Summary of scopeCompleted
     * @param mixed $qry
     * @return mixed
     */
    public function scopePending($qry)
    {
        return $qry->where('status', AppConst::PENDING);
    }

    /**
     * Summary of scopeRejected
     * @param mixed $qry
     * @return mixed
     */
    public function scopeRejected($qry)
    {
        return $qry->where('status', AppConst::REJECTED);
    }

    /**
     * Summary of scopeCancelled
     * @param mixed $qry
     * @return mixed
     */
    public function scopeCancelled($qry)
    {
        return $qry->where('status', 'CANCEL');
    }

    /**
     * Relationship With RequestInfo
     *
     * @return HasMany
     */
    public function request_infos()
    {
        return $this->hasMany(RequestInfo::class);
    }

    /**
     * Relationship with ServiceRequest
     *
     */
    public function time_slots()
    {
        return $this->belongsToMany(TimeSlot::class);
    }

    /**
     * Get the book time_slots
     * @return HasMany
     */
    public function book_time_slots(): HasMany
    {
        return $this->HasMany(TimeSlot::class);
    }

    /**
     * Relationship with User
     *
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relationship with User
     *
     */
    public function provider()
    {
        return $this->belongsTo(User::class, 'provider_id');
    }

    /**
     * service request of provider
     * @param Builder $query
     * @param mixed $id
     * @return Builder $query
     */
    public function scopeOf_provider($query, $id)
    {
        return $query->where('provider_id', $id);
    }

    /**
     * Competed service request
     * @param mixed $query
     * @return mixed
     */
    public function scopeCompleted($query)
    {
        return $query->whereIsCompleted(1);
    }

    /**
     * On going service request
     * @param mixed $query
     * @return mixed
     */
    public function scopeOngoing($query)
    {
        return $query->Where('working_status', WorkingStatus::STARTED);
    }

    /**
     * Relationship with Provider Service
     *
     */
    public function requested_sub_service()
    {
        return $this->belongsTo(SubService::class, 'sub_service_id');
    }

    /**
     * Relationship with QuotationInfo
     *
     */
    public function quotation_info()
    {
        return $this->belongsTo(QuotationInfo::class);
    }

    /**
     * Relationship with WorkedTime
     *
     */
    public function worked_times()
    {
        return $this->hasMany(WorkedTime::class);
    }

    /**
     * Relationship with Feedback from provider
     *
     */
    public function provider_feeback()
    {
        return $this->hasOne(Feedback::class, 'service_request_id')->whereNull('user_id');
    }

    /**
     * Relationship with Feeback from user
     *
     */
    public function user_feeback()
    {
        return $this->hasOne(Feedback::class, 'service_request_id')->whereNull('provider_id');
    }

    /**
     * Relationship with Feeback transaction
     *
     */
    public function payable()
    {
        return $this->hasOne(Transaction::class)->where('is_payable', true);
    }

    public function transaction()
    {
        return $this->hasOne(Transaction::class)->whereStatus('succeeded');
    }

    /**
     * Relationship with service request
     *
     * @return HasOne
     */
    public function message()
    {
        return $this->hasOne(Message::class)->latest();
    }

    public function providers_subscription()
    {
        return $this->hasOne(ProvidersSubscription::class, 'service_request_id');
    }

    /**
     * Load relationship with service request
     *
     * @return $this
     */
    public function loadWithRelationship()
    {
        return $this->load([
            'user',
            'provider',
            'request_infos',
            'quotation_info',
            'requested_sub_service',
            'provider_feeback',
            'user_feeback',
            'payable',
            'worked_times'
        ]);
    }

    /**
     * Craete new searvice request 
     *
     * @param array $service_request
     * @return array
     */
    public function createRequest($service_request, $user)
    {
        try {
            if (!is_array($service_request['questions'])) $service_request['questions'] = json_decode($service_request['questions'], TRUE);
            $resp = $this->_questionSubService($service_request['questions']);
            if (isset($resp['data'])) return $resp;
            $provider = User::with('provider_profile:id,provider_id,hourly_rate')
            ->provider()
            ->find($service_request['provider_id'], ['id', 'first_name', 'last_name', 'email', 'device_token', 'role', 'os_platform']);
            if (!$provider->provider_profile) {
                return [
                    'data' => [
                        'error' => true,
                        'message' => 'provider profile not found'
                    ],
                    'status' => HttpStatusCode::OK
                ];
            }
            $subService = $resp['sub_service'];
            switch ($service_request['is_hourly']) {
                case "1":
                    return $this->_hourlyRequest($service_request, $user, $provider, $subService);
                case "0":
                    return $this->_quotationRequest($service_request, $subService, $user);
                default:
                    return [
                        'data' => [
                            'error' => true,
                            'message' => 'hourly may true or false'
                        ],
                        'status' => HttpStatusCode::OK
                    ];
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * store media details in media table
     * @param string $url
     * @param string $name
     * @param int $id
     * @return Media
     */
    
    private function _storeInMedia(string $url, string $name, int $id, $type = null): Media
    {
        return Media::create([
            'user_id' => auth()->user()->id,
            'type' => $type ?? MediaType::IMAGE,
            'name' => $name,
            'url' => $url,
            'quotation_info_id' => $id
        ]);
    }

    /**
     * user can get quotation
     *
     * @param array $quotation
     * @param ServiceRequest $serviceRequest
     * @return ServiceRequest
     */
    public function quotation($quotation, $serviceRequest)
    {
        $quotationInfo = QuotationInfo::find($serviceRequest->quotation_info_id);
        if (isset($quotation['reply'])) {
            $quotationInfo->reply = $quotation['reply'];
        }
        $quotationInfo->duration = $quotation['duration'];
        $quotationInfo->price = $quotation['price'];
        $quotationInfo->save();
        return ServiceRequest::where('provider_id', '=', auth()->user()->id)
            ->with('quotation_info')
            ->find($serviceRequest->id);
    }

    /**
     * list of order of provider
     *
     * @param array $params
     * @return void
     */
    public function ListOfOrder($params)
    {
        return ServiceRequest::where('provider_id', '=', auth()->user()->id)
            // ->orWhereHas('payable', function ($q) {
            //     $q->where('is_payable', true);
            // })
            ->with([
                'user' => function ($qry) {
                    return $qry->withCount('provider_feedbacks as feedback_count');
                },
                'time_slots',
                'quotation_info',
                'quotation_info.quotation_media',
                'request_infos',
                'request_infos.question',
                'request_infos.question.sub_service',
                'request_infos.option',
                'payable',
                'worked_times'
        ])
            ->latest()
            ->paginate(AppConst::PAGE_SIZE);
    }

    /**
     * list or user's order
     *
     * @return Collection|null
     */
    public function userOrders()
    {
        $with = [
            'provider' => function ($qry) {
                return $qry->withCount('user_feedbacks');
            },
            'provider.provider_profile',
            'time_slots',
            'quotation_info',
            'quotation_info.quotation_media',
            'request_infos.question',
            'request_infos.question.sub_service',
            'request_infos.option',
            'payable',
            'worked_times',
            'user_feeback',
            'provider_feeback',
        ];

        return ServiceRequest::where('user_id', '=', auth()->user()->id)
            // ->orWhereHas('payable', function ($q) {
            //     $q->where('is_payable', true);
            // })
            ->with($with)
            ->latest()
            ->paginate(AppConst::PAGE_SIZE)->withPath("");
    }

    /**
     * makeMovingRequest
     * @param array $moveRequest
     * @return $this
     */
    public function makeMovingRequest($moveRequest)
    { 
        $moveRequest['name'] = isset($moveRequest['name']) == true ? $moveRequest['name'] : null;
        $moveRequest['email'] = isset($moveRequest['email']) == true ? $moveRequest['email'] : null;
        $moveRequest['phone'] = isset($moveRequest['phone']) == true ? $moveRequest['phone'] : null;
        $moveRequest['detail'] = isset($moveRequest['detail']) == true ? $moveRequest['detail'] : null;
        $moveRequest['sub_service_id'] = isset($moveRequest['sub_service_id']) == true ? $moveRequest['sub_service_id'] : null;

        $quotationInfo = QuotationInfo::create($moveRequest);

        $data['user_id'] = auth()->user()->id;
        $data['sub_service_id'] = $moveRequest['sub_service_id'];
        $data['provider_id'] = $moveRequest['provider_id'];
        $data['quotation_info_id'] = $quotationInfo->id;
        $data['is_quotation'] = true;
        isset($moveRequest['sub_service_id']) ? $subService = SubService::find($moveRequest['sub_service_id']) : $subService = null;
        if ($subService) {
            $data['sub_service'] = $subService->name;
            $data['type'] = ServiceRequestType::MOVING_REQUEST;
        }
        $serviceRequest = $this->create($data);

        $provider = User::provider()->find($moveRequest['provider_id']);

        $payload = [
            'title' => 'New request received',
            'body' => "New Quotation by " . auth()->user()->first_name,
            'user_id' => auth()->user()->id,
            'service_request_id' => $serviceRequest->id,
            'type' => 'MOVING'
        ];
        $this->_createNotification($provider, $payload);

        if (isset($moveRequest['questions'])) {
            $this->_storeQuestionAndAnswer($moveRequest['questions'], $serviceRequest);
        }
        return $serviceRequest;
    }

    /************* Private Functions **************/

    /**
     * @param mixed $questions
     * @return array
     */
    private function _questionSubService($questions)
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

    /**
     * Summary of _hourlyRequest
     * @param array $service_request
     * @param User $user
     * @param User $provider
     * @param SubService $subService
     * @return mixed
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
        $type = null;
        if (isset($service_request['token'])) {
            $type = 'token';
        } else if (isset($service_request['card_id'])) {
            $type = 'card';
        }
        $payAndCreateServiceRequest = $this->_payAndCreateServiceRequest($service_request[$type == 'token' ? $type : "card_id"], $user, $data, $type, $service_request);
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
        ];;
    }

    /**
     * Summary of _selectedPlan
     * @param array $serviceRequest
     * @param User $user
     * @return array
     */
    public function _selectedPlan($serviceRequest, $user)
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

    /**
     * Summary of _getEndDate
     * @param int $duration
     * @param string $type
     * @return string
     */
    private function _getEndDate($duration, $type)
    {
        $date = Carbon::now();
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
     * Summary of _payAndCreateServiceRequest
     * @param string $tokenOrCardId
     * @param User $user
     * @param array $data
     * @param string $type
     * @param array $service_request
     * @return array|bool|mixed
     */
    private function _payAndCreateServiceRequest($tokenOrCardId, $user, $data, $type, $service_request)
    {
        $subscription = null;
        if (isset($service_request['plan_id'])) {
            $res = $this->_selectedPlan($service_request, $user);
            if ($res['error']) return $res;
            $subscription = $res['subscription'];
            $discount = (($data['paid_amount'] * $subscription->off) / 100);
            $data['paid_amount'] = $data['paid_amount'] - $discount;
        }
        $payment = null;
        if ($data['paid_amount'] <= 0) {
            $data['paid_amount'] = 0;
        } else {
            $amount = $data['paid_amount'] * 100;
            $amount > 0 && $amount < 50 ? $data['paid_amount'] = 0.5 : $data['paid_amount'];
            $payment = $this->_pay($tokenOrCardId, $user, $data['paid_amount'], $type);
            if (isset($payment['error'])) return $payment;
        }
        $serviceRequest = ServiceRequest::create($data);
        $serviceRequest->book_time_slots()->createMany($service_request['book_time_slots']);
        if ($data['paid_amount'] > 0 && $payment && isset($payment['data'])) {
            $transaction = Transaction::create([
                'user_id' => $user->id,
                'service_request_id' => $serviceRequest->id,
                'payment_id' => $payment['data']->id,
                'amount' => $payment['data']->amount_captured / 100,
                'amount_captured' => $payment['data']->amount_captured / 100,
                'status' => $payment['data']->status,
                'payment_method' => $payment['data']->payment_method_details->card->brand,
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
     * Summary of _createSubscriptionHistory
     * @param mixed $subscription
     * @param mixed $transaction
     * @param mixed $serviceRequest
     * @param mixed $discount
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
     * Summary of _getDate
     * @param $date
     * @param string|null $type
     * @param int $add
     * @return \Carbon\CarbonInterface|\Illuminate\Support\Carbon|void
     */
    private function _getDate($date = null, string $type =  null, int $add = 4)
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
     * @param string $tokenOrCardId
     * @param User $user
     * @param mixed $amount
     * @param string $type
     * @return array
     */
    private function _pay($tokenOrCardId, $user, $amount, $type)
    {
        $description = "Hourly Service Request";
        $payment = null;
        if ($type == "card") {
            Stripe::setApiKey(config('services.stripe.secret'));
            Customer::update($user->stripe_id, [
                'default_source' => $tokenOrCardId
            ]);
            $payment = Common::stripe_payment(null, $amount, $description);
        }
        if ($type == "token") {
            $payment = Common::stripe_payment($tokenOrCardId, $amount, $description);
        }
        if ($payment && $payment['error']) {
            return [
                'error' => true,
                'response' => [
                    'data' => [
                        'error' => true,
                        'message' => $payment['data']
                    ], 'status' => HttpStatusCode::CONFLICT
                ]
            ];
        }
        return [
            'data' => $payment['data']
        ];
    }

    /**
     * Summary of _quotationRequest
     * @param array $service_request
     * @param SubService $subService
     * @param User $user
     * @return array
     */
    private function _quotationRequest($service_request, $subService, $user)
    {
        $first_name = $service_request['first_name'] ?? "";
        $last_name = $service_request['last_name'] ?? "";
        $name = "{$first_name} {$last_name}";
        $info = [
            'detail' => $service_request['detail'] ?? null,
            'name' => !ctype_space($name) ? trim($name) :  null,
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
     * Summary of _storeQuestionAndAnswer
     * @param array $questions
     * @param ServiceRequest $serviceRequest
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
     * Summary of _createNotification
     * @param User $provider
     * @param array $payload
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
}
