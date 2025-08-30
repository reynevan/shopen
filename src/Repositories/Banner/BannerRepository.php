<?php

namespace Shopen\Repositories\Banner;

use Illuminate\Database\Eloquent\Builder;
use Shopen\Models\Banner\Banner;

class BannerRepository
{
    public function getPaginated($sortField, $sortDir, $searchQuery = null)
    {
        return Banner::query()
            ->when($searchQuery, function (Builder $query) use ($searchQuery) {
                $query->whereLike('title', '%' . $searchQuery . '%');
            })
            ->orderBy($sortField, $sortDir)
            ->paginate()
            ->withQueryString();
    }
}