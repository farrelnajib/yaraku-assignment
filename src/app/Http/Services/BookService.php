<?php

namespace App\Http\Services;

use App\Book;
use App\Exports\BooksExport;
use App\Http\DTO\Book as BookDTO;
use App\Http\DTO\Requests\ListBooksInput;
use App\Http\DTO\Responses\ExportResponse;
use App\Http\DTO\Responses\Pagination;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Spatie\ArrayToXml\ArrayToXml;

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
        return BookDTO::fromModel($bookDB);
    }

    /**
     * Business logic to create book in the system
     *
     * @param string $bookId ID of the book that is going to be updated
     * @param array $book Validated book in form of associative array
     * @return BookDTO The updated book instance
     * @throws ModelNotFoundException When the given ID does not exist in database
     */
    public function updateBook(string $bookId, array $book): BookDTO {
        $bookDB = Book::findOrFail($bookId);
        $bookDB->author = $book['author'];
        $bookDB->save();

        return BookDTO::fromModel($bookDB);
    }

    /**
     * Business logic to list books in pagination
     *
     * @param ListBooksInput $input
     * @return Pagination The result of books list in pagination
     */
    public function listBooks(ListBooksInput $input): Pagination
    {
        return Pagination::fromModel(Book::filter($input)->paginate($input->perPage), function (Book $book): BookDTO {
            return BookDTO::fromModel($book);
        });
    }

    /**
     * Business logic to delete book. Throws `ModelNotFoundException` if the given ID does not exist in database
     *
     * @param string $bookId ID of the book that is going to be deleted
     * @return void
     * @throws ModelNotFoundException
     */
    public function deleteBook(string $bookId): void {
        $bookDB = Book::findOrFail($bookId);
        $bookDB->delete();
    }

    /**
     * Business logic to export all books to csv with filter and sort (pagination ignored)
     *
     * @param ListBooksInput $input
     * @param array $fields
     * @return ExportResponse
     */
    public function exportToCsv(ListBooksInput $input, array $fields): ExportResponse
    {
        $books = Book::filter($input)->get($fields);
        $resData = BooksExport::makeCsv($books, $fields);
        return new ExportResponse($resData, "books-" . now() . ".csv", "text/csv");
    }

    /**
     * Business logic to export all books to xml with filter and sort (pagination ignored)
     *
     * @param ListBooksInput $input
     * @param array $fields
     * @return ExportResponse
     */
    public function exportToXml(ListBooksInput $input, array $fields): ExportResponse
    {
        $books = Book::filter($input)->get($fields);
        $arr = [
            "books" => $books->toArray()
        ];
        $resData = ArrayToXml::convert($arr, 'books');
        return new ExportResponse($resData, "books-" . now() . ".xml", "text/xml");
    }
}
