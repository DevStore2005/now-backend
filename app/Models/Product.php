<?php

namespace App\Models;

use App\Models\Order;
use App\Models\Category;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    protected $table = 'products';

    protected $fillable = [
        'image',
        'category_id',
        'name',
        'price',
        'description',
        'restaurant_id',
        'grocer_id',
        'quantity',
        'type',
        'country_id',
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
     * Relationship with Order
     *
     * @return HasMany
     */
    public function grocery_order()
    {
        return $this->hasMany(Order::class, 'grocer_id', 'id');
    }

    /**
     * Relationship with Order
     *
     * @return HasMany
     */
    public function food_order()
    {
        return $this->hasMany(Order::class, 'restaurant_id', 'id');
    }

    /**
     * Relationship with Category
     *
     * @return \Illuminate\Database\Eloquent\Relations\belongsTo
     */
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
}
