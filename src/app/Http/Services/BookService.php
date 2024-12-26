<?php

namespace App\Http\Services;

use App\Http\DTO\Book as BookDTO;
use App\Book;

class BookService
{
    /**
     * Business logic to create book in the system
     *
     * @param array $book Validated book in form of associative array
     * @return BookDTO The created book instance
     */
    public function createBook(array $book): BookDTO {
        $bookDB = Book::create($book);
        return new BookDTO($bookDB->title, $bookDB->author);
    }
}
