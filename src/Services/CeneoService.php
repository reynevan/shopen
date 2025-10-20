<?php

namespace Shopen\Services;

use Shopen\Models\Ceneo\CeneoCategory;

class CeneoService
{
    public function getCategories(): array
    {
        $categories = CeneoCategory::query()->get();

        $categoriesByParent = [];
        foreach ($categories as $category) {
            $parentId = $category->parent_id ?? 0;
            if (!isset($categoriesByParent[$parentId])) {
                $categoriesByParent[$parentId] = [];
            }
            $categoriesByParent[$parentId][] = $category;
        }

        $buildTree = function($parentId = null) use (&$buildTree, $categoriesByParent) {
            $result = [];
            $children = $categoriesByParent[$parentId] ?? [];

            foreach ($children as $category) {
                $result[] = [
                    'id' => $category->id,
                    'name' => $category->name,
                    'children' => $buildTree($category->id)
                ];
            }

            return $result;
        };

        return $buildTree(0);
    }
}