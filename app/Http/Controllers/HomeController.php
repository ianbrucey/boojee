<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index()
    {
        $books = DB::table("books")->where("user_id", auth()->id())->orderBy("order_id")->get();
        return view("dashboard")->with("books", $books);
    }
}
