<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Slider extends Model
{
    use HasFactory;

    protected $fillable = [
        'description',
        'bg_image',
        'front_image',
        'country_id',
        'status'
    ];

    /**
     * @param $query
     * @return mixed
     */
    public function scopeActive($query): mixed
    {
        return $query->where('status', 1);
    }
}
