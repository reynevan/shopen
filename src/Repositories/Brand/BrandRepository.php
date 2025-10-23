<?php

namespace Shopen\Repositories\Brand;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Shopen\Models\Brand\Brand;

class BrandRepository
{

    public function getActive()
    {
        return Brand::query()
            ->with(['media'])
            ->has('media')
            ->active()
            ->get();
    }

    public function getVisibleOnHomepage()
    {
        return Brand::query()
            ->with(['media'])
            ->has('media')
            ->active()
            ->visibleOnHomepage()
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

    public function getAll(): Collection
    {
        return Brand::query()->orderBy('name')->get();
    }

    public function getAllByIds($ids): Collection
    {
        return Brand::query()->whereIn('id', $ids)->orderBy('name')->get();
    }
}