<?php

namespace App\Models;

use App\Http\Helpers\Common;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceContent extends Model
{
    /**
     * The attributes that are mass assignable.
     * @var array
     */
    protected $fillable = [
        'sub_service_id',
        'title',
        'description',
        'image',
    ];

    /**
     * Relationship with Sub Service
     * @return BelongsTo
     */
    public function sub_service()
    {
        return $this->belongsTo(SubService::class);
    }

    /**
     * New service content.
     * @param  array $data
     */
    public function createServiceContent($data)
    {
        return ServiceContent::create($data);
    }

    /**
     * Update service content.
     * @param ServiceContent $serviceContent
     * @param  array $data
     */
    public function updateServiceContent(ServiceContent $serviceContent, $data)
    {
        return $serviceContent->update($data);
    }
    
    /**
     * Boot method
     */
    public static function boot()
    {
        parent::boot();
        static::deleting(function ($model) {
            if (isset($model->image)) {
                $common = new Common();
                $common->delete_media($model->image);
            }
        });
    }
}
