<?php

namespace Shopen\Repositories\Category;

use Illuminate\Database\Eloquent\Collection;
use Shopen\Models\Category\Category;

class CategoryRepository
{
    public function getAll($maxChildrenLevel = false): Collection
    {
        return Category::query()
            ->when($maxChildrenLevel !== false, function ($query) use ($maxChildrenLevel) {
                return $query
                    ->with(['children', 'children.children', 'children.children.children'])
                    ->where('level', '<=', $maxChildrenLevel);
            })
            ->orderBy('level')
            ->orderBy('sort_index')
            ->get();
    }

    public function getArray($selectedCategoryId = null): array
    {
        $data = [];
        $categories = Category::query()->orderBy('level')->orderBy('sort_index')->get();

        $categoryAttributeRepository = app(CategoryAttributeRepository::class);
        $names = $categoryAttributeRepository->getValues('name');
        $isActive = $categoryAttributeRepository->getValues('is_active');


        $categoryMap = [];
        foreach ($categories as $category) {
            $categoryMap[$category->id] = [
                'id' => $category->id,
                'is_active' => $isActive[$category->id] ?? false,
                'name' => $names[$category->id] ?? '',
                'is_selected' => $category->id === (int)$selectedCategoryId,
                'has_selected' => false,
                'children' => []
            ];
        }

        foreach ($categories as $category) {
            if ($category->parent_id === null) {
                $data[] = &$categoryMap[$category->id];
            } else {
                $categoryMap[$category->parent_id]['children'][] = &$categoryMap[$category->id];
            }
        }

        $this->setHasSelected($categoryMap, $selectedCategoryId);
        return $data;
    }

    private function setHasSelected(&$categoryMap, $selectedCategoryId): void
    {
        if ($selectedCategoryId === null) {
            return;
        }

        $hasSelectedChild = function($categoryId) use (&$categoryMap, &$hasSelectedChild, $selectedCategoryId) {
            $category = &$categoryMap[$categoryId];

            foreach ($category['children'] as &$child) {
                if ($child['is_selected'] || $hasSelectedChild($child['id'])) {
                    return true;
                }
            }
            return false;
        };

        foreach ($categoryMap as $categoryId => &$category) {
            $category['has_selected'] = $hasSelectedChild($categoryId);
        }
    }

    public function getAllByIds($ids): Collection
    {
        return Category::query()->whereIn('id', $ids)->get();
    }

    public function getById($id)
    {
        return Category::query()->where('id', $id)->first();
    }
}
