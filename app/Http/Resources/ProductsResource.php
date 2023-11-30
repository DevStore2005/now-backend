<?php

namespace App\Http\Resources;

use App\Utils\HttpStatusCode;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ProductsResource extends ResourceCollection
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
            'data' => $this->collection,
            'categories' => $this->categories,
            'message' => HttpStatusCode::$statusTexts[HttpStatusCode::OK]
        ];
    }
}
