<?php

namespace App\Http\Resources;

use Illuminate\Support\Collection;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ChatResource extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return Collection
     */
    public function toArray($request)
    {
        return $this->collection;
    }
}
