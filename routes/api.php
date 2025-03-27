<?php

declare(strict_types=1);

use App\Http\Controllers\Book\CreateControllerA;
use App\Http\Controllers\Book\CreateControllerB;
use App\Http\Controllers\Book\CreateControllerC;
use App\Http\Controllers\Book\CreateControllerD;
use App\Http\Controllers\Book\CreateControllerE;
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


// Controller 比較用
Route::post('/books/create-a', CreateControllerA::class);
Route::post('/books/create-b', CreateControllerB::class);
Route::post('/books/create-c', CreateControllerC::class);
Route::post('/books/create-d', CreateControllerD::class);
Route::post('/books/create-e', CreateControllerE::class);
