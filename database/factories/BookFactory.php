<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Book;
use Brick\DateTime\LocalDate;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Book>
 */
class BookFactory extends Factory
{
    protected $model = Book::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->title(),
            'isbn' => $this->faker->isbn13(),
            'authors' => [$this->faker->name(), $this->faker->name()],
            'publisher' => $this->faker->company(),
            'publish_date' => LocalDate::parse($this->faker->date()),
        ];
    }
}
