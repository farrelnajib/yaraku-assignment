<?php

namespace App\Http\DTO\Responses;

use Closure;
use Illuminate\Pagination\LengthAwarePaginator;

class Pagination
{
    public int $currentPage;
    public int $perPage;
    public int $total;
    public int $lastPage;
    public array $data;

    public function __construct(int $currentPage, int $perPage, int $total, int $lastPage, array $data)
    {
        $this->currentPage = $currentPage;
        $this->perPage = $perPage;
        $this->total = $total;
        $this->lastPage = $lastPage;
        $this->data = $data;
    }

    /**
     * Format paginated response with custom data transformation.
     *
     * @template TInput
     * @template TOutput
     * @param LengthAwarePaginator $pagination
     * @param Closure(TInput): TOutput $itemTransformFunc
     * @return Pagination
     */
    public static function fromModel(LengthAwarePaginator $pagination, Closure $itemTransformFunc): self
    {
        $transformedData = collect($pagination->items())->map(function ($item) use ($itemTransformFunc) {
            return $itemTransformFunc($item);
        })->toArray();
        return new self(
            $pagination->currentPage(),
            $pagination->perPage(),
            $pagination->total(),
            $pagination->lastPage(),
            $transformedData
        );
    }
}
