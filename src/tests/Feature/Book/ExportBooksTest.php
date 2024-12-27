<?php

namespace Tests\Feature\Book;

use App\Book;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Arr;
use Tests\TestCase;

class ExportBooksTest extends TestCase
{
    protected $defaultHeaders = [
        "Accept" => "application/json",
    ];

    use RefreshDatabase;

    private function seedBooks(int $size): void
    {
        $books = [];
        for ($i = 0; $i < $size; $i++) {
            $books[$i] = [
                "title" => "Book " . ($i + 1),
                "author" => "Author " . ($i + 1),
                "created_at" => now(),
                "updated_at" => now(),
            ];
        }

        Book::insert($books);
    }

    /**
     * Test if GET `/api/books/export` returns the same status code and content type as specified in the test suites
     *
     * @return void
     */
    public function testExportInvalidType(): void
    {
        $testSuites = [
            [
                "query" => [],
                "status" => 400,
                "contentType" => "application/json",
            ],
            [
                "query" => [
                    "type" => "nonExistentType"
                ],
                "status" => 400,
                "contentType" => "application/json",
            ],
            [
                "query" => [
                    "type" => "csv"
                ],
                "status" => 200,
                "contentType" => "text/csv; charset=UTF-8",
            ],
            [
                "query" => [
                    "type" => "xml"
                ],
                "status" => 200,
                "contentType" => "text/xml; charset=UTF-8",
            ]
        ];

        $this->seedBooks(10);

        foreach ($testSuites as $testSuite) {
            $url = '/api/books/export?' . Arr::query($testSuite["query"]);
            $response = $this->get($url, $this->defaultHeaders);
            $response->assertStatus($testSuite["status"]);
            $response->assertHeader("Content-Type", $testSuite["contentType"]);
        }
    }
}
