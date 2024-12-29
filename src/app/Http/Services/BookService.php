<?php

namespace App\Http\Services;

use App\Book;
use App\ExportJob;
use App\Http\DTO\Book as BookDTO;
use App\Http\DTO\Export;
use App\Http\DTO\Requests\ListBooksInput;
use App\Http\DTO\Responses\Pagination;
use App\Jobs\ExportJobProcessor;
use Illuminate\Database\Eloquent\ModelNotFoundException;

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
     * Business logic to handle export. This function will create a job, dispatch a queue, and
     * `ExportJobProcessor` will handle the export process in the background
     *
     * @param string $type Type of import, can be "csv" or "xml"
     * @param array $fields Fields that want to be imported, can contains "title" or "author"
     * @return Export The job object
     */
    public function export(string $type = 'csv', array $fields = ["title", "author"]): Export
    {
        $exportJob = new ExportJob();
        $exportJob->status = "PENDING";
        $exportJob->type = $type;
        $exportJob->fields = $fields;
        $exportJob->save();

        dispatch(new ExportJobProcessor($exportJob));

        return Export::fromModel($exportJob);
    }

    /**
     * Get an export job by id
     *
     * @param string $id
     * @return Export
     */
    public function getExportJobById(string $id): Export {
        $exportDB = ExportJob::findOrFail($id);
        return Export::fromModel($exportDB);
    }
}
