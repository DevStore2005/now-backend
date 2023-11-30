<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProviderProfile extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     * @access protected
     */
    protected $fillable = [
        'starting_rate',
        'hourly_rate',
        'country',
    ];
    
    /**
     * Relationship with User
     *
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(ProviderProfile::class, 'provider_id', 'id');
    }
}
