<?php

namespace Feature\Book;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateBookTest extends TestCase
{
    protected $defaultHeaders = [
        "Accept" => "application/json",
    ];

    use RefreshDatabase;

    /**
     * Test if POST /api/books returned 201 with valid object returned
     *
     * @return void
     */
    public function testBookCreatedSuccessfully()
    {
        $bookData = [
            "title" => "Book 1",
            "author" => "Author 1",
        ];

        $response = $this->post("/api/books", $bookData, $this->defaultHeaders);
        $response->assertStatus(201)->assertJson(["data" => $bookData]);
    }

    /**
     * Test if POST /api/books returned 422 when title does not exist
     *
     * @return void
     */
    public function testValidateTitleRequiredOnCreateBook()
    {
        $bookData = [
            "author" => "Author 1",
        ];

        $response = $this->post("/api/books", $bookData, $this->defaultHeaders);
        $response->assertStatus(422)->assertJsonValidationErrors(["title"]);
    }

    /**
     * Test if POST /api/books returned 422 when title does not exist
     *
     * @return void
     */
    public function testValidateAuthorRequiredOnCreateBook()
    {
        $bookData = [
            "title" => "Title 1",
        ];

        $response = $this->post("/api/books", $bookData, $this->defaultHeaders);
        $response->assertStatus(422)->assertJsonValidationErrors(["author"]);
    }
}
