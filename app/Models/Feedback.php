<?php

namespace App\Models;

use App\Models\Order;
use App\Models\ServiceRequest;
use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'service_request_id', 'user_id', 'provider_id', 'for_user_id', 'comment', 'rating',
    ];

    /**
     * Relationship with ServiceRequest
     *
     */
    public function service_request()
    {
        return $this->belongsTo(ServiceRequest::class);
    }

    /**
     * Relationship with provider
     *
     */
    public function provider()
    {
        return $this->belongsTo(User::class, 'provider_id', 'id');
    }

    /**
     * Relationship with provider
     *
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * Relationship with for user
     *
     */
    public function for_user()
    {
        return $this->belongsTo(User::class, 'for_user_id', 'id');
    }

    /**
     * Relationship with for order
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function ratings($provierId)
    {
        $avg = $this->where('for_user_id', $provierId)
            ->avg('rating');
        return [
            'total' => $this
                ->where('for_user_id', $provierId)
                ->count(),
            'average' => $avg ? round($avg, 1) : 0,
            'ratings' => [
                'five' => $this->where('for_user_id', $provierId)
                    ->where('rating', 5)
                    ->count(),
                'four' => $this->where('for_user_id', $provierId)
                    ->where('rating', 4)
                    ->count(),
                'three' => $this->where('for_user_id', $provierId)
                    ->where('rating', 3)
                    ->count(),
                'two' => $this->where('for_user_id', $provierId)
                    ->where('rating', 2)
                    ->count(),
                'one' => $this->where('for_user_id', $provierId)
                    ->where('rating', 1)
                    ->count(),
            ]
        ];
    }
}
