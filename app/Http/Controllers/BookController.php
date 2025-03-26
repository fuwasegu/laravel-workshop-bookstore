<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class BookController
{
    /**
     * @return Collection<int, Book>
     */
    public function index(): Collection
    {
        return Book::all();
    }

    public function show(Book $book): Book
    {
        return $book;
    }

    /**
     * @throws ValidationException
     */
    public function update(Book $book, Request $request): Book
    {
        // バリデーション
        $validator = Validator::make($request->all(), [
            'title' => ['required', 'string'],
            'isbn' => ['required', 'string'],
        ]);
        $validator->validate();

        // 埋めて更新
        $book->fill($validator->validated());
        $book->save();

        return $book;
    }
}
