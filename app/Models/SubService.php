<?php

namespace App\Models;

use App\Models\Service;
use App\Models\Question;
use Illuminate\Support\Arr;
use App\Http\Helpers\Common;
use App\Models\ServiceContent;
use App\Models\ServiceRequest;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubService extends Model
{
    protected $fillable = ['service_id', 'name', 'credit', 'status', 'image', 'terms', 'view_type', 'show_in_the_footer'];

    public function scopeActive($query)
    {
        return $query->where('status', 'Active');
    }

    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    /**
     * Relationship with Questions
     *
     * @return HasMany
     */
    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    public function provider_sub_services()
    {
        return $this->hasMany(ProviderService::class)->latest();
    }

    /**
     * Relationship with Service Request
     *
     * @return HasMany
     */
    public function service_requests()
    {
        return $this->hasMany(ServiceRequest::class);
    }

    /**
     * Relationship with Service Content
     * @return  HasMany
     */
    public function service_contents()
    {
        return $this->hasMany(ServiceContent::class);
    }

    /**
     * Get the sub service that owns the Faq
     * @return HasMany
     * @access public
     */
    public function faq(): HasMany
    {
        return $this->hasMany(SubService::class);
    }

    /**
     * Create new service or update exsiting
     *
     * @param array $service
     * @return object
     */
    public function createSubService($subService)
    {
        $common  = new Common();
        if (isset($subService['id']) && !empty($subService['id'])) {
            if (isset($subService['image'])) {
                $getService = SubService::where('image', '!=', null)->find($subService['id']);
                if ($getService !== null && $getService->image !== '') {
                    $common->delete_media($getService->image);
                }
                $image = $common->store_media($subService['image'], 'sub_services');
                $subService['image'] = $image;
            }
            return SubService::find($subService['id'])->update(Arr::except($subService, ['_token']));
        } else {
            $image = $common->store_media($subService['image'], 'sub_services');
            return SubService::create([
                'name' => $subService['name'],
                'credit' => $subService['credit'],
                'service_id' => $subService['service_id'],
                'image' => $image,
                'view_type' => $subService['view_type'],
                'show_in_the_footer' => $subService['show_in_the_footer'],
                'terms' => $subService['terms']
            ]);
        }
    }

    // Boot method
    protected static function boot()
    {
        parent::boot();
        static::deleting(function ($subService) {
            if ($subService->image) {
                $common = new Common();
                $common->delete_media($subService->image);
            }
            if ($subService->questions()->exists()) {
                $subService->load('questions');
                foreach ($subService->questions as $question) {
                    $question->options()->delete();
                }
                $subService->delete();
            }
            if ($subService->service_contents()->exists()) {
                $subService->load('service_contents');
                foreach ($subService->service_contents as $serviceContent) {
                    if ($serviceContent->image) {
                        $common = new Common();
                        $common->delete_media($serviceContent->image);
                    }
                    $serviceContent->delete();
                }
            }
        });
    }
}
