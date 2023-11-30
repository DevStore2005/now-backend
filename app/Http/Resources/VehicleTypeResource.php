<?php

namespace App\Http\Resources;

use App\Utils\HttpStatusCode;
use Illuminate\Http\Resources\Json\JsonResource;

class VehicleTypeResource extends JsonResource
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
            'date' => [
                'id' => $this->id,
                'name' => $this->name,
                'type' => $this->type,
            ],
            'message' => HttpStatusCode::$statusTexts[HttpStatusCode::OK]
        ];
    }
}
