<?php

namespace Tests\Unit;

//use PHPUnit\Framework\TestCase;
use App\Book;
use App\ExportJob;
use App\Http\DTO\Requests\ListBooksInput;
use App\Services\BookService;
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

    private function seedBooks(int $count): void
    {
        $books = [];
        for ($i = 0; $i < $count; $i++) {
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
        $this->seedBooks(100);
        $paginationRes = $this->bookService->listBooks(new ListBooksInput(null, null, null, null));
        $this->assertCount(15, $paginationRes->data);
    }

    /**
     * Test if listBooks with `$perPage` parameter will return the exact number of data
     *
     * @return void
     */
    public function testBooksWithCustomPerPageListedSuccessfully() {
        $this->seedBooks(100);
        for ($i = 1; $i <= 10; $i++) {
            $perPage = rand(1, 100);
            $paginationRes = $this->bookService->listBooks(new ListBooksInput($perPage, null, null, null));
            $this->assertCount($perPage, $paginationRes->data);
        }
    }

    /**
     * Test if listBooks with `$searchText` parameter will return data where title OR author contains the searched text
     *
     * @return void
     */
    public function testBooksWithSearchListedSuccessfully() {
        $this->seedBooks(100);
        $testSuites = [
            [
                "searchText" => "book",
                "isFound" => true,
            ],
            [
                "searchText" => "author",
                "isFound" => true,
            ],
            [
                "searchText" => "random",
                "isFound" => false,
            ]
        ];

        foreach ($testSuites as $testSuite) {
            $searchText = $testSuite["searchText"];
            $paginationRes = $this->bookService->listBooks(new ListBooksInput(null, $searchText, null, null));
            foreach ($paginationRes->data as $book) {
                $condition = str_contains(strtolower($book->title), $searchText) || str_contains(strtolower($book->author), $searchText);
                $this->assertEquals($testSuite["isFound"], $condition);
            }
        }
    }

    /**
     * Test if deleteBook will return Exception specified in the test suites
     *
     * @return void
     */
    public function testDeleteBook()
    {
        $testSuites = [
            [
                "id" => 1,
                "exception" => null,
            ],
            [
                "id" => 1,
                "exception" => ModelNotFoundException::class,
            ]
        ];

        $this->seedBooks(1);
        foreach ($testSuites as $testSuite) {
            if ($testSuite["exception"] !== null) {
                $this->expectException($testSuite["exception"]);
            }
            $this->bookService->deleteBook($testSuite["id"]);
        }
    }

    /**
     * Test if `export` method returns `Export` DTO which contains the export job specs.
     *
     * @return void
     */
    public function testExportJobCreatedSuccessfully()
    {
        $testSuites = ['csv', 'xml'];
        $this->seedBooks(10);

        foreach ($testSuites as $testSuite) {
            $res = $this->bookService->export($testSuite);
            $this->assertEquals($testSuite, $res->type);
        }
    }

    /**
     * Test if `getExportJobById` method returns `Export` DTO,
     * and throws `ModelNotFoundException` if not found
     *
     * @return void
     */
    public function testGetExportJobById()
    {
        ExportJob::create([
            "id" => 1,
            "status" => "PENDING",
            "type" => "csv",
            "fields" => ["title", "author"],
        ]);

        $testSuites = [
            [
                "id" => 1,
                "exception" => null,
            ],
            [
                "id" => 2,
                "exception" => ModelNotFoundException::class,
            ]
        ];

        foreach ($testSuites as $testSuite) {
            if ($testSuite["exception"] !== null) {
                $this->expectException($testSuite["exception"]);
            }
            $res = $this->bookService->getExportJobById($testSuite["id"]);
            if ($testSuite["exception"] == null) {
                $this->assertNotNull($res);
            }
        }
    }
}
