<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Page extends Model implements HasMedia
{
    use InteractsWithMedia;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'name',
        'content',
        'type',
        'image',
        'og_title',
        'og_description',
        'country_id',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('og_image')
            ->singleFile();
    }

    // auto attch featured image when get blog
    public function getOgImageAttribute()
    {
        return $this->getFirstMediaUrl('og_image');
    }
}
