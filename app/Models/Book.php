<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        "user_id",
        "google_id",
        "self_link",
        "title",
        "authors",
        "small_thumbnail",
        "thumbnail",
        "language",
        "description",
        "page_count",
        "order_id",
    ];

    public function format_book_response_from_api($bookObject) {
        return [
            // user_id
            "google_id" => $bookObject->id.date("Ymdhis"),
            "self_link" => $bookObject->selfLink,
            "title" => $bookObject->volumeInfo->title,
            "authors" => implode(",", $bookObject->volumeInfo->authors),
            "small_thumbnail" => $bookObject->volumeInfo->imageLinks->smallThumbnail,
            "thumbnail" => $bookObject->volumeInfo->imageLinks->thumbnail,
            "language" => $bookObject->volumeInfo->language,
            "description" => $bookObject->volumeInfo->description,
            "page_count" => $bookObject->volumeInfo->pageCount
            // order_id
        ];
    }
}
