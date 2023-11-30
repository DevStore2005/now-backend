<?php

namespace App\Models;

use App\Models\ServiceRequest;
use App\Models\ProviderSchedule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TimeSlot extends Model
{
    protected $fillable = ['provider_schedule_id', 'start', 'end'];

    /**
     * Relationship with ProviderChedule
     *
     */
    public function provider_schedule()
    {
        return $this->belongsTo(ProviderSchedule::class);
    }

    /**
     * Relationship with ServiceRequest
     *
     */
    public function service_requests()
    {
        return $this->belongsToMany(ServiceRequest::class);
    }

    /**
     * Get the Service Requets that are booked in this time slot
     *
     * @return BelongsTo
     */
    public function service_request(): BelongsTo
    {
        return $this->belongsTo(ServiceRequest::class);
    }
}
