<?php

namespace App\Models;

use App\Models\Country;
use App\Models\ZipCode;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class State extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'country_id',
        'country_code',
    ];

    /**
     *  Get the country that owns the state.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * Get the city's zipcodes.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function zip_codes()
    {
        return $this->belongsToMany(ZipCode::class);
    }


    /**
     * @return HasMany
     */
    public function cities(): HasMany
    {
        return $this->Hasmany(City::class, 'state_id');
    }
}
