<?php

declare(strict_types=1);

namespace App\Models;

use App\Casts\LocalDate;
use Brick\DateTime\LocalDate as BrickLocalDate;
use Database\Factories\BookFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Collection;

/**
 * @property int                        $id
 * @property string                     $title
 * @property string                     $isbn
 * @property null|CarbonImmutable       $created_at
 * @property null|CarbonImmutable       $updated_at
 * @property Collection<int, BookStock> $stocks
 * @property null|string                $publisher
 * @property null|BrickLocalDate        $publish_date
 * @property array                      $authors
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Book newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Book newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Book query()
 * @method static \Database\Factories\BookFactory                    factory($count = null, $state = [])
 *
 * @mixin \Eloquent
 */
class Book extends Model
{
    /** @use HasFactory<BookFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'isbn',
        'publisher',
        'publish_date',
        'authors',
    ];

    protected $casts = [
        'id' => 'int',
        'title' => 'string',
        'isbn' => 'string',
        'created_at' => 'immutable_datetime',
        'updated_at' => 'immutable_datetime',
        'publisher' => 'string',
        'publish_date' => LocalDate::class,
        'authors' => 'json:unicode',
    ];

    /**
     * @return HasMany<BookStock, $this>
     */
    public function stocks(): HasMany
    {
        return $this->hasMany(BookStock::class);
    }
}
