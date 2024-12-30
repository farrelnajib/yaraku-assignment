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
     * Test if GET `/api/v1/books/export` returns the same status code and content type as specified in the test suites
     *
     * @return void
     */
    public function testExportInvalidType(): void
    {
        $testSuites = [
            [
                // Default to CSV
                "body" => [],
                "status" => 200,
                "type" => "csv",
            ],
            [
                "body" => [
                    "type" => "nonExistentType"
                ],
                "status" => 400,
            ],
            [
                "body" => [
                    "type" => "csv"
                ],
                "status" => 200,
                "type" => "csv",
            ],
            [
                "body" => [
                    "type" => "xml"
                ],
                "status" => 200,
                "type" => "xml",
            ]
        ];

        $this->seedBooks(10);

        foreach ($testSuites as $testSuite) {
            $url = '/api/v1/books/export';
            $response = $this->postJson($url, $testSuite["body"], $this->defaultHeaders);
            $response->assertStatus($testSuite["status"]);

            if ($testSuite["status"] === 200) {
                $response->assertJson(["data" => [
                    "type" => $testSuite["type"],
                ]]);
            }
        }
    }
}
