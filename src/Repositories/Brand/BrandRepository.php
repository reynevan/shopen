<?php

namespace Shopen\Repositories\Brand;

use Illuminate\Database\Eloquent\Builder;
use Shopen\Models\Brand\Brand;

class BrandRepository
{

    public function getActive()
    {
        return Brand::query()
            ->has('media')
            ->active()
            ->get();
    }

    public function getPaginated($sortField, $sortDir, $searchQuery = null)
    {
        return Brand::query()
            ->with(['media'])
            ->when($searchQuery, function (Builder $query) use ($searchQuery) {
                $query
                    ->whereLike('name', '%' . $searchQuery . '%');
            })
            ->orderBy($sortField, $sortDir)
            ->paginate(25);
    }

    public function getAll()
    {
        return Brand::query()->orderBy('name')->get();
    }
}