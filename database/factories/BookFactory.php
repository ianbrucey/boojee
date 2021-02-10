<?php

namespace Database\Factories;

use App\Models\Book;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Book::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            "user_id" => 1,
            "google_id" => $this->faker->shuffleString("asaf345sda453fsa"),
            "self_link" => $this->faker->imageUrl(),
            "title" => $this->faker->title,
            "authors" => $this->faker->name,
            "small_thumbnail" => $this->faker->imageUrl(),
            "thumbnail" => $this->faker->imageUrl(),
            "language" => "en",
            "description" => $this->faker->text(),
            "page_count" => $this->faker->numberBetween(100,200),
            "order_id" => date("Ymdhis"),
        ];
    }
}
