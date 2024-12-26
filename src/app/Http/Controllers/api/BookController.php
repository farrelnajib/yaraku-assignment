<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\DTO\Response;
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
        return response()->json(new Response($book), 201);
    }

    /**
     * Validate and then create a new book in the system
     *
     * @param StoreBook $request
     * @param int $id
     * @return JsonResponse The updated book instance.
     * @throws ModelNotFoundException When the provided id is not valid
     */
    public function update(StoreBook $request, int $id): JsonResponse
    {
        $validated = $request->validated();
        $book = $this->bookService->updateBook($id, $validated);
        return response()->json(new Response($book));
    }

    /**
     * List books in the system in pagination format
     *
     * @param StoreBook $request
     * @return JsonResponse The list of books instance in pagination format.
     */
    public function index(Request $request): JsonResponse
    {
        return response()->json($this->bookService->listBooks($request->input('per_page')));
    }
}
