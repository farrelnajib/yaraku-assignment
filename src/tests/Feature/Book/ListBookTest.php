<?php

namespace Tests\Feature\Book;

use App\Book;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ListBookTest extends TestCase
{
    protected $defaultHeaders = [
        "Accept" => "application/json",
    ];

    use RefreshDatabase;

    private function seedBooks(): void
    {
        $books = [];
        for ($i = 0; $i < 100; $i++) {
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
     * Test if GET `/api/books` without any params will return 15 items.
     * Default per_page is 15
     *
     * @return void
     */
    public function testDefaultPagination()
    {
        $this->seedBooks();
        $response = $this->get('/api/books', $this->defaultHeaders);
        $response->assertStatus(200);
        $response->assertJsonCount(15, 'data');
    }

    /**
     * Test if GET `/api/books` with `per_page` parameter will return the exact number of data
     *
     *
     * @return void
     */
    public function testPagination()
    {
        $this->seedBooks();
        for ($i = 1; $i <= 10; $i++) {
            $perPage = rand(1, 100);
            $response = $this->get('/api/books?per_page=' . $perPage, $this->defaultHeaders);
            $response->assertStatus(200);
            $response->assertJsonCount($perPage, 'data');
        }
    }
}
