<?php
namespace App\Http\DTO\Requests;

use Illuminate\Http\Request;

class ListBooksInput extends ListInput
{
    public ?string $searchText;

    public function __construct(?int $perPage, ?string $sortField, ?string $sortDirection, ?string $searchText)
    {
        parent::__construct($perPage, $sortField, $sortDirection);
        $this->searchText = $searchText;
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
            $request->input('sort_field'),
            $request->input('sort_direction'),
            $request->input('search_text'),
        );
    }
}
