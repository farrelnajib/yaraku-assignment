<?php
namespace App\Http\DTO\Requests;

use Illuminate\Http\Request;

class ListInput
{
    public ?int $perPage;
    public string $sortField;
    public string $sortDirection = 'asc' | 'desc';

    public function __construct(?int $perPage, ?string $sortField, ?string $sortDirection)
    {
        $this->perPage = $perPage;
        $this->sortField = $sortField ?? 'id';
        $this->sortDirection = $sortDirection ?? 'asc';
    }

    /**
     * Parse request params to `ListInput` object
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
        );
    }
}
