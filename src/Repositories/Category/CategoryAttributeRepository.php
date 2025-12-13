<?php

namespace Shopen\Repositories\Category;

use Illuminate\Support\Facades\Cache;
use Shopen\Models\Category\Attribute\CategoryAttribute;
use Shopen\Models\Category\Category;
use Shopen\Repositories\Attribute\AttributeRepository;

class CategoryAttributeRepository extends AttributeRepository
{
    const ATTRIBUTE_MODEL = CategoryAttribute::class;

    public function getAttributeValue(Category $category, $code)
    {
        return Cache::rememberForever("category.{$category->id}.attribute.$code", function () use ($code, $category) {
           return $category->getCustomAttribute($code);
        });
    }
}