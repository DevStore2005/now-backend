<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['user_id', 'type', 'address', 'flat_no', 'zip_code', 'state', 'city'];

    /**
     * New Address
     * @param array $addressData
     * @param int $userId
     * @return Address
     */
    public function createAddress(array $addressData, int $userId){
        return $this->create($addressData+['user_id'=>$userId]);
    }
}
