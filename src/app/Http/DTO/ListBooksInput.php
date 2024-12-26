<?php
namespace App\Http\DTO;

use Illuminate\Http\Request;

class ListBooksInput
{
    public ?int $perPage;
    public ?string $searchText;
    public string $sortField;
    public string $sortDirection = 'asc' | 'desc';

    public function __construct(?int $perPage, ?string $searchText, ?string $sortField, ?string $sortDirection)
    {
        $this->perPage = $perPage;
        $this->searchText = $searchText;
        $this->sortField = $sortField ?? 'id';
        $this->sortDirection = $sortDirection ?? 'asc';
    }

    /**
     * Parse request params to `ListBooksInput` object
     *
     * @param Request $request
     * @return self
     */

    public static function fromRequest(Request $request): self
    {
        return new self(
            $request->input('per_page'),
            $request->input('search_text'),
            $request->input('sort_field'),
            $request->input('sort_direction'),
        );
    }
}
