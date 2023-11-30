<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExtraInfo extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'type',
        'image',
        'url',
        'title_1',
        'title_2',
        'description_1',
        'description_2',
    ];

    public function front_page()
    {
        return $this->belongsTo(FrontPage::class, 'type', 'type');
    }
}
