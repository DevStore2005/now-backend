<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
       'sender_id', 'receiver_id', 'message', 'service_request_id', 'is_admin', 'is_read'
    ];

    /**
     * Relationship with sender
     *
     * @return BelongsTo
     */
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id', 'id');
    }

    /**
     * Relationship with receiver
     *
     * @return BelongsTo
     */
    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id', 'id');
    }

    /**
     * Relationship with service request
     *
     * @return BelongsTo
     */
    public function service_request()
    {
        return $this->belongsTo(ServiceRequest::class);
    }
}
