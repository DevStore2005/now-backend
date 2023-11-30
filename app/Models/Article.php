<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Article extends Model
{
    use SoftDeletes;

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     * @var string[]
     */
    protected $fillable = [
        'title',
        'sub_service_id',
        'content',
        'slug',
        'for_role',
        'published_at'
    ];

    protected $with = ['sub_service:id,name'];

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
     * The Article belongs to SubService.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function sub_service(): BelongsTo
    {
        return $this->belongsTo(SubService::class);
    }

    /**
     * @param array $data
     * @return Article|array
     */
    public function createArticle(array $data)
    {
        $data['slug'] = Str::slug($data['title']);
        return $this->create($data);
    }

    /**
     * boot
     * @return void
     */
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($article) {
            $article->slug = $article->_generateSlug($article->title);
            $article->title = Str::title($article->title);
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
}
