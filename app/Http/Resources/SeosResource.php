<?php

namespace App\Http\Resources;

use App\Http\Resources\CommentResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class SeosResource extends ResourceCollection
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
                fn($seo) => [
                    'id' => $seo->id,
                    'page_name' => $seo->page_name,
                    'og_title' => $seo->og_title,
                    'og_image' => $seo->og_image,
                    'og_description' => $seo->og_description,
                ]
            ), 'message' => 'OK'
        ];
    }
}
