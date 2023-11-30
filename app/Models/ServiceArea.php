<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceArea extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'place_id',
    ];

    /**
     * @var $primaryKey
     */
    protected $primaryKey = 'id';

    /**
     * The "type" of the primary key UUID.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * @var $autoIncrement
     */
    public $incrementing = false;

    /**
     * Relationship With Zip Codes
     * 
     * @return BelongsTo
     */
    public function zipCode()
    {
        return $this->belongsTo(ZipCode::class);
    }
}
