<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class PagesResource extends ResourceCollection
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
                    'name' => $page->name,
                    'title' => $page->title,
                    'content' => $page->content,
                    'image' => $page->image,
                    'type' => $page->type,
                    'og_title' => $page->og_title,
                    'og_image' => $page->og_image,
                    'og_description' => $page->og_description,
                ]
            ), 'message' => 'OK'
        ];
    }
}
