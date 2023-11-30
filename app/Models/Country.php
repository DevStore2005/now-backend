<?php

namespace App\Models;

use App\Models\City;
use App\Models\State;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Country extends Model
{
    /**
     *  The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'iso2',
        'iso3',
        'currency',
        'currency_name',
        'emoji',
        'is_default',
        'is_active',
        'stripe_enable',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'is_default' => 'boolean',
        'is_active' => 'boolean',
        'stripe_enable' => 'boolean',
    ];


    /**
     * @return HasMany
     */
    public function cities(): HasMany
    {
        return $this->hasMany(City::class, 'country_id');
    }


    /**
     * @return HasMany
     */
    public function states(): HasMany
    {
        return $this->hasMany(State::class);
    }
}
