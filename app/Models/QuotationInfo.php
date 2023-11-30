<?php

namespace App\Models;

use App\Models\ServiceRequest;
use Illuminate\Database\Eloquent\Model;

class QuotationInfo extends Model
{
    protected $fillable = [
        'detail',
        'reply',
        'duration',
        'price',
        'name',
        'email',
        'phone',
        'from_address',
        'start_lat',
        'start_lng',
        'to_address',
        'end_lat',
        'end_lng',
        'date',
    ];

    /**
     * Relationship with QuotationInfo
     *
     */
    public function service_request()
    {
        return $this->hasOne(ServiceRequest::class);
    }

    /**
     * Relationship with Media
     *
     */
    public function quotation_media()
    {
        return $this->hasMany(Media::class);
    }
}
