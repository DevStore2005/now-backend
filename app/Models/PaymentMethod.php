<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'icon',
        'country_id',
    ];

    public function providers()
    {
        return $this->belongsToMany(
            User::class,
            'payment_method_provider',
            'payment_method_id',
            'provider_id'
        );
    }
}
