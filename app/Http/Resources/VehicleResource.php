<?php

namespace App\Http\Resources;

use App\Utils\HttpStatusCode;
use Illuminate\Http\Resources\Json\JsonResource;

class VehicleResource extends JsonResource
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
                'vehicle_type' => $this->vehicle_type,
                'provider' => $this->provider,
                'name' => $this->name,
                'model' => $this->model,
                'number' => $this->number,
                'condition' => $this->condition,
                'company_name' => $this->company_name 
            ],
            'message' => HttpStatusCode::$statusTexts[HttpStatusCode::OK]
        ];
    }
}
