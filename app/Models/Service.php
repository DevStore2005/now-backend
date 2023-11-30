<?php

namespace App\Models;

use Illuminate\Support\Arr;
use App\Http\Helpers\Common;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Service extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = [
        'name',
        'status',
        'image',
        'og_title',
        'og_description',
        'country_id',
    ];


    public function sub_services()
    {
        return $this->hasMany(SubService::class, 'service_id');
    }

    public function provider_services()
    {
        return $this->hasMany(ProviderService::class);
    }

    /**
     * Create new service or update exsiting
     *
     * @param array $service
     * @return object
     */
    public function createService($service)
    {
        $common = new Common();
        if (isset($service['id']) && !empty($service['id'])) {
            if (isset($service['image'])) {
                $getService = Service::where('image', '!=', null)->find($service['id']);
                if ($getService !== null && $getService->image !== '') {
                    $common->delete_media($getService->image);
                }
                $image = $common->store_media($service['image'], 'services');
                $service['image'] = $image;
            }
            $service['country_id'] = isset($service['default_country']) ? $service['default_country']['id'] : null;
            $updatedService = Service::find($service['id'])->update(Arr::except($service, ['_token']));
            if (isset($service['og_image'])) {
                $updatedService->clearMediaCollection('og_image');
                $updatedService->addMedia($service['og_image'])->toMediaCollection('og_image');
            }
            return $updatedService;
        } else {
            $image = $common->store_media($service['image'], 'services');
            $newService = Service::create([
                'name' => $service['name'],
                'image' => $image,
                'og_title' => $service['og_title'],
                'og_description' => $service['og_description'],
                'country_id' => isset($service['default_country']) ? $service['default_country']['id'] : null,
            ]);

            if (isset($service['og_image'])) {
                $newService->addMedia($service['og_image'])->toMediaCollection('og_image');
            }
            return $newService;
        }
    }

    // Boot method
    protected static function boot()
    {
        parent::boot();
        static::deleting(function ($service) {
            if ($service->image) {
                $common = new Common();
                $common->delete_media($service->image);
            }
            if ($service->sub_services()->exists()) {
                $service->load('sub_services');
                foreach ($service->sub_services as $subService) {
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
                    if ($subService->service_contents()->exists()) $subService->service_contents()->delete();
                    $subService->delete();
                }
            }
        });
    }

    /**
     * @return void
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('og_image')
            ->singleFile();
    }

    /**
     * @return string
     */
    public function getOgImageAttribute()
    {
        return $this->getFirstMediaUrl('og_image');
    }
}
