<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AddressResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'error' => false,
            'data' => [
                'id' => $this->id,
                'type' => $this->type,
                'address' => $this->address,
                'flat_no' => $this->flat_no,
                'zip_code' => $this->zip_code,
                'country' => $this->country,
                'created_at' => $this->created_at,
                'updated_at' => $this->updated_at,
            ],
            'message' => 'OK',
        ];
    }
}   
