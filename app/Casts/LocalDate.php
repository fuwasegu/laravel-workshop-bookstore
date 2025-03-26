<?php

declare(strict_types=1);

namespace App\Casts;

use Brick\DateTime\LocalDate as BrickLocalDate;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;
use Override;

class LocalDate implements CastsAttributes
{
    #[Override]
    public function get(Model $model, string $key, mixed $value, array $attributes): ?BrickLocalDate
    {
        if (null === $value) {
            return null;
        }

        return BrickLocalDate::parse((string) $value);
    }

    #[Override]
    public function set(Model $model, string $key, mixed $value, array $attributes): ?string
    {
        if (null === $value) {
            return null;
        }

        if (!$value instanceof BrickLocalDate) {
            throw new InvalidArgumentException('The given value is not a Brick\DateTime\LocalDate instance.');
        }

        return (string) $value;
    }
}
