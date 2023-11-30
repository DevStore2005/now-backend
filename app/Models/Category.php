<?php

namespace App\Models;

use App\Models\Product;
use App\Http\Helpers\Common;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    /**
     * fillable fields for a category
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'image',
        'type',
        'status',
    ];

    public function scopeCategory($query, $type = null)
    {
        return $query->where('type', $type);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    /**
     * create a new category
     * @param array $data
     */
    public function createCategory(array $data)
    {
        try {
            if (isset($data['image'])) {
                $common = new Common();
                $path = $common->store_media($data['image'], 'categories');
                $data['image'] = $path;
                Category::create($data);
            }
            Category::create($data);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * update a category
     * @param array $data
     * @param Category $category
     */
    public function updateCategory(array $data, Category $category)
    {
        try {
            if (isset($data['image'])) {
                $common = new Common();
                $category->image && $common->delete_media($category->image);
                $path = $common->store_media($data['image'], 'categories');
                $data['image'] = $path;
            }
            $category->update($data);
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
