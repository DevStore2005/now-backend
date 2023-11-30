<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Seo extends Model implements HasMedia
{
    use InteractsWithMedia;
    protected $fillable = [
        'page_name',
        'og_title',
        'og_description',
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
