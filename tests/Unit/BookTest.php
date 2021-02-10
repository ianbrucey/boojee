<?php

namespace Tests\Unit;

use App\Models\Book;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class BookTest extends TestCase
{

    public function get_1_sample_api_response() {
        return json_decode(
            file_get_contents( sprintf("%s/1gBookResponse.json", base_path()) )
        );
    }

    public function get_many_sample_api_response() {
        return json_decode(
            file_get_contents( sprintf("%s/gBooksResponse.json", base_path()) )
        );
    }

    public function format_book_response_from_api($bookObject, $userId) {
        return [
            "user_id" => $userId,
            "google_id" => $bookObject->id.date("Ymdhis"),
            "self_link" => $bookObject->selfLink,
            "title" => $bookObject->volumeInfo->title,
            "authors" => implode(",", $bookObject->volumeInfo->authors),
            "small_thumbnail" => $bookObject->volumeInfo->imageLinks->smallThumbnail,
            "thumbnail" => $bookObject->volumeInfo->imageLinks->thumbnail,
            "language" => $bookObject->volumeInfo->language,
            "description" => $bookObject->volumeInfo->description,
            "page_count" => $bookObject->volumeInfo->pageCount,
            "order_id" => date("Ymdhis")
        ];
    }
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_can_add_book() {

        /**
         * Test that we can add books based on a sample api request
         */
        $user = User::factory()->create();
        $bookObject = $this->format_book_response_from_api($this->get_1_sample_api_response(), $user->id);
        $book = new Book($bookObject);
        $book->save();
        $book->refresh();
        $this->assertTrue($book->user_id == $user->id);
    }

    /**
     * Test that we can delete books
     */
    public function test_can_delete_book() {
        $book =  Book::factory()->create();
        $id = $book->id;
        $book->delete();
        $bookCount = Book::where('id', $id)->count();
        $this->assertTrue($bookCount === 0);
    }

    /**
     * Test that we can switch the order of the books via the order_id
     */
    public function test_can_switch_book_order() {
        $book1 =  Book::factory()->create();
        sleep(1); // this was used so that the order_id's could be created at different times since it is a time based id
        $book2 =  Book::factory()->create();

        $book1OrderId = $book1->order_id;
        $book2OrderId = $book2->order_id;
        $book2->order_id = $book1OrderId;
        $book1->order_id = $book2OrderId;
        $book2->save();
        $book1->save();
        $book2->refresh();
        $book1->refresh();

        $this->assertTrue($book1OrderId == $book2->order_id);


    }
}
