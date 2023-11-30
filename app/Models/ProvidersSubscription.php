<?php

namespace App\Models;

use App\Models\Plan;
use App\Utils\AppConst;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProvidersSubscription extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'plan_id',
        'service_request_id',
        'type',
        'duration',
        'off',
        'start_date',
        'end_date',
        'status',
    ];

    /**
     * Subscription belongs to User.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Subscription belongs to Plan.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function service_request(): BelongsTo
    {
        return $this->belongsTo(ServiceRequest::class);
    }

    /**
     * Subscription belongs to subscription_histories.
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function subscription_histories(): HasMany
    {
        return $this->hasMany(SubscriptionHistory::class, 'providers_subscription_id', 'id');
    }

    /**
     * Scope a query to only include active subscriptions.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where([
            'status' => AppConst::ACTIVE,
            ['end_date', '>=', now()->format('Y-m-d')]
        ]);
    }

    function future_subscriptions()
    {
        return $this->hasMany(SubscriptionHistory::class, 'providers_subscription_id', 'id')
        ->whereNull('status')
        ->where('deduction_date', '>', now()->format('Y-m-d'));
    }

}
