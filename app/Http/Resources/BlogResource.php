<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BlogResource extends JsonResource
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
                'author' => "Admin",
                'title' => $this->title,
                'slug' => $this->slug,
                'views' => $this->views,
                'featured_image' => $this->featured_image,
                'category' => $this->category,
                'contents' => $this->contents->map(fn ($content) => [
                    'id' => $content->id,
                    'content' => $content->content,
                    'image' => $content->getFirstMediaUrl('image'),
                ]),
                'commets' => $this->comments,
                'og_title' => $this->og_title,
                'og_image' => $this->og_image,
                'og_description' => $this->og_description,
                'created_at' => $this->created_at,
                'updated_at' => $this->updated_at,
            ],
            'message' => 'Blog retrieved successfully'
        ];
    }
}
