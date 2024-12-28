<?php

namespace App\Exports;

use Illuminate\Support\Collection;

class BooksExport
{
    private Collection $data;
    private array $fields;

    public function __construct(Collection $data, array $fields)
    {
        $this->data = $data;
        $this->fields = $fields;
    }

    public static function makeCsv(Collection $data, array $fields): string
    {
        $exporter = new static($data, $fields);
        return $exporter->toCsv();
    }

    public function toCsv(): string
    {
        // Initialize output buffer
        $output = fopen('php://temp', 'r+');
        if ($output === false) {
            throw new \RuntimeException('Unable to create temporary memory stream.');
        }

        // Write the headers to the CSV
        fputcsv($output, $this->fields);

        // Write each row of data
        foreach ($this->data as $row) {
            $rowArray = [];
            foreach ($this->fields as $field) {
                $rowArray[] = (string)$row->$field; // Dynamically add selected columns
            }
            fputcsv($output, $rowArray);
        }

        // Move back to the beginning of the buffer
        rewind($output);

        // Get the CSV as a string
        $csvString = stream_get_contents($output);
        if ($csvString === false) {
            throw new \RuntimeException('Unable to read from memory stream.');
        }

        // Close the buffer
        fclose($output);

        return $csvString;
    }
}
