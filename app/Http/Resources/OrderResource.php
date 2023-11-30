<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $arr = [];
        if ($this->restaurant_id != null) {
            $arr = ['restaurant' => $this->restaurant, 'food' => $this->food];
        }
        if ($this->grocer_id != null) {
            $arr = ['grocery_store' => $this->grocery_store, 'product' => $this->product];
        }
        return [
            'error' => false,
            'data' => [
                "id" => $this->id,
                "order_no" => $this->order_no,
                "user_id" => $this->user_id,
                "restaurant_id" => $this->restaurant_id,
                "grocer_id" => $this->grocer_id,
                "transaction_id" => $this->transaction_id,
                "category_id" => $this->category_id,
                "address_id" => $this->address_id,
                "product_id" => $this->product_id,
                "food_id" => $this->food_id,
                "type" => $this->type,
                "status" => $this->status,
                "address" => $this->address,
                "description" => $this->description,
                "price" => $this->price,
                "total_amount" => $this->total_amount,
                "quantity" => $this->quantity,
                "discount" => $this->discount,
                "payment_type" => $this->payment_type,
                "payment_status" => $this->payment_status,
                "created_at" => $this->created_at,
                "updated_at" => $this->updated_at,
            ] + $arr,
            'message' => 'Product retrieved successfully.'
        ];
    }
}
