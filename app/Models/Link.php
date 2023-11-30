<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Link extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     * @var array
     */
    protected $fillable = [
        'page',
        'type',
        'is_blog',
        'name',
        'url',
        'description',
        'country_id',
    ];

    /**
     * Create new link
     * @param array $data
     */
    public function createLink(array $data)
    {
        $data['country_id'] = isset($data['default_country']) ? $data['default_country']['id'] : null;
        return Link::create($data);
    }

    /**
     * Update link
     *
     * @param array $data
     * @param  $this $link
     */
    public function updateLink(array $data, $link)
    {
        return $link->update($data);
    }
}

