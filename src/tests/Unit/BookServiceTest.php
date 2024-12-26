<?php

namespace Tests\Unit;

//use PHPUnit\Framework\TestCase;
use App\Book;
use App\Http\Services\BookService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookServiceTest extends TestCase
{
    use RefreshDatabase;

    protected BookService $bookService;
    public function setUp(): void
    {
        parent::setUp();
        $this->bookService = new BookService();
    }

    /**
     * Test if create book is successful and the entity exist in db
     *
     * @return void
     */
    public function testCreateBookSuccessfully()
    {
        $data = [
            "title" => "asd",
            "author" => "asd",
        ];

        $book = $this->bookService->createBook($data);
        $this->assertDatabaseHas('books', $data);
        $this->assertEquals($data['title'], $book->title);
        $this->assertEquals($data['author'], $book->author);
    }
}
