<?php

namespace App\Http\Resources;

use App\Utils\HttpStatusCode;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceRequestResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'error' => false,
            'data' => [
                'id' => $this->id,
                'user' => $this->user,
                'requested_sub_service' => $this->requested_sub_service,
                'provider' => $this->provider ? $this->provider->load('provider_profile') : $this->provider,
                // 'competed_service_request' => $this->provider->serviceRequestCount(),
                'address' => $this->address,
                'status' => $this->status,
                'quotation_info' => $this->quotation_info,
                'hours' => $this->hours,
                'is_quotaion' => $this->is_quotaion,
                'direct_contact' => $this->direct_contact,
                'is_replied' => $this->is_replied,
                'is_completed' => $this->is_completed,
                'worked_times' => $this->worked_times,
                'user_feeback' => $this->user_feeback,
                'worked_hours' => $this->worked_hours,
                'working_status' => $this->working_status,
                'sub_service' => $this->sub_service,
                'payment_status' => $this->payment_status,
                'paid_amount' => $this->paid_amount,
                'payable' => $this->payable,
                'payable_amount' => $this->payable_amount,
                'type' => $this->type,
            ],
            'message' => HttpStatusCode::$statusTexts[HttpStatusCode::OK]
        ];
    }
}
