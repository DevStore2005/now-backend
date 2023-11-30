<?php

namespace App\Http\Resources;

use App\Http\Resources\BusinessProfileResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class OrdersResource extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'error' => false,
            'data' => $this->collection->map((function ($order) {
                if ($order->food_id || $order->product_id) {
                    $type = $order->product_id != null  ? 'product' : 'food';
                    if ($type == 'food') {
                        $data = [
                            'food' => $order->food,
                            'restaurant' => $order->restaurant
                        ];
                    } else {
                        $data = [
                            'product' => $order->product,
                            'grocery_store' => $order->grocery_store
                        ];
                    }
                    return [
                        'id' => $order->id,
                        'order_no' => $order->order_no,
                        'user_id' => $order->user_id,
                        'restaurant_id' => $order->restaurant_id,
                        'grocer_id' => $order->grocer_id,
                        'transaction_id' => $order->transaction_id,
                        'category_id' => $order->category_id,
                        'address_id' => $order->address_id,
                        'product_id' => $order->product_id,
                        'food_id' => $order->food_id,
                        'type' => $order->type,
                        'status' => $order->status,
                        'address' => $order->address,
                        'description' => $order->description,
                        'price' => $order->price,
                        'total_amount' => $order->total_amount,
                        'quantity' => $order->quantity,
                        'discount' => $order->discount,
                        'payment_type' => $order->payment_type,
                        'payment_status' => $order->payment_status,
                        'created_at' => $order->created_at,
                        'updated_at' => $order->updated_at,
                    ] + $data;
                }
            })),
            'message' => 'Order list retrieved successfully'
        ];
    }
}
