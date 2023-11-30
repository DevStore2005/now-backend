<?php

namespace App\Models;

use App\Models\User;
use App\Utils\ServiceType;
use App\Models\VehicleType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Vehicle extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'provider_id',
        'vehicle_type_id',
        'name',
        'model',
        'number',
        'condition',
        'company_name'
    ];

    /**
     * Relationship with provider
     *
     * @return BelongsTo
     */
    public function provider()
    {
        return $this->belongsTo(User::class, 'provider_id', 'id');
    }

    /**
     * Relationship with provider
     *
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'provider_id', 'id');
    }

    /**
     * Relationship with vehicle type
     *
     * @return BelongsTo
     */
    public function vehicle_type()
    {
        return $this->belongsTo(VehicleType::class);
    }


    public function storeVehicle($vehicles)
    {
        $provider = auth()->user();
        $createdVehicles = [];
        foreach ($vehicles as $key => $vehicle) {
            $vehicle['provider_id'] = $provider->id;
            $createdVehicles[$key] = $this->create($vehicle);
        }
        $provider->service_type = ServiceType::MOVING;
        $provider->save();
        return $createdVehicles;
    }
}
