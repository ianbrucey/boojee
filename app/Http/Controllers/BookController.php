<?php

namespace App\Http\Controllers;

use App\Http\Resources\BookResource;
use App\Models\Book;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookController extends Controller
{

    public function index(Request $request) {

        $query = $request->q ?? "";

        if(!$query) {
            return view("search");
        }

        $lang = $request->lang ?? "";
        $startIndex = $request->startIndex ?? 0;
        $base_url = env("GOOGLE_API_URL");
        $api_key = env("GOOGLE_API_KEY");



         $params = [
             'q' => $query,
             'maxResults'=> 30,
             'startIndex' => $startIndex,
             'key' => $api_key,
         ];

         $apiURL = sprintf("%s?%s", $base_url, http_build_query($params));

         $httpClient = new Client();

        try {
            $response = json_decode($httpClient->get($apiURL)->getBody());
        } catch (GuzzleException $e) {
            return response()->json( [
                'error' => "Could not make request",
                'results' => false
            ], 500);
        }

        $pages = ceil($response->totalItems / 30);

        /**
         * used '$pages, $currentPage, and $startIndex to create a light pagination
         */

        return view("search")->with([
            "books" => $response->totalItems ? $response->items : [],
            "query" => htmlspecialchars($query),
            "total" => $response->totalItems,
            "pages" => $pages,
            "currentPage" => ($startIndex / 30) + 1,
            "startIndex" => $startIndex
        ]);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // maximum of ten stores here
        $exists = DB::table("books")->where("google_id", $request->google_id)->first();
        if($exists) {
            return response()->json([
                "warning" => "You already have this book!",
            ], 400);
        }
        $req = $request->all();
        $req['user_id'] = $request->user_id ?? auth()->id();
        /**
         * I used the 'date()' function to create order_id's so that I could keep the order of books in succession,
         * but still have them easily be unique so that I could switch them for the drag and switch feature
         */
        $req['order_id'] = $request->order_id ?? date("Ymdhis");
        $book = Book::create($req);

        return response()->json([
            "success" => "Book titled, {$book->title}, added successfully",
            "book_id" => $book->id
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            $httpClient = new Client();
            $response = json_decode($httpClient->get(sprintf("%s/%s", env("GOOGLE_API_URL"), $id))->getBody());
        } catch (GuzzleException $e) {
            return response()->json( [
                'error' => "Could not make request",
                'results' => $e->getMessage()
            ], 500);
        }

        return view("book-details")->with("book", $response);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        /**
         * Here we are switching the book order_ids for the drag and switch feature
         */
        $book1 = Book::find($id);
        $book2 = Book::find($request->book_2);
        $book1OrderId = $book1->order_id;
        $book2OrderId = $book2->order_id;
        $book2->order_id = $book1OrderId;
        $book1->order_id = $book2OrderId;
        $book2->save();
        $book1->save();
        return response()->json([
            "book1_order_id" => $book1->order_id,
            "book2_order_id" => $book2->order_id,
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        Book::destroy($id);

        return response()->json(['success' => "resource deleted successfully."],200);
    }
}
