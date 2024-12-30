<?php

namespace Tests\Feature\Book;

use App\Book;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateBookTest extends TestCase {
    protected $defaultHeaders = [
        "Accept" => "application/json",
    ];

    use RefreshDatabase;

    private function seedBook(): Book
    {
        return Book::create([
            "title" => "Book 1",
            "author" => "Author 1",
        ]);
    }

    /**
     * Test if PUT `/api/v1/books/{id}` returned 422 when title does not exist
     *
     * @return void
     */
    public function testValidateTitleRequiredOnUpdateBook()
    {
        $bookData = [
            "author" => "Author 1",
        ];

        $response = $this->put("/api/v1/books/1", $bookData, $this->defaultHeaders);
        $response->assertStatus(422)->assertJsonValidationErrors(["title"]);
    }

    /**
     * Test if PUT `/api/v1/books/{id}` returned 422 when author does not exist
     *
     * @return void
     */
    public function testValidateAuthorRequiredOnUpdateBook()
    {
        $bookData = [
            "title" => "Title 1",
        ];

        $response = $this->put("/api/v1/books/1", $bookData, $this->defaultHeaders);
        $response->assertStatus(422)->assertJsonValidationErrors(["author"]);
    }

    /**
     * Test if PUT `/api/v1/books/{id}` returned 422 when title and author does not exist
     *
     * @return void
     */
    public function testValidateAuthorAndTitleRequiredOnUpdateBook()
    {
        $bookData = [];

        $response = $this->put("/api/v1/books/1", $bookData, $this->defaultHeaders);
        $response->assertStatus(422)->assertJsonValidationErrors(["title", "author"]);
    }

    /**
     * Test if PUT `/api/v1/books/{id}` returned 404 when the provided id is not valid and does not exist in database
     *
     * @return void
     */
    public function testReturnNotFoundOnUpdateBook()
    {
        $updateData = [
            "title" => "Book Edit",
            "author" => "Author Edit",
        ];

        $response = $this->put("/api/v1/books/1", $updateData, $this->defaultHeaders);
        $response->assertStatus(404);
    }

    /**
     * Test if PUT `/api/v1/books/{id}` returned 200 with valid object returned
     *
     * @return void
     */
    public function testBookUpdatedSuccessfully()
    {
        $defaultData = $this->seedBook();
        $updateData = [
            "title" => "Book Edit",
            "author" => "Author Edit",
        ];

        $response = $this->put("/api/v1/books/1", $updateData, $this->defaultHeaders);
        $response->assertStatus(200)->assertJson(["data" => [
            "title" => $defaultData->title,
            "author" => "Author Edit",
        ]]);
    }
}
