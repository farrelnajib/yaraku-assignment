<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\DTO\Requests\ListBooksInput;
use App\Http\DTO\Responses\Response as ResponseDTO;
use App\Http\Requests\StoreBook;
use App\Http\Services\BookService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class BookController extends Controller
{
    protected BookService $bookService;
    public function __construct(BookService $bookService)
    {
        $this->bookService = $bookService;
    }

    /**
     * Validate and then create a new book in the system
     *
     * @param StoreBook $request
     * @return JsonResponse The created book instance.
     * @throws UnprocessableEntityHttpException If the object submitted doesn't pass validation
     */
    public function store(StoreBook $request): JsonResponse
    {
        $validated = $request->validated();
        $book = $this->bookService->createBook($validated);
        return response()->json(new ResponseDTO($book), 201);
    }

    /**
     * Validate and then create a new book in the system
     *
     * @param StoreBook $request
     * @param int $id ID of the book that is going to be updated
     * @return JsonResponse The updated book instance.
     * @throws ModelNotFoundException When the provided id is not valid
     */
    public function update(StoreBook $request, int $id): JsonResponse
    {
        try {
            $validated = $request->validated();
            $book = $this->bookService->updateBook($id, $validated);
            return response()->json(new ResponseDTO($book));
        } catch (ModelNotFoundException $exception) {
            return response()->json(['message' => 'Book not found'], 404);
        }
    }

    /**
     * List books in the system in pagination format
     *
     * @param StoreBook $request
     * @return JsonResponse The list of books instance in pagination format.
     */
    public function index(Request $request): JsonResponse
    {
        return response()->json($this->bookService->listBooks(ListBooksInput::fromRequest($request)));
    }

    /**
     * List books in the system in pagination format
     *
     * @param int $id ID of the book that is going to be deleted
     * @return JsonResponse
     */
    public function delete(int $id): JsonResponse
    {
        try {
            $this->bookService->deleteBook($id);
            return response()->json(null, 204);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Book not found'], 404);
        }
    }

    /**
     * Export books in the system in csv / xml format.
     * Returns 400 if `type` query is neither `csv` nor `xml`
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function export(Request $request): JsonResponse
    {
        $type = $request->json("type", 'csv');
        $fields = $request->json("fields", ["title", "author"]);
        if (!in_array($type, ['csv', 'xml'])) {
            return response()->json(['message' => 'Type must be CSV or XML'], 400);
        }

        if (sizeof($fields) === 0) {
            $fields = ['title', 'author'];
        }

        $export = $this->bookService->export($type, $fields);
        return response()->json(new ResponseDTO($export));
    }

    public function getExportJobById(int $id): JsonResponse
    {
        try {
            $export = $this->bookService->getExportJobById($id);
            return response()->json(new ResponseDTO($export), 200);
        } catch (ModelNotFoundException $exception) {
            return response()->json(['message' => 'Export job not found'], 404);
        }
    }
}
