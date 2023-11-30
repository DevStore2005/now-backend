<?php

namespace App\Models;

use App\Models\Option;
use App\Models\Question;
use App\Models\ServiceRequest;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RequestInfo extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'service_request_id', 'question_id', 'option_id', 'detail', 'reply', 'duration', 'price'
    ];

    /**
     * Relationship With ServiceRequest
     *
     * @return BelongsTo
     */
    public function service_request()
    {
        return $this->belongsTo(ServiceRequest::class);
    }

    /**
     * Relationship With Questions
     *
     * @return BelongsTo
     */
    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    /**
     * Relationship With Questions
     *
     * @return BelongsTo
     */
    public function option()
    {
        return $this->belongsTo(Option::class);
    }
}
