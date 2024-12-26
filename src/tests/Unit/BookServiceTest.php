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

    /**
     * Test if listBooks without any params will return 15 items.
     * the default $perPage is 15
     *
     * @return void
     */
    public function testBooksWithDefaultPaginationListedSuccessfully() {
        $this->seedBooks();
        $paginationRes = $this->bookService->listBooks(null);
        $this->assertCount(15, $paginationRes->data);
    }

    /**
     * Test if listBooks with `per_page` parameter will return the exact number of data
     *
     * @return void
     */
    public function testBooksWithCustomPerPageListedSuccessfully() {
        $this->seedBooks();
        for ($i = 1; $i <= 10; $i++) {
            $perPage = rand(1, 100);
            $paginationRes = $this->bookService->listBooks($perPage);
            $this->assertCount($perPage, $paginationRes->data);
        }
    }
}
