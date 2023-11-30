<?php

namespace App\Models;

use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BusinessProfile extends Model
{
    protected $table = 'business_profile';
    protected $fillable = ['name', 'address', 'user_id', 'city', 'address', 'business_phone', 'state', 'about', 'rating', 'cover_image'];

    /**
     * Relationship with User
     *
     * @return BelongsTo User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship with Products
     *
     * @return HasMany Product
     */
    public function products()
    {
        return $this->hasMany(Product::class, 'grocer_id');
    }

    /**
     * Relationship with Foods
     *
     * @return HasMany Product
     */
    public function foods()
    {
        return $this->hasMany(Product::class, 'restaurant_id');
    }

    /**
     * Relationship with product Orders
     *
     * @return HasMany Order
     */
    public function product_orders()
    {
        return $this->hasMany(Order::class, 'grocer_id');
    }

    /**
     * Relationship with food Orders
     *
     * @return HasMany Order
     */
    public function food_orders()
    {
        return $this->hasMany(Order::class, 'restaurant_id');
    }
}
