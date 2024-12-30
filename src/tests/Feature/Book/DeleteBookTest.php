<?php

namespace Tests\Feature\Book;

use App\Book;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeleteBookTest extends TestCase
{
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
     * Test if DELETE `/api/v1/books/{id}` returned the same status code specified in the test suites
     *
     * @return void
     */
    public function testValidateTitleRequiredOnUpdateBook()
    {
        $testSuites = [
            [
                "id" => 1,
                "code" => 204,
            ],
            [
                "id" => 1,
                "code" => 404,
            ]
        ];

        $this->seedBook();
        foreach ($testSuites as $testSuite) {
            $response = $this->delete("/api/v1/books/" . $testSuite["id"], [], $this->defaultHeaders);
            $response->assertStatus($testSuite["code"]);
        }
    }
}
