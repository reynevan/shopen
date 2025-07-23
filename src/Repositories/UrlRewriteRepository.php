<?php

namespace Shopen\Repositories;

use Shopen\Models\UrlRewrite;

class UrlRewriteRepository
{
    public function getAllForCategories()
    {
        return UrlRewrite::query()->where('entity_type', 'category')->get();
    }
}