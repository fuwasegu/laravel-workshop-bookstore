<?php

declare(strict_types=1);

namespace App\Service\Book\Mock;

use App\Service\Book\BookDetail;
use App\Service\Book\Contract\Fetcher as FetcherContract;
use Brick\DateTime\LocalDate;

class Fetcher implements FetcherContract
{
    public function fetch(string $isbn): ?BookDetail
    {
        return new BookDetail(
            isbn: $isbn,
            title: 'サンプルタイトル',
            publisher: 'サンプル出版社',
            publishDate: LocalDate::parse('2025-03-27'),
            authors: ['山田太郎', '田中花子'],
        );
    }
}
