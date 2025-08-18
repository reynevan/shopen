<?php

namespace Shopen\Repositories\Category;

use Illuminate\Support\Collection;
use Shopen\Models\Category\Category;

class CategoryRepository
{
    public function getAll($maxChildrenLevel = false)
    {
        return Category::query()
            ->when($maxChildrenLevel !== false, function ($query) use ($maxChildrenLevel) {
                return $query
                    ->with(['children'])
                    ->where('level', '<=', $maxChildrenLevel);
            })
            ->orderBy('level')
            ->orderBy('sort_index')
            ->get();
    }

    public function getById($id)
    {
        return Category::query()->where('id', $id)->first();
    }
}