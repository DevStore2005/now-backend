<?php

namespace App\Models;

use App\Http\Helpers\Common;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VehicleType extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title', 'image', 'type'
    ];

    /**
     * Relationship with vehicle
     *
     * @return HasMany
     */
    public function vehicles()
    {
        return $this->hasMany(Vehicle::class);
    }

    /**
     * add new vehicle type
     * @param array $data
     * @return VehicleType
     * @throws \Exception
     */
    public function addNewVehicleType($formData){
        $common = new Common();
        $formData['image'] = $common->store_media($formData['image'], 'vehicle_types');
        try {
            return VehicleType::create($formData);
        } catch (\Throwable $th) {
            $common->delete_media($formData['image']);
            throw $th;
        }
    }

    /**
     * update vehicle type
     * @param array $data
     * @param VehicleType $vehicleType
     */
    public function updateVehicleType($formData, $vehicleType){
        $common = new Common();
        try {
            if(isset($formData['image'])) {
                $common->delete_media($vehicleType->image);
                $formData['image'] = $common->store_media($formData['image'], 'vehicle_types');
            }
            $vehicleType->update($formData);
            return $vehicleType;
        } catch (\Throwable $th) {
            $common->delete_media($formData['image']);
            throw $th;
        }
    }
}
