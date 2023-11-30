<?php

namespace App\Models;

use Spatie\MediaLibrary\HasMedia;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\SoftDeletes;

class Content extends Model implements HasMedia
{
    use SoftDeletes, InteractsWithMedia;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'blog_id',
        'content',
    ];

    /**
     * Get of blog
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function blog(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Blog::class);
    }

    /**
     * allow only sigle file registerMediaCollections
     * @return void
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('image')
        ->singleFile();
    }

}
