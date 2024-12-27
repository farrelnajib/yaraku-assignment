<?php

namespace App\Exports;

use App\Book;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class BooksExport implements FromCollection, WithHeadings
{
    private Collection $data;
    private array $fields;

    public function __construct(Collection $data, array $fields)
    {
        $this->data = $data;
        $this->fields = $fields;
    }

    /**
    * @return Collection
    */
    public function collection(): Collection
    {
        return $this->data;
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return $this->fields;
    }
}
