<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\BookController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    if(auth()->check()) {
        return redirect()->route("home");
    } else {
        return view('welcome');
    }
});

Route::get("home", [HomeController::class, "index"])->name("home")->middleware('auth');;




Route::resource("book", BookController::class)
    ->names([
        'index' => 'dashboard'
    ])->middleware('auth');;

require __DIR__.'/auth.php';
