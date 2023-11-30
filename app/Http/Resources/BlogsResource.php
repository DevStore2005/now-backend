<?php

namespace App\Http\Resources;

use App\Http\Resources\CommentResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class BlogsResource extends ResourceCollection
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
            'data' => $this->collection->map(
                fn ($blog) => [
                    'id' => $blog->id,
                    'author' => "Admin",
                    'title' => $blog->title,
                    'slug' => $blog->slug,
                    'views' => $blog->views,
                    'featured_image' => $blog->featured_image,
                    'category' => $blog->category,
                    'lastest_comment' => $blog->lastest_comment,
                    'contents' => $blog->contents->map(fn ($content) => [
                        'id' => $content->id,
                        'content' => $content->content,
                        'image' => $content->getFirstMediaUrl('image'),
                    ]),
                    'og_title' => $blog->og_title,
                    'og_image' => $blog->og_image,
                    'og_description' => $blog->og_description,
                    'created_at' => $blog->created_at,
                    'updated_at' => $blog->updated_at
                ]
            ), 'message' => 'Blogs retrieved successfully'
        ];
    }
}
