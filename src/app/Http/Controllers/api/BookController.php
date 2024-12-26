<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\DTO\Response;
use App\Http\Requests\StoreBook;
use App\Http\Services\BookService;
use Illuminate\Http\JsonResponse;
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
}
