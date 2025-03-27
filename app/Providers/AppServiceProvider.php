<?php

declare(strict_types=1);

namespace App\Providers;

use App\Service\Book\Contract\Fetcher as FetcherContract;
use App\Service\Book\OpenBD\Fetcher;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(FetcherContract::class, Fetcher::class);
    }

    public function boot(): void {}
}
