<?php

declare(strict_types=1);

namespace App;

use App\Http\DTO\Requests\ListBooksInput;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    public function scopeFilter(Builder $query, ListBooksInput $input): Builder {
        if (!empty($input->searchText)) {
            $bindings = "%{$input->searchText}%";
            $query->whereRaw("LOWER(title) LIKE ?", $bindings)
                ->orWhereRaw("LOWER(author) LIKE ?", $bindings);
        }

        $query->orderBy($input->sortField, $input->sortDirection);
        return $query;
    }
    protected $fillable = ["title", "author"];
}
