<?php

namespace App\Models;

use Stripe\Charge;
use Stripe\Stripe;
use Stripe\Customer;
use App\Models\Product;
use App\Utils\AppConst;
use App\Models\Feedback;
use App\Utils\OrderType;
use App\Utils\ProductType;
use App\Http\Helpers\Common;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'order_no',
        'user_id',
        'restaurant_id',
        'grocer_id',
        'transaction_id',
        'category_id',
        "address_id",
        'product_id',
        'food_id',
        'type',
        'status',
        'address',
        'description',
        'price',
        'total_amount',
        'quantity',
        'discount',
        'payment_status',
    ];

    /**
     * Relationship with Restaurant
     *
     * @return BelongsTo
     */
    public function restaurant()
    {
        return $this->belongsTo(BusinessProfile::class, 'restaurant_id');
    }

    /**
     * Relationship with grocery store
     *
     * @return BelongsTo
     */
    public function grocery_store()
    {
        return $this->belongsTo(BusinessProfile::class, 'grocer_id');
    }

    /**
     * Relationship with grocery product
     *
     * @return BelongsTo
     */
    public function product()
    {
        return $this->belongsTo(Product::class)->where('type', ProductType::GROCERY);
    }

    /**
     * Relationship with grocery product
     *
     * @return BelongsTo
     */
    public function food()
    {
        return $this->belongsTo(Product::class)->where('type', ProductType::FOOD);
    }

    /**
     * Relationship with Feedback
     *
     * @return HasOne
     */
    public function feedback()
    {
        return $this->hasOne(Feedback::class);
    }


    /**
     * Create new order
     * @param array $oderData
     */
    public function createOrder($orderData)
    {
        $user = auth()->user();
        $type = isset($orderData['type']) ? $orderData['type'] : "CARD";
        $carts = $orderData['cart_ids'];
        $order = [];
        $lastOrderIid = $this->latest()->first() ? $this->latest()->first()->id : 1;
        $totalPrice = 0;
        $paid = false;
        $transaction_id = null;
        foreach ($carts as $key => $cartId) {
            $cart = Cart::find($cartId);
            $totalPrice = $cart->price + $totalPrice;
        }
        foreach ($carts as $key => $cartId) {
            $newOrder = new Order();
            $cart = Cart::find($cartId);
            if ($cart) {
                $newOrder->order_no = '#' . str_pad($lastOrderIid + $key + 1, 8, "0", STR_PAD_LEFT);
                $newOrder->user_id = $user->id;
                isset($orderData['address']) && $newOrder->address = $orderData['address'];
                isset($orderData['address_id']) && $newOrder->address_id = $orderData['address_id'];
                isset($orderData['description']) && $newOrder->description = $orderData['description'];
                $newOrder->total_amount = $cart->price;
                $newOrder->quantity = $cart->quantity;
                $newOrder->status = AppConst::PENDING;
                $newOrder->payment_type = $type;
                if ($cart && $cart->food_id) {
                    $cart->load('food');
                    $cart->food_id && $newOrder->food_id = $cart->food_id;
                    $cart->food_id && $newOrder->restaurant_id = $cart->food->restaurant_id;
                    $cart->food->category_id && $newOrder->category_id = $cart->food->category_id;
                    $newOrder->price = $cart->price;
                    $newOrder->type = ProductType::FOOD;
                }

                if ($cart && $cart->product_id) {
                    $cart->load('product');
                    $cart->product_id && $newOrder->product_id = $cart->product_id;
                    $cart->product_id && $newOrder->grocer_id = $cart->product->grocer_id;
                    $cart->product->category_id && $newOrder->category_id = $cart->product->category_id;
                    $newOrder->type = ProductType::GROCERY;
                    $newOrder->price = $cart->price;
                }

                if ($type != OrderType::CASH_ON_DELIVERY) {
                    if (isset($orderData['remember_card']) && isset($orderData['token']) && $orderData['remember_card'] == true) {
                        // Common::stripe_add_card($orderData['token'], null);
                        // $result = Common::stripe_payment(null, $newOrder->total_amount, "new order");
                        // if ($result['error'] == true) {
                        //     return $result;
                        // } else {
                        //     $newOrder->transaction_id = $result['data']->id;
                        //     $newOrder->payment_status = AppConst::PAID;
                        // }
                    } elseif (isset($orderData['token']) && isset($orderData['remember_card']) == false) {
                        if($paid == false){
                            $result = Common::stripe_payment($orderData['token'], $totalPrice, "new order");
                            if ($result['error'] == true) {
                                return $result;
                            } else {
                                $newOrder->transaction_id = $result['data']->id;
                                $transaction_id = $result['data']->id;
                                $newOrder->payment_status = AppConst::PAID;
                                $paid = true;
                            }
                        } else {
                            $newOrder->transaction_id = $transaction_id;
                            $newOrder->payment_status = AppConst::PAID;
                        }
                    } elseif ($user->defaultPaymentMethod()) {
                        if (isset($orderData['card_id'])) {
                            Stripe::setApiKey(config('services.stripe.secret'));
                            Customer::update($user->stripe_id, [
                                'default_source' => $orderData['card_id']
                            ]);
                        }
                        $result = Common::stripe_payment(null, $newOrder->total_amount, "new order");
                        if ($result['error'] == true) {
                            return $result;
                        } else {
                            $newOrder->transaction_id = $result['data']->id;
                            $newOrder->payment_status = AppConst::PAID;
                        }
                    }
                } else {
                    $newOrder->payment_status = AppConst::PENDING;
                }

                $newOrder->save();
                if ($newOrder) {
                    if ($cart->food) {
                        $cart->food->total_order = $cart->food->total_order !== null ? $cart->food->total_order + 1 : 1;
                        $cart->food->save();
                        $cart->food->load('restaurant');
                        $cart->food->restaurant->total_order = $cart->food->restaurant->total_order !== null ? $cart->food->restaurant->total_order + 1 :  1;
                        $cart->food->restaurant->save();
                    }
                    if ($cart->product) {
                        $cart->product->total_order = $cart->product->total_order !== null ? $cart->product->total_order + 1 : 1;
                        $cart->product->save();
                        $cart->product->load('grocery_store');
                        $cart->product->grocery_store->total_order = $cart->product->grocery_store->total_order !== null ? $cart->product->grocery_store->total_order + 1 : 1;
                        $cart->product->grocery_store->save();
                    }
                    $cart->delete();
                } else {
                    return ['error' => true, 'message' => 'some thing went wrong', 'success order' => $order];
                }
                array_push($order, $newOrder);
            } else {
                return ['error' => true, 'message' => 'Cart no ' . $cartId . ' not found', 'success order' => $order];
            }
        }
        return ['error' => false, 'message' => 'Order created successfully', 'data' => $order];
    }
}
