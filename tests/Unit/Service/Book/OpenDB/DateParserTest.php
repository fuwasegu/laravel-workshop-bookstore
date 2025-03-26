<?php

declare(strict_types=1);

namespace Tests\Unit\Service\Book\OpenDB;

use App\Service\Book\OpenDB\DateParser;
use Brick\DateTime\LocalDate;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class DateParserTest extends TestCase
{
    /**
     * @return array<string, array{
     *     0: string,
     *     1: ?LocalDate
     * }> テストケース名 => [入力文字列, 期待されるLocalDateオブジェクト or null]
     */
    public static function provide日付パースデータ(): array
    {
        return [
            // --- 正常系 (既存) ---
            '完全な日付' => ['1989-01-01', LocalDate::of(1989, 1, 1)],
            '年月のみ' => ['2011-01', LocalDate::of(2011, 1, 1)],
            '年のみ' => ['1975', LocalDate::of(1975, 1, 1)],
            '先頭c付き年月' => ['c1983-01', LocalDate::of(1983, 1, 1)],
            '角括弧付き年月' => ['[1974]-01', LocalDate::of(1974, 1, 1)],
            'カンマ区切り後半採用' => ['1974, c1973-01', LocalDate::of(1973, 1, 1)],
            '前後に空白ありの完全な日付' => [' 1999-12-31 ', LocalDate::of(1999, 12, 31)],
            '前後に空白ありの年月' => [' 2020-02 ', LocalDate::of(2020, 2, 1)],
            '前後に空白ありの年' => [' 2005 ', LocalDate::of(2005, 1, 1)],

            // --- 正常系 (YYYYMMDD 追加) ---
            'YYYYMMDD形式' => ['20220101', LocalDate::of(2022, 1, 1)],
            '空白付きYYYYMMDD形式' => [' 20230228 ', LocalDate::of(2023, 2, 28)],
            'YYYYMMDD形式うるう年' => ['20240229', LocalDate::of(2024, 2, 29)],

            // --- null を期待するケース (既存) ---
            '範囲指定カンマ区切り' => ['1982-1983, c1975-c1979', null],
            'ハイフンによる範囲指定' => ['1982-1983', null],
            '謎形式 cm' => ['25 cm-01', null],
            '空文字列' => ['', null],
            '空白のみ' => ['   ', null],
            '完全なゴミ文字列' => ['invalid-date', null],
            '存在しない日付_YYYY-MM-DD形式' => ['2023-02-29', null], // LocalDate::parse で失敗
            '存在しない日付_4月31日' => ['2024-04-31', null], // LocalDate::parse で失敗
            '存在しない日付_ありえない月' => ['2024-13-01', null], // LocalDate::parse で失敗
            'カンマ区切りだが後半が不正' => ['1974, c1973-13', null],
            'カンマ区切りだが後半が年のみ' => ['1974, c1973', null],
            '角括弧形式だが月が不正' => ['[1974]-13', null],
            'c形式だが月が不正' => ['c1983-13', null],
            'c形式だが年のみ' => ['c2000', null],
            '角括弧形式だが年のみ' => ['[1999]', null],

            // --- null を期待するケース (YYYYMMDD 追加) ---
            'YYYYMMDD形式だが不正な日付_うるう年でない' => ['20230229', null], // LocalDate::parse で失敗
            'YYYYMMDD形式だが不正な月' => ['20231301', null], // LocalDate::parse で失敗
            'YYYYMMDD形式だが不正な日' => ['20230431', null], // LocalDate::parse で失敗
            'YYYYMMDD形式だが桁数不足_7桁' => ['2023022', null], // 形式マッチせず null
            'YYYYMMDD形式だが桁数不足_6桁' => ['202302', null], // 形式マッチせず null
            'YYYYMMDD形式だが桁数超過' => ['202302281', null], // 形式マッチせず null
            'YYYYMMDD形式だが数字以外を含む' => ['2023022A', null], // 形式マッチせず null
        ];
    }

    /**
     * @param string     $inputString  入力される日付文字列
     * @param ?LocalDate $expectedDate 期待される LocalDate オブジェクト、またはパース不能なら null
     */
    #[DataProvider('provide日付パースデータ')]
    #[Test]
    public function 日付文字列をパースできること(string $inputString, ?LocalDate $expectedDate): void
    {
        $parser = new DateParser();

        // パースを実行
        $actualDate = $parser->parse($inputString);

        // 結果をアサート
        if (null === $expectedDate) {
            // 期待値が null の場合
            $this->assertNull($actualDate);
        } else {
            // 期待値が LocalDate オブジェクトの場合
            $this->assertInstanceOf(LocalDate::class, $actualDate);
            $this->assertEquals($expectedDate, $actualDate);
        }
    }
}
