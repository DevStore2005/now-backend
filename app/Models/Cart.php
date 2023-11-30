<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $table = 'cart';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'object_id',
        'product_id',
        'food_id',
        'price',
        'quantity',
    ];
    
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function food()
    {
        return $this->belongsTo(Product::class, 'food_id');
    }
}
