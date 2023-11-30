<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FrontPage extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'country_id',
        'title',
        'description',
        'image',
        'type',
    ];

    public function extra_infos()
    {
        return $this->hasMany(ExtraInfo::class, 'type', 'type');
    }

    public function app_urls()
    {
        return $this->extra_infos()->whereType("App");
    }

    public function extra_info()
    {
        return $this->hasOne(ExtraInfo::class, 'type', 'type')->whereType("Info");
    }


}
