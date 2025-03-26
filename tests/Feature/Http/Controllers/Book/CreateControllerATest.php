<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Book;

use App\Models\Book;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CreateControllerATest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function 書籍情報を取得して保存できる(): void
    {
        // OpenBD APIのモック
        Http::fake([
            'https://api.openbd.jp/v1/get*' => Http::response([
                [
                    'onix' => [
                        'DescriptiveDetail' => [
                            'TitleDetail' => [
                                'TitleElement' => [
                                    'TitleText' => [
                                        'content' => 'テスト書籍',
                                    ],
                                ],
                            ],
                            'Contributor' => [
                                [
                                    'PersonName' => [
                                        'content' => 'テスト著者1',
                                    ],
                                ],
                                [
                                    'PersonName' => [
                                        'content' => 'テスト著者2',
                                    ],
                                ],
                            ],
                        ],
                        'PublishingDetail' => [
                            'Imprint' => [
                                'ImprintName' => 'テスト出版社',
                            ],
                            'PublishingDate' => [
                                [
                                    'PublishingDateRole' => '01',
                                    'Date' => '20220101',
                                ],
                            ],
                        ],
                    ],
                    'summary' => [
                        'pubdate' => '2022-01-01',
                    ],
                ],
            ], 200),
        ]);

        $response = $this->postJson('/api/books/create-a', [
            'isbn' => '9784000000000',
        ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('books', [
            'isbn' => '9784000000000',
            'title' => 'テスト書籍',
            'publisher' => 'テスト出版社',
            'publish_date' => '2022-01-01',
        ]);

        $book = Book::query()->where('isbn', '9784000000000')->first();
        assert($book instanceof Book);
        $this->assertSame(['テスト著者1', 'テスト著者2'], $book->authors);
    }

    #[Test]
    public function 書籍情報が取得できない場合はエラーを返す(): void
    {
        // 空の配列を返すようにモック
        Http::fake([
            'https://api.openbd.jp/v1/get*' => Http::response([null], 200),
        ]);

        $response = $this->postJson('/api/books/create-a', [
            'isbn' => '9784000000000',
        ]);

        $response->assertStatus(400)
            ->assertJson([
                'message' => '書籍データを取得できませんでした',
                'isbn' => '9784000000000',
            ]);

        // 中途半端に登録されていないことを確認
        $this->assertDatabaseMissing('books', [
            'isbn' => '9784000000000',
        ]);
    }

    #[Test]
    public function isbnが未入力の場合はバリデーションエラーを返す(): void
    {
        $response = $this->postJson('/api/books/create-a', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['isbn']);

        $this->assertDatabaseCount('books', 0);
    }
}
