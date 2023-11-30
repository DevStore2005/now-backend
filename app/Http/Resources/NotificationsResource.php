<?php

namespace App\Http\Resources;

use App\Utils\HttpStatusCode;
use Illuminate\Http\Resources\Json\ResourceCollection;

class NotificationsResource extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'error' => false,
            'data' => $this->collection,
            'message' => HttpStatusCode::$statusTexts[HttpStatusCode::OK],
        ];
    }
}
