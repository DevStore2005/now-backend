<?php

namespace App\Models;

use App\Models\Comment;
use App\Models\Content;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\HasMedia;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Blog extends Model implements HasMedia
{
    use SoftDeletes, InteractsWithMedia;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'category_id',
        'title',
        'slug',
        'views',
        'og_title',
        'og_description',
    ];

    protected $with = ['contents:id,blog_id,content', 'category:id,name'];


    /**
     * Get of contents
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function contents(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Content::class);
    }

    /**
     * Get of category
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get of comments
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function comments(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Get of comments
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function lastest_comment(): HasOne
    {
        return $this->hasOne(Comment::class)->latest();
    }

    public function createBlog($blog)
    {
        $createdBlog = $this->create([
            'title' => $blog['title'],
            'category_id' => $blog['category_id'],
            'og_title' => $blog['og_title'],
            'og_description' => $blog['og_description'],
        ]);

        if (isset($blog['og_image']) && $blog->file('og_image')) {
            $blog->addMedia($blog['og_image'])->toMediaCollection('og_image');
        }

        if ($blog['featured_image']) $createdBlog->addMedia($blog['featured_image'])->toMediaCollection('featured_image');
        try {
            $contents = $createdBlog->contents()->createMany([
                ...array_map(fn($content) => [
                    'content' => $content
                ], $blog['content']),
            ]);
            foreach ($contents as $key => $content) {
                if (isset($blog['image'][$key + 1])) {
                    $content->addMedia($blog['image'][$key + 1])->toMediaCollection('image');
                }
            }
        } catch (\Throwable $th) {
            // remove blog if content not created and delete media
            $createdBlog->clearMediaCollection('featured_image');
            $createdBlog->delete();
        }
        return $createdBlog;
    }


    /**
     * update blog
     * @param $data
     * @param Blog $blog
     * @return Blog
     */
    public function updateBlog($data, $blog)
    {
        $blog->update([
            'title' => $data['title'],
            'category_id' => $data['category_id'],
            'og_title' => $data['og_title'],
            'og_description' => $data['og_description'],
        ]);
        if (isset($data['featured_image'])) {
            $blog->clearMediaCollection('featured_image');
            $blog->addMedia($data['featured_image'])->toMediaCollection('featured_image');
        }
        if (isset($data['og_image'])) {
            $blog->clearMediaCollection('og_image');
            $blog->addMedia($data['og_image'])->toMediaCollection('og_image');
        }

        try {
            foreach ($blog->contents as $key => $content) {
                $blog->contents[$key]->update([
                    'content' => $data['content'][$key + 1]
                ]);
                if (isset($data['image']) && isset($data['image'][$key + 1])) {
                    $content->clearMediaCollection('image');
                    $content->addMedia($data['image'][$key + 1])->toMediaCollection('image');
                }
            }
        } catch (\Throwable $th) {
            throw $th;
        }
        return $blog;
    }

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * boot
     * @return void
     */
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($blog) {
            $blog->slug = $blog->_generateSlug($blog->title);
            $blog->title = Str::title($blog->title);
        });
    }

    /**
     * @param string $title
     * @return string
     * @access private
     */
    private function _generateSlug($title)
    {
        $slug = Str::slug($title);
        if ($this->_hasSlug($slug)) {
            return $slug = $this->_generateSlug($slug . '-' . rand(1, 100));
        }
        return $slug;
    }


    private function _hasSlug($slug)
    {
        return static::whereSlug($slug)->exists();
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('featured_image')
            ->singleFile();
        $this->addMediaCollection('og_image')
            ->singleFile();
    }

    // auto attch featured image when get blog
    public function getFeaturedImageAttribute()
    {
        return $this->getFirstMediaUrl('featured_image');
    }

    public function getOgImageAttribute()
    {
        return $this->getFirstMediaUrl('og_image');
    }
}
