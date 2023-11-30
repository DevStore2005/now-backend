<?php

namespace App\Models;

use App\Models\User;
use App\Models\Service;
use App\Models\SubService;
use App\Utils\ServiceType;
use App\Utils\ProviderType;
use App\Utils\HttpStatusCode;
use App\Models\ServiceRequest;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProviderService extends Model
{
    protected $fillable = ['provider_id', 'service_id', 'sub_service_id'];


    /**
     * Relationship with sub services
     *
     * @return HasMany
     */
    public function sub_services()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Relationship with users
     *
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship with users
     *
     * @return BelongsTo
     */
    public function provider()
    {
        return $this->belongsTo(User::class, 'provider_id');
    }

    /**
     * Relationship with Service
     *
     * @return BelongsTo
     */
    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Relationship with sub Service
     *
     * @return BelongsTo
     */
    public function sub_service()
    {
        return $this->belongsTo(SubService::class);
    }

    /**
     * Relationship with Service requests
     *
     * @return HasMany
     */
    public function service_requests()
    {
        return $this->hasMany(ServiceRequest::class);
    }

    /**
     * Get The Provider Service
     *
     * @return <array> Service
     */
    public static function getProviderServices($provider_id)
    {
        $servicesData = Service::whereStatus(true)->with(['provider_services' => function ($qry) use ($provider_id) {
            return $qry->select('id', 'service_id', 'status')->where('provider_id', $provider_id);
        }, 'sub_services' => function ($qry) use ($provider_id) {
            return $qry->whereStatus(true)->select('id', 'name', 'service_id')->with(['provider_sub_services' => function ($qry) use ($provider_id) {
                return $qry->select('id', 'sub_service_id', 'status')->where('provider_id', $provider_id);
            }]);
        }])->get(['id', 'name']);

        foreach ($servicesData as $mainKey => $service) {
            $test = $service->provider_services->filter(function ($item) {
                return $item->status == 1;
            });
            if ($test->isEmpty()) {
                $servicesData[$mainKey]->status = false;
            } else {
                $servicesData[$mainKey]->status = true;
            }
        }
        return $servicesData;
        // return Service::whereHas('provider_services.sub_service', function ($qry) use ($provider_id) {
        //     return $qry->where('provider_id', $provider_id);
        // })->with(['sub_services' => function ($qry) use ($provider_id) {
        //     return $qry->select('id', 'name', 'service_id', 'status')->whereHas('provider_sub_services', function ($qry) use ($provider_id) {
        //         return $qry->where('provider_id', $provider_id);
        //     });
        // }])->get(['id', 'name', 'status']);
    }

    /**
     * Update service status
     *
     */
    public function updateStatusProviderService($data)
    {
        $user = auth()->user();
        $providerId = $user->id;
        $providerService = ProviderService::where('provider_id', $providerId);
        if (isset($data['service_id'])) {
            if ($providerService->where('service_id', $data['service_id'])->exists()) {
                $providerService->where('service_id', $data['service_id'])->update(['status' => $data['status']]);
                $this->_updateServiceType($user);
                return [
                    'result' => [
                        'error' => false,
                        'message' => 'Service status updated successfully.',
                        'data' => ProviderService::where('service_id', $data['service_id'])->with('service:id,name')->get()
                    ],
                    'statusCode' => HttpStatusCode::OK
                ];
            } else {
                return [
                    'result' => [
                        'error' => true,
                        'message' => 'Service not found.',
                    ],
                    'statusCode' => HttpStatusCode::NOT_FOUND
                ];
            }
        } elseif (isset($data['sub_service_id'])) {
            if ($providerService->where('sub_service_id', $data['sub_service_id'])->exists()) {
                $providerService->where('sub_service_id', $data['sub_service_id'])->latest()->update(['status' => $data['status']]);
                if ($user->provider_type != ProviderType::BUSINESS) {
                    $this->_updateStatus($user, $data);
                }
                $this->_updateServiceType($user);
                return ['result' => [
                    'error' => false,
                    'message' => 'Sub Service status updated successfully',
                    'data' => $providerService->first()
                ], 'statusCode' => HttpStatusCode::OK];
            } else {
                $subService = SubService::where('id', $data['sub_service_id'])->first();
                if ($subService) {
                    $providerService = ProviderService::create([
                        'provider_id' => $providerId,
                        'service_id' => $subService->service_id,
                        'sub_service_id' => $data['sub_service_id'],
                        'statusCode' => $data['status']
                    ]);
                    if ($user->provider_type != ProviderType::BUSINESS) {
                        $user->service_type = "MULTIPLE";
                        $user->save();
                        $this->_updateStatus($user, $data);
                    }
                    $this->_updateServiceType($user);
                    return ['result' => [
                        'error' => false,
                        'message' => 'Sub Service Created successfully',
                        'data' => $providerService
                    ], 'statusCode' => HttpStatusCode::CREATED];
                } else {
                    return [
                        'result' => [
                            'error' => true,
                            'message' => 'Sub Service not found'
                        ], 'statusCode' => HttpStatusCode::NOT_FOUND
                    ];
                }
            }
        }
    }

    private function _updateStatus($user, $data)
    {
        $providerServices = ProviderService::where('provider_id', $user->id)->where('sub_service_id', $data['sub_service_id'])->first();
        if ($providerServices->status == 1) {
            ProviderService::where('provider_id', $user->id)->where('service_id', '!=', $providerServices->service_id)->latest()->update(['status' => 0]);
        }
        return;
    }

    private function _updateServiceType($user)
    {
        $providerService = ProviderService::whereProvider_id($user->id)->whereStatus(true)->distinct('service_id')->pluck('service_id')->toArray();
        if (count($providerService) == 1 && $providerService[0] == 3) {
            $user->service_type = ServiceType::MOVING;
            $user->save();
        } elseif (count($providerService) > 1) {
            $user->service_type = ServiceType::MULTIPLE;
            $user->save();
        } else {
            $user->service_type = ServiceType::SERVICE;
            $user->save();
        }
    }
}
