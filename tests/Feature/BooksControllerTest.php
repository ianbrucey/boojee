<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use phpDocumentor\Reflection\Types\Object_;
use Tests\TestCase;

class BooksControllerTest extends TestCase
{

    use RefreshDatabase;

    /**
     * Sample API response from google books
     * @return string
     */
    public function get_1_sample_api_response() {
        return json_decode(
            file_get_contents( sprintf("%s/1gBookResponse.json", base_path()) )
        );
    }

    /**
     * Sample API response from google books
     * @return string
     */
    public function get_many_sample_api_response() {
        return json_decode(
            file_get_contents( sprintf("%s/gBooksResponse.json", base_path()) )
        );
    }

    /**
     * Formatting our sample response
     * @return array
     */
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
     * Test that we can add books based on a sample api request via controller
     */
    public function test_can_add_a_book()
    {

        $user = User::factory()->create();
        $bookObject = $this->format_book_response_from_api($this->get_1_sample_api_response(), $user->id);

        $this->withoutMiddleware();
        $response = $this->json("POST",'/book', $bookObject);

        $response->assertStatus(201);


    }

    /**
     * Test that we can delete books based on a sample api request via controller
     */
    public function test_can_delete_a_book()
    {

        $user = User::factory()->create();
        $bookObject = $this->format_book_response_from_api($this->get_1_sample_api_response(), $user->id);

        $this->withoutMiddleware();
        $response = $this->json("POST",'/book', $bookObject);

        $response->assertStatus(201);

        $bookId = $response->json()['book_id'];
        $book = Book::find($bookId);
        $response = $this->json("DELETE",'/book/'.$book->id);
        $response->assertStatus(200);
        $book = Book::where("id", $bookId)->get();
        $this->assertTrue($book->count() === 0);


    }

    /**
     * Test that we can switch the order of 2 books via the controller
     */
    public function test_can_switch_book_order()
    {

        $user = User::factory()->create();

        $this->actingAs($user);

        $book1 =  Book::factory()->create([
            "user_id" => $user->id
        ]);

        $book1orderId = $book1->order_id;

        sleep(1); // so we get different timestamp on order_id, for the purpose of testing

        $book2 =  Book::factory()->create([
            "user_id" => $user->id
        ]);

        $book2orderId = $book2->order_id;

        $this->withoutMiddleware();
        $response = $this->json("PUT","/book/{$book1->id}", [
            "book_2" => $book2->id,
        ]);

        $newBook1OrderId = $response->json()['book1_order_id'];
        $newBook2OrderId = $response->json()['book2_order_id'];

        $this->assertTrue($book1orderId == $newBook2OrderId);
        $this->assertTrue($book2orderId == $newBook1OrderId);

        $response->assertStatus(200);

    }

}
