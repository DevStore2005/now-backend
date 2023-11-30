<?php

namespace App\Http\Resources;

use App\Utils\ProductType;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $arr = $this->type == ProductType::FOOD ? ['restaurant' => $this->restaurant] : ['grocery_store' => $this->grocery_store];
        return [
            'error' => false,
            'data' => [
                'id' => $this->id,
                'restaurant_id' => $this->restaurant_id,
                'food_id' => $this->food_id,
                'name' => $this->name,
                'discount_price' => $this->discount_price,
                'price' => $this->price,
                'quantity' => $this->quantity,
                'description' => $this->description,
                'image' => $this->image,
                'status' => $this->status,
                'created_at' => $this->created_at,
                'updated_at' => $this->updated_at,
            ] + $arr,
            'message' => 'Product retrieved successfully.'
        ];
    }
}
