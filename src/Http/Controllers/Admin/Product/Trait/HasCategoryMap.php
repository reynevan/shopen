<?php

namespace Shopen\Http\Controllers\Admin\Product\Trait;

use Shopen\Models\Category\Category;

trait HasCategoryMap
{
    protected array $categoryMap = [];

    private function createCategoryMap()
    {
        $categories = Category::query()->get();
        $names = $this->categoryAttributeRepository->getValues('name');
        foreach ($categories as $category) {
            $this->categoryMap[$names[$category->id]][$category->parent_id] = $category->id;
        }
    }

    private function getCategory($name, $parentId)
    {
        return $this->categoryMap[$name][$parentId] ?? null;
    }

}