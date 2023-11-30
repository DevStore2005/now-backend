<?php

namespace App\Models;

use App\Models\City;
use App\Models\User;
use App\Models\State;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ZipCode extends Model
{

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'zip_codes';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'code',
    ];

    /**
     * Relationship With Users
     *
     * @return BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(User::class);
        // return $this->belongsToMany(User::class, 'user_zip_code', 'zip_code_id', 'user_id');
    }

    /**
     * Relationship With Users
     *
     * @return BelongsToMany
     */
    public function providers()
    {
        return $this->belongsToMany(User::class);
        // return $this->belongsToMany(User::class, 'user_zip_code', 'zip_code_id', 'user_id');
    }

    /**
     * Relationship With Cities
     *
     * @return BelongsToMany
     */
    public function cities(){
        return $this->belongsToMany(City::class);
    }

    /**
     * Relationship With States
     *
     * @return BelongsToMany
     */
    public function states()
    {
        return $this->belongsToMany(State::class);
    }

    /**
     * Relationship With Service Areas
     * 
     * @return HasMany
     */
    public function service_areas()
    {
        return $this->hasMany(ServiceArea::class);
    }
}
