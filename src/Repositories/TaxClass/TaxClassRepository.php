<?php

namespace Shopen\Repositories\TaxClass;

use Illuminate\Database\Eloquent\Builder;
use Shopen\Models\Product\TaxClass;

class TaxClassRepository
{

    public function getAll()
    {
        return TaxClass::query();
    }

    public function getPaginated($sortField, $sortDir, $searchQuery = null)
    {
        return TaxClass::query()
            ->when($searchQuery, function (Builder $query) use ($searchQuery) {
                $query
                    ->whereLike('code', '%' . $searchQuery . '%')
                    ->orWhereLike('name', '%' . $searchQuery . '%')
                    ->orWhereLike('description', '%' . $searchQuery . '%');
            })
            ->orderBy($sortField, $sortDir)
            ->paginate()
            ->withQueryString();
    }
}