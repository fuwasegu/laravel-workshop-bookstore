<?php

declare(strict_types=1);

namespace App\Http\Controllers\Book;

use App\Models\Book;
use App\Service\Book\OpenDB\DateParser;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

/**
 * Http Facade を使ったパターン.
 */
class CreateControllerA
{
    /**
     * @throws ValidationException
     */
    public function __invoke(Request $request): Book
    {
        // バリデーション
        $validator = Validator::make($request->all(), [
            'isbn' => ['required', 'string'],
        ]);
        $validator->validate();

        // HTTP リクエストを送信して JSON を得る
        $json = Http::get('https://api.openbd.jp/v1/get', [
            'isbn' => $request->string('isbn'),
        ])->json('0'); // index 0 番目

        // 書籍情報が取得できない場合
        if (null === $json) {
            throw new HttpResponseException(new JsonResponse([
                'message' => '書籍データを取得できませんでした',
                'isbn' => $request->string('isbn'),
            ], 400));
        }

        $descriptiveDetail = $json['onix']['DescriptiveDetail'] ?? [];
        $publishingDetail = $json['onix']['PublishingDetail'] ?? [];

        $authors = array_map(
            fn (array $contributor) => $contributor['PersonName']['content'] ?? '',
            $descriptiveDetail['Contributor'] ?? [],
        );

        $parser = new DateParser();

        // まずは PublishingDate から探す
        /** @var Collection<int, array{PublishingDateRole: "01"|"02"|"09"|"25", Date: string}> $pubDates */
        $pubDates = Collection::make($publishingDetail['PublishingDate'] ?? [])
            ->filter(fn (array $d) => $d['PublishingDateRole'] ?? '' === '01');

        $pubDate = $pubDates->isNotEmpty()
            ? $parser->parse($pubDates->firstOrFail()['Date'])
            : $parser->parse($json['summary']['pubdate'] ?? '');

        return Book::create([
            'isbn' => $request->string('isbn'),
            // title は必須なので仮タイトルを入れておいてあとから手動更新する想定
            'title' => $descriptiveDetail['TitleDetail']['TitleElement']['TitleText']['content'] ?? '仮タイトル',
            'publisher' => $publishingDetail['Imprint']['ImprintName'] ?? null,
            'publish_date' => $pubDate,
            'authors' => array_values(array_filter($authors)), // null や空文字列を削除して index 振り直す
        ]);
    }
}
