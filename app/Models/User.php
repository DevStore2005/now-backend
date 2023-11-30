<?php

namespace App\Models;

use Carbon\Carbon;
use App\Models\Plan;
use App\Models\ZipCode;
use App\Utils\AppConst;
use App\Utils\UserType;
use App\Models\Feedback;
use App\Models\Portfolio;
use App\Models\BlockedSlot;
use App\Utils\ProviderType;
use Illuminate\Support\Arr;
use App\Http\Helpers\Common;
use App\Models\PaymentMethod;
use Laravel\Cashier\Billable;
use App\Models\ServiceRequest;
use App\Models\ProviderProfile;
use App\Models\ProviderService;
use App\Models\ProviderSchedule;
use Spatie\MediaLibrary\HasMedia;
use Illuminate\Support\Facades\DB;
use Laravel\Passport\HasApiTokens;
use Illuminate\Pagination\Paginator;
use App\Models\ProvidersSubscription;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Password;
use Illuminate\Database\Eloquent\Builder;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable implements HasMedia
{
    use HasApiTokens, Notifiable, Billable, SoftDeletes, InteractsWithMedia;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'first_name',
        'last_name',
        'email',
        'password',
        'phone',
        'role',
        'account_type',
        'status',
        'country_id',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Create a Admin Scope
     *
     * @param $this $query
     * @return $this $query
     */
    public function scopeAdmin($query)
    {
        return $query->where('role', UserType::ADMIN);
    }

    /**
     * Create a Provider Scope
     *
     * @param $this $query
     * @return $this $query
     */
    public function scopeProvider($query)
    {
        return $query->where('role', UserType::PROVIDER);
    }

    public function scopeIndividualProvider($query)
    {
        return $query->provider()->where('provider_type', ProviderType::INDIVIDUAL);
    }

    public function scopeCompanyProvider($query)
    {
        return $query->provider()->where('provider_type', ProviderType::BUSINESS);
    }

    /**
     * Create a User Scope
     *
     * @param $this $query
     * @return $this $query
     */
    public function scopeUser($query)
    {
        return $query->where('role', UserType::USER);
    }

    /**
     * Relationship With ZipCode
     *
     * @return BelongsToMany
     */
    public function zip_codes()
    {
        return $this->belongsToMany(ZipCode::class);
        // return $this->belongsToMany(ZipCode::class, 'user_zip_code', 'user_id', 'zip_code_id');
    }

    /**
     * Relationship With ProviderSchedule
     *
     * @return HasMany
     */
    public function provider_schedules(): HasMany
    {
        return $this->hasMany(ProviderSchedule::class, 'provider_id', 'id');
    }

    /**
     * Relationship with Media
     *
     * @return HasMany
     */
    public function medias()
    {
        return $this->hasMany(Media::class);
    }

    /**
     * Relationship with Media
     *
     * @return HasMany
     */
    public function docs_licenses(): HasMany
    {
        return $this->hasMany(Media::class);
    }

    /**
     * Relationship with SubService
     *
     * @return HasMany|Builder
     */
    public function provider_services()
    {
        return $this->hasMany(ProviderService::class, 'provider_id')->latest();
    }

    /**
     * Relationship with ServiceRequest
     *
     * @return HasMany
     */
    public function service_requests()
    {
        return $this->hasMany(ServiceRequest::class);
    }

    /**
     * Relationship with ServiceRequest for provider
     *
     * @return HasMany
     */
    public function provider_service_requests(): HasMany
    {
        return $this->hasMany(ServiceRequest::class, "provider_id");
    }

    public function provider_completed_service_requests()
    {
        return $this->hasMany(ServiceRequest::class, "provider_id")->whereIs_completed(1);
    }

    /**
     * Relationship with ServiceRequest for user
     *
     * @return HasMany
     */
    public function user_service_requests()
    {
        return $this->hasMany(ServiceRequest::class, "user_id");
    }

    /**
     * Relationship with ProviderProfile
     *
     * @return HasOne
     */
    public function provider_profile(): HasOne
    {
        return $this->hasOne(ProviderProfile::class, 'provider_id', 'id');
    }

    /**
     * Relationship with sender messages
     *
     * @return HasMany
     */
    public function sender_messages(): HasMany
    {
        return $this->hasMany(Message::class, 'sender_id', 'id');
    }

    /**
     * Relationship with user feedback
     *
     * @return HasMany|Builder
     */
    public function user_feedbacks()
    {
        return $this->hasMany(Feedback::class, 'for_user_id', 'id')->where('provider_id', null);
    }

    /**
     * Relationship with provider feedback
     *
     * @return HasMany|Builder
     */
    public function provider_feedbacks()
    {
        return $this->hasMany(Feedback::class, 'for_user_id', 'id')->where('user_id', null);
    }

    /**
     * Relationship with receiver messages
     *
     * @return HasMany
     */
    public function receiver_messages(): HasMany
    {
        return $this->hasMany(Message::class, 'receiver_id', 'id');
    }

    /**
     * Relationship with message
     *
     * @return HasOne|Builder
     */
    public function sender_message()
    {
        return $this->hasOne(Message::class, 'sender_id', 'id')->latest();
    }

    /**
     * Relationship with message
     *
     * @return HasOne|Builder
     */
    public function receiver_message()
    {
        return $this->hasOne(Message::class, 'receiver_id', 'id')->latest();
    }

    /**
     * Relationship with vehicle
     *
     * @return HasMany
     */
    public function vehicles(): HasMany
    {
        return $this->hasMany(Vehicle::class, 'provider_id', 'id');
    }

    /**
     * Relationship with portfolio
     *
     * @return HasMany
     */
    public function portfolios(): HasMany
    {
        return $this->hasMany(Portfolio::class, 'provider_id');
    }

    /**
     * Relationship with Schedule
     *
     * @return HasMany
     */
    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class, 'provider_id');
    }

    /**
     * Relationship with Schedule
     *
     * @return HasMany
     */
    public function blockedSlots(): HasMany
    {
        return $this->hasMany(BlockedSlot::class, 'provider_id');
    }

    /**
     * Provider's plans
     * @return HasMany
     */
    public function plans(): HasMany
    {
        return $this->hasMany(Plan::class, 'provider_id');
    }

    /**
     * User's Subscriptions
     * @return HasMany
     */
    public function hourly_subscriptions(): HasMany
    {
        return $this->hasMany(ProvidersSubscription::class);
    }

    /**
     * Get user's transactions
     * @return HasMany
     */
    public function user_transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'user_id');
    }

    /**
     * Get provider's transactions
     * @return HasMany
     */
    public function provider_transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'provider_id');
    }

    public function payment_methods(): BelongsToMany
    {
        return $this->belongsToMany(PaymentMethod::class, 'payment_method_provider', 'provider_id', 'payment_method_id');
    }

    /**
     * Get User Comments
     * @return HasMany
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comments::class);
    }


    /**
     * Get provider list
     *
     * @param string|array|null
     * @return LengthAwarePaginator|null
     */
    public function providerList($params): ?LengthAwarePaginator
    {
        if (!$params) return null;
        $userColumns = [
            'id',
            'first_name',
            'last_name',
            'image',
            'rating',
            'provider_type',
            'account_type',
            'bio',
            'verified_at',
            'service_type',
            'country_id'
        ];
        $with = [
            'provider_profile:id,provider_id,business_name,city,hourly_rate,total_earn',
            'plans:id,provider_id,title,price,type,duration,off,description',
            'zip_codes',
            'zip_codes.service_areas',
            // 'user_feedbacks' => fn ($q) => $q->take(1),
            // 'user_feedbacks.user',
            // 'portfolios' => fn ($qry) => $qry->approved_portfolios()
        ];
        $withCount = [
            'user_feedbacks',
            // 'user_feedbacks as avg_rating' => fn ($q) => $q->select(DB::raw('avg(rating)')),
            'provider_completed_service_requests',
            // 'provider_schedules' => fn ($qry) => $qry->where('full_date', '>=', Carbon::now()->format('Y-m-d'))
        ];

        return User::provider()
            ->select($userColumns)
            ->has('provider_profile')
            ->whereStatus(AppConst::ACTIVE)
            ->with($with)
            ->withCount($withCount)
            ->when(Arr::has($params, 'country_id'), function ($q) use ($params) {
                $q->where('country_id', '=', $params['country_id']);
            })
            ->when((Arr::has($params, 'place_id') === true && Arr::has($params, 'zipCode') === true), function ($query) use ($params) {
                return $query->whereHas('zip_codes', function ($q) use ($params) {
                    return $q->whereHas('service_areas', function ($qry) use ($params) {
                        return $qry->where('place_id', $params['place_id'])
                            ->whereHas('zipCode', function ($q) use ($params) {
                                $q->where('code', '=', $params['zipCode']);
                            });
                    });
                });
            })
            ->when((Arr::has($params, 'place_id') === true && Arr::has($params, 'zipCode') === false), function ($query) use ($params) {
                return $query->whereHas('zip_codes', function ($q) use ($params) {
                    return $q->whereHas('service_areas', function ($qry) use ($params) {
                        return $qry->where('place_id', $params['place_id']);
                    });
                });
            })
            ->when(Arr::has($params, 'service'), function ($query) use ($params) {
                return $query->whereHas('provider_services', function ($q) use ($params) {
                    return $q->where('service_id', $params['service'])
                        ->where('status', 1);
                });
            })
            ->when(Arr::has($params, 'service_type') == true, function ($query) use ($params) {
                return $query
                    ->where('service_type', $params['service_type'])
                    ->orWhere('service_type', 'MULTIPLE');
            })
            ->when(Arr::has($params, 'vehicle_type_id') == true, function ($query) use ($params) {
                return $query->has('vehicles')->whereHas('vehicles', function ($q) use ($params) {
                    return $q->where('vehicle_type_id', $params['vehicle_type_id']);
                });
            })
            ->when(Arr::has($params, 'subService'), function ($query) use ($params) {
                return $query->whereHas('provider_services', function ($q) use ($params) {
                    return $q->where('sub_service_id', $params['subService'])
                        ->where('status', 1);
                });
            })
            ->when(Arr::has($params, 'zipCode'), function ($query) use ($params) {
                return $query->whereHas('zip_codes', function ($q) use ($params) {
                    return $q->where('code', $params['zipCode']);
                });
            })
            ->when(Arr::has($params, 'rating'), function ($query) use ($params) {
                return $query->where('rating', '>=', $params['rating']);
            })
            ->when(Arr::has($params, 'today'), function ($query) {
                return $query->whereHas('provider_schedules', function ($q) {
                    return $q->where('full_date', '>=', Carbon::now()->format('Y-m-d'));
                });
            })
            ->when(Arr::has($params, 'week'), function ($query) {
                return $query->whereHas('provider_schedules', function ($q) {
                    return $q->where('full_date', '<=', Carbon::now()->addWeek()->format('Y-m-d'));
                });
            })
            ->when(Arr::has($params, 'hourly'), function ($query) {
                return $query->where('provider_type', ProviderType::INDIVIDUAL);
            })
            ->when(Arr::has($params, 'quotation'), function ($query) {
                return $query->where('provider_type', ProviderType::BUSINESS);
            })
            ->when(Arr::has($params, ['date']), function ($query) use ($params) {
                $day = Carbon::parse($params['date'])->dayName;
                return $query->whereHas(
                    'schedules',
                    function ($qry) use ($day, $params) {
                        return $qry->where('day', $day)->when(
                            $params['slot'],
                            function ($q) use ($params) {
                                return $q->whereTime('from_time', '<=', $params['slot'][0])
                                    ->whereTime('to_time', '>=', $params['slot'][1]);
                            }
                        );
                    }
                )->whereHas('blockedSlots', function ($qry) use ($params) {
                    return $qry;
                    // return $qry->where(function ($bqry) use ($params) {
                    //     return $bqry->Where('date', $params['date'])
                    //         ->when($params['slot'], function ($q) use ($params) {
                    //             return $q->whereTime('from_time', '<=', $params['slot'][0])
                    //                 ->whereTime('to_time', '>=', $params['slot'][1]);
                    //         });
                    // });
                });
            })
            ->paginate(AppConst::PAGE_SIZE)
            ->withQueryString();
    }

    /**
     * providerProfile
     * @param int $id
     * @param bool $rtn
     * @return array
     */
    public function providerProfile(int $id, $rtn = false, $hasProfile = true): array
    {
        // $userColumns = [
        //     'id',
        //     'first_name',
        //     'last_name',
        //     'email',
        //     'phone',
        //     'image',
        //     'rating',
        //     'provider_type',
        //     'account_type',
        //     'status',
        //     'bio',
        //     'verified_at',
        //     'service_type',
        //     'phone_verification',
        //     'credit',
        //     'rating',
        //     'social_type',
        // ];

        $with = [
            'provider_profile',
            // :id,provider_id,business_name,city,hourly_rate,total_earn',
            'payment_methods',
            'docs_licenses',
            'schedules:id,provider_id,day,from_time,to_time,is_custom',
            'blockedSlots' => function ($q) {
                return $q->select('id', 'provider_id', 'date', 'from_time', 'to_time')
                    ->where('date', '>=', Carbon::now()->format('Y-m-d'));
            },
            'portfolios' => function ($qry) {
                return $qry->approved_portfolios()
                    ->select('id', 'provider_id', 'image', 'description');
            },
            'provider_services' => function ($qry) {
                return $qry->select([
                    'id',
                    'provider_id',
                    'service_id',
                    'sub_service_id',
                    'status'
                ])
                    ->where('status', 1)
                    ->with([
                        // 'service' => function ($q) {
                        //     return $q->whereStatus(1)->select('id', 'name');
                        // },
                        'sub_service' => function ($q) {
                            return $q->whereStatus(1)->select('id', 'name');
                        }
                    ]);
            },
            'plans:id,provider_id,title,price,type,duration,off,description',
        ];

        $withCount = [
            'user_feedbacks',
            'provider_completed_service_requests as provider_service_requests_count',
            'provider_service_requests as worked_hours' => function ($q) {
                return $q->where('is_completed', true)
                    ->select(DB::raw('SUM(worked_hours) as worked_hours'));
            }
        ];

        $provider = $this->provider();
        if ($hasProfile) {
            $provider = $provider->has('provider_profile');
        }
        $provider = $provider->when(!$rtn, fn($query) => $query->whereStatus(AppConst::ACTIVE))
            ->with($with)
            ->withCount($withCount)
            ->find($id);

        // with([
        //     'provider_profile',
        //     'docs_licenses',
        //     'provider_schedules.time_slots',
        //     'vehicles.vehicle_type',
        //     'provider_services' => function ($qry) {
        //         return $qry->where('status', 1)->with([
        //             'service' => function ($qry) {
        //                 return $qry->whereStatus(1);
        //             }, 'sub_service' => function ($qry) {
        //                 return $qry->whereStatus(1);
        //             }
        //         ]);
        //     },
        //     'provider_schedules' => function ($q) {
        //         return $q->latest()->take(1);
        //     },
        //     'portfolios' => function ($qry) {
        //         return $qry->approved_portfolios();
        //     },
        // ])->withCount(['provider_service_requests ' => function ($q) {
        //         return $q->whereIs_completed(true);
        // }])->provider()->


        // $feedback = Feedback::whereFor_user_id($id)
        //     ->where('provider_id', null)
        //     ->with('user')
        //     ->latest()
        //     ->get();
        // if (isset($feedback) && $feedback !== null) {
        //     return ['provider' => $provider, 'feedback' => $feedback];
        // }
        return ['provider' => $provider];
    }

    /**
     * business_profile
     * @return HasOne
     */
    public function business_profile(): HasOne
    {
        return $this->hasOne(BusinessProfile::class, 'user_id', 'id');
    }

    /**
     * forgetPassword
     * @param User $user
     * @return bool
     */
    public function forgotPassword(User $user): bool
    {
        $status = Password::sendResetLink(['email' => $user->email]);
        return $status === Password::RESET_LINK_SENT;
    }


    /**
     * The channels the user receives notification broadcasts on.
     *
     * @return string
     */
    public function receivesBroadcastNotificationsOn(): string
    {
        return 'users.' . $this->id;
    }

    /**
     * The channels the user receives notification broadcasts on.
     * @param string $first_name
     * @param string $last_name
     *
     * @return string
     */
    private static function genrateUsername(string $first_name, string $last_name): string
    {
        $username = strtolower($first_name . $last_name);
        $username = preg_replace('/[^A-Za-z0-9\-]/', '', $username);
        $username = preg_replace('/-+/', '-', $username);
        $username = preg_replace('/-+$/', '', $username);
        $username = preg_replace('/^-+/', '', $username);
        $username = $username . rand(100, 999);
        $user = User::whereUsername($username)->exists();
        if ($user) self::genrateUsername($first_name, $last_name);
        return $username;
    }

    // Boot method
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->rating = 5.00;
            if ($model->first_name && $model->last_name) {
                $model->username = self::genrateUsername($model->first_name, $model->last_name);
            }
        });
        static::saving(function ($model) {
            if (!$model->username && $model->first_name && $model->last_name) {
                $model->username = self::genrateUsername($model->first_name, $model->last_name);
            }
        });
        static::deleting(function ($model) {
            $common = new Common();
            if ($model->image) $common->delete_media($model->image);
            $model->load('portfolios');
            $model->load('docs_licenses');
            if (!empty($model->portfolios)) {
                foreach ($model->portfolios as $portfolio) {
                    if ($portfolio->image) $common->delete_media($portfolio->image);
                    $portfolio->delete();
                }
            }
            if (!empty($model->docs_licenses)) {
                foreach ($model->docs_licenses as $license) {
                    if ($license->url) $common->delete_media($license->url);
                    $license->delete();
                }
            }
        });
    }


    /**
     * @return HasMany
     */
    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class);
    }

}
