<?php

namespace Tests\Unit;

//use PHPUnit\Framework\TestCase;
use App\Book;
use App\Http\Services\BookService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
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
            "title" => "Title 1",
            "author" => "Author 1",
        ];

        $book = $this->bookService->createBook($data);
        $this->assertDatabaseHas('books', $data);
        $this->assertEquals($data['title'], $book->title);
        $this->assertEquals($data['author'], $book->author);
    }

    /**
     * Test if updateBook throws ModelNotFoundException when id is not found
     *
     * @return void
     */
    public function testUpdateBookNotFound() {
        $this->expectException(ModelNotFoundException::class);
        $this->bookService->updateBook("2", [
            "title" => "Title 2",
            "author" => "Author 2",
        ]);
    }

    /**
     * Test if updateBook is successful and the entity exist in db
     * with condition ONLY authors name can only be edited
     *
     * @return void
     */
    public function testUpdateBookSuccessfully() {
        $createdBook = $this->bookService->createBook([
            "title" => "Title 1",
            "author" => "Author 1",
        ]);

        $book = $this->bookService->updateBook($createdBook->id, [
            "title" => "Title Edit",
            "author" => "Author Edit",
        ]);

        $this->assertDatabaseHas('books', [
            "title" => "Title 1",
            "author" => "Author Edit",
        ]);
        $this->assertEquals("Title 1", $book->title);
        $this->assertEquals("Author Edit", $book->author);
    }
}
