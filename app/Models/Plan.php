<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Plan extends Model
{
    use SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'stripe_id',
        'stripe_name',
        'price',
        'credit',
        'type',
        'duration',
        'off',
        'threshold',
        'description'
    ];

    /**
     * plan belongs to provider
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function provider()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * plan has many subscriptions
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function subscriptions()
    {
        return $this->hasMany(ProvidersSubscription::class);
    }
}
