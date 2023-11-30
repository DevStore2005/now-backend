<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class SliderResource extends ResourceCollection
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'error' => false,
            'data' => $this->collection->map(
                fn($page) => [
                    'id' => $page->id,
                    'description' => $page->description,
                    'bg_image' => $page->bg_image,
                    'front_image' => $page->front_image,
                ]
            ), 'message' => 'OK'
        ];
    }
}
