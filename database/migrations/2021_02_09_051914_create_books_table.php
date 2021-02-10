<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBooksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->foreignId("user_id");
            $table->string("google_id");
            $table->string("self_link");
            $table->string("title");
            $table->string("authors")->nullable();
            $table->text("small_thumbnail")->nullable();
            $table->text("thumbnail")->nullable();
            $table->string("language")->nullable();
            $table->text("description")->nullable();
            $table->integer("page_count")->nullable();
            $table->string("order_id");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('books');
    }
}
