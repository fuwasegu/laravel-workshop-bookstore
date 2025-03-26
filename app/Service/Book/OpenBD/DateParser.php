<?php

declare(strict_types=1);

namespace App\Service\Book\OpenBD;

use Brick\DateTime\DateTimeException;
use Brick\DateTime\LocalDate;
use Brick\DateTime\Parser\DateTimeParseException;

class DateParser
{
    public function parse(string $dateString): ?LocalDate
    {
        // YYYY-MM-DD 文字列への変換を試みる
        $ymd = $this->convertToYmdString(trim($dateString));

        // 変換に成功した場合のみパース
        if (null !== $ymd) {
            try {
                return LocalDate::parse($ymd);
            } catch (DateTimeException|DateTimeParseException) {
                return null;
            }
        }

        return null;
    }

    private function convertToYmdString(string $dateString): ?string
    {
        if (preg_match('/^\d{4},\s*c(\d{4}-\d{2})$/', $dateString, $matches)) {
            return $matches[1].'-01';
        }
        if (str_contains($dateString, ',') || preg_match('/\d{4}-\d{4}/', $dateString)) {
            return null; // 範囲などは除外
        }
        if (preg_match('/^\d+\s*cm-\d{2}$/i', $dateString)) {
            return null; // cm形式は除外
        }
        if (preg_match('/^\[(\d{4})]-(\d{2})$/', $dateString, $matches)) {
            return "{$matches[1]}-{$matches[2]}-01";
        }
        if (preg_match('/^c(\d{4}-\d{2})$/', $dateString, $matches)) {
            return $matches[1].'-01';
        }
        if (preg_match('/^(\d{4})(\d{2})(\d{2})$/', $dateString, $matches)) {
            return "{$matches[1]}-{$matches[2]}-{$matches[3]}";
        }
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateString)) {
            return $dateString;
        }
        if (preg_match('/^\d{4}-\d{2}$/', $dateString)) {
            return $dateString.'-01';
        }
        if (preg_match('/^\d{4}$/', $dateString)) {
            return $dateString.'-01-01';
        }

        return null; // どの形式にも一致しない
    }
}
