<?php

namespace App\Models;

use App\Models\ServiceRequest;
use Illuminate\Database\Eloquent\Model;

class WorkedTime extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'service_request_id', 'start_at', 'end_at', 'is_paused',
    ];

    /**
     * Relationship with QuotationInfo
     *
     */
    public function service_request()
    {
        return $this->belongsTo(ServiceRequest::class);
    }
}
