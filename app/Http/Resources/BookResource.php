<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BookResource extends JsonResource
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
            "google_id" => $request->id,
            "self_link" => $request->selfLink,
            "title" => $request->volumeInfo->title,
            "authors" => implode(",", $request->volumeInfo->authors),
            "small_thumbnail" => $request->volumeInfo->imageLinks->smallThumbnail,
            "thumbnail" => $request->volumeInfo->imageLinks->thumbnail,
            "language" => $request->volumeInfo->language,
            "description" => $request->volumeInfo->description,
            "page_count" => $request->volumeInfo->pageCount,
            "order_id" => date("Ymdhis")
        ];
    }
}
