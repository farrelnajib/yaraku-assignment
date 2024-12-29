<?php

namespace App\Jobs;

use App\Book;
use App\ExportJob;
use App\Exports\BooksExport;
use App\Http\DTO\Export;
use App\Http\DTO\Requests\ListBooksInput;
use App\Services\MqttService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use PhpMqtt\Client\Exceptions\DataTransferException;
use PhpMqtt\Client\Exceptions\RepositoryException;
use Spatie\ArrayToXml\ArrayToXml;

class ExportJobProcessor implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected ExportJob $exportJob;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(ExportJob $exportJob)
    {
        $this->exportJob = $exportJob;
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws RepositoryException|DataTransferException
     */
    public function handle(MqttService $mqttService)
    {
        $this->exportJob->update(['status' => 'PROCESSING']);

        sleep(1);

        $fields = $this->exportJob->fields;
        $type = $this->exportJob->type;
        $books = Book::all($fields);

        $resData = "";
        if ($type == 'csv') {
            $resData = BooksExport::makeCsv($books, $fields);
        } else {
            $booksArray = [
                "books" => $books->toArray(),
            ];
            $resData = ArrayToXml::convert($booksArray, 'books');
        }

        $now = now();
        $fileName = "exports/books-$now.$type";
        Storage::disk('local')->put($fileName, $resData);
        $this->exportJob->status = "FINISHED";
        $this->exportJob->download_url = $fileName;
        $this->exportJob->update();

        $id = $this->exportJob->id;
        $mqttService->publish("export/$id", Export::fromModel($this->exportJob));
    }

    /**
     * Export all books to csv with filter and sort (pagination ignored)
     *
     * @param ListBooksInput $input
     * @param array $fields
     * @return string
     */
    private function exportToCsv(ListBooksInput $input, array $fields): string
    {
        $books = Book::filter($input)->get($fields);
        return BooksExport::makeCsv($books, $fields);
    }

    /**
     * Business logic to export all books to xml with filter and sort (pagination ignored)
     *
     * @param ListBooksInput $input
     * @param array $fields
     * @return ExportResponse
     */
    private function exportToXml(ListBooksInput $input, array $fields): string
    {
        $books = Book::filter($input)->get($fields);
        $arr = [
            "books" => $books->toArray()
        ];
        return ArrayToXml::convert($arr, 'books');
    }
}
