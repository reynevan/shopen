<?php

namespace Shopen\Repositories\Banner;

use Illuminate\Database\Eloquent\Builder;
use Shopen\Models\Banner\Banner;

class BannerRepository
{
    public function getPaginated($sortField, $sortDir, $searchQuery = null, $placement = null)
    {
        return Banner::query()
            ->with(['media'])
            ->when($searchQuery, function (Builder $query) use ($searchQuery) {
                $query->whereLike('title', '%' . $searchQuery . '%');
            })
            ->when($placement, function (Builder $query) use ($placement) {
                $query->where('placement_key', $placement);
            })
            ->orderBy($sortField, $sortDir)
            ->paginate()
            ->withQueryString();
    }
}