<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PortfolioResource extends JsonResource
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
                'provider_id' => $this->provider_id,
                'image' => $this->image,
                'description' => $this->description,
                'created_at' => $this->created_at,
                'updated_at' => $this->updated_at,
            ],
            "message" => "Portfolio retrieved successfully.",
        ];
    }
}
