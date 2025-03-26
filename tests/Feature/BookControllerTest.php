<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Book;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Testing\Fluent\AssertableJson;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class BookControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function 一覧を取得できる(): void
    {
        // ダミーデータを生成する
        Book::factory()->count(5)->create();

        $response = $this->get('/api/books');

        // 200 が返ってくること
        $response->assertStatus(200);

        // 5 件返ってくること
        $response->assertJsonCount(5);

        // 各要素が book の構造を持っていること
        $response->assertJson(
            fn (AssertableJson $json) => $json
                ->each(
                    fn (AssertableJson $json) => $json
                        ->whereType('id', 'integer')
                        ->whereType('title', 'string')
                        ->whereType('isbn', 'string')
                        ->whereType('created_at', 'string')
                        ->whereType('updated_at', 'string')
                        ->etc()
                )
        );
    }

    #[Test]
    public function 単一の本を取得できる(): void
    {
        $book = Book::factory()->create([
            'title' => 'テスト本',
            'isbn' => 'xxx-xxx-xxx-xxx',
        ]);

        $response = $this->get("/api/books/{$book->id}");

        $response->assertStatus(200);
        $response->assertJson(
            fn (AssertableJson $json) => $json
                ->where('id', $book->id)
                ->where('title', $book->title)
                ->where('isbn', $book->isbn)
                ->where('created_at', $book->created_at?->toJSON())
                ->where('updated_at', $book->updated_at?->toJSON())
                ->etc()
        );
    }

    #[Test]
    public function 存在しない本を取得しようとすると404が返る(): void
    {
        $response = $this->get('/api/books/1');

        $response->assertStatus(404);
    }

    #[Test]
    public function 本を更新できる(): void
    {
        $book = Book::factory()->create([
            'title' => '古いタイトル',
            'isbn' => 'old-isbn',
        ]);

        Carbon::setTestNow('2024-01-01T00:00:00');

        $response = $this->put("/api/books/{$book->id}", [
            'title' => '新しいタイトル',
            'isbn' => 'new-isbn',
        ]);

        $response->assertStatus(200);
        $response->assertJson(
            fn (AssertableJson $json) => $json
                ->where('id', $book->id)
                ->where('title', '新しいタイトル')
                ->where('isbn', 'new-isbn')
                ->where('updated_at', '2024-01-01T00:00:00.000000Z')
                ->etc()
        );

        $this->assertDatabaseHas('books', [
            'id' => $book->id,
            'title' => '新しいタイトル',
            'isbn' => 'new-isbn',
        ]);
    }

    #[Test]
    public function 存在しない本を更新しようとすると404が返る(): void
    {
        $response = $this->put('/api/books/1', [
            'title' => '新しいタイトル',
            'isbn' => 'new-isbn',
        ]);

        $response->assertStatus(404);
    }
}
