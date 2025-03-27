<?php

declare(strict_types=1);

namespace App\Service\Book\Contract;

use App\Service\Book\BookDetail;
use App\Service\Book\BookServiceException;

interface Fetcher
{
    /**
     * 指定されたISBNから書籍の詳細情報を取得する.
     *
     * @param string $isbn 取得対象のISBN
     *
     * @return null|BookDetail 書籍情報が見つかった場合は BookDetail オブジェクト、見つからない場合は null
     *
     * @throws BookServiceException データ取得処理中にエラーが発生した場合
     */
    public function fetch(string $isbn): ?BookDetail;
}
