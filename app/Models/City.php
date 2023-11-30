<?php

namespace App\Models;

use App\Models\Country;
use App\Models\ZipCode;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class City extends Model
{
    /**
     *  The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'country_id'];


    /**
     * @return BelongsTo
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * @return BelongsToMany
     */
    public function zip_codes(): BelongsToMany
    {
        return $this->belongsToMany(ZipCode::class);
    }
}
