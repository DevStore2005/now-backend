<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'provider_id',
        'user_id',
        'payment_id',
        'refund_id',
        'amount',
        'amount_captured',
        'status',
        'payment_method',
        'service_request_id',
        'is_credit',
        'is_payable',
        'type',
        'fw_transaction_id',
        'card_info',
        'customer_info',
    ];

    protected $casts = [
        'card_info' => 'array',
        'customer_info' => 'array',
    ];

    /**
     * Relationship with Feeback transaction
     *
     */
    public function service_request()
    {
        return $this->belongsTo(ServiceRequest::class);
    }

    /**
     * Relationship with Provider
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function provider()
    {
        return $this->belongsTo(User::class, "provider_id", "id");
    }

    /**
     * Relationship with User
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, "user_id", "id");
    }
}
