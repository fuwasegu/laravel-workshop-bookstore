<?php

declare(strict_types=1);

use App\Http\Controllers\BookController;
use App\Http\Controllers\BookStockController;

Route::get('/books', [BookController::class, 'index']);
Route::get('/books/{book}', [BookController::class, 'show'])
    ->whereNumber('book');
Route::put('/books/{book}', [BookController::class, 'update'])
    ->whereNumber('book');

Route::get('/books/{book}/stocks', [BookStockController::class, 'count'])
    ->whereNumber('book');
Route::patch('/books/{book}/stocks', [BookStockController::class, 'adjustStock'])
    ->whereNumber('book');
