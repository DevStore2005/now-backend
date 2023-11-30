<?php

namespace App\Http\Resources;

use App\Utils\BusinessProfileType;
use Illuminate\Http\Resources\Json\JsonResource;

class BusinessProfileResource extends JsonResource
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
                'name' => $this->name,
                'address' => $this->address,
                'city' => $this->city,
                'state' => $this->state,
                'country' => $this->country,
                'business_phone' => $this->business_phone,
                'website' => $this->website,
                'type' => $this->type,
                'restaurant_type' => $this->restaurant_type,
                'profile_image' => $this->profile_image,
                'cover_image' => $this->cover_image,
                'about' => $this->about,
                'rating' => $this->rating,
                'user' => $this->user,
                'created_at' => $this->created_at,
                'updated_at' => $this->updated_at,
            ],
            'message' => $this->type == BusinessProfileType::RESTAURANT ? 'Restaurant retrieved successfully.' : 'Grocery Store retrieved successfully.'
        ];
    }
}
