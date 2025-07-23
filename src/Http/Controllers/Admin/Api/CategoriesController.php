<?php

namespace Shopen\Http\Controllers\Admin\Api;


use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Shopen\Http\Resources\Admin\Category\CategoryResource;
use Shopen\Models\Category\Category;
use Shopen\Repositories\Category\CategoryRepository;
use Shopen\Services\CustomAttributesService;

class CategoriesController
{
    public function __construct(
        private readonly CategoryRepository $categoryRepository,
        private readonly CustomAttributesService $customAttributesService
    )
    {}

    public function index(): AnonymousResourceCollection
    {
          $categories = $this->categoryRepository->getAll(0);

          $this->customAttributesService->preloadCategoryAttributes();

          return CategoryResource::collection($categories);
    }

    public function show(Category $category)
    {

    }

    public function update(Category $category)
    {
        $data = request()->post('category');
        foreach ($data['attributes'] as $key => $value) {
            if ($value) {
                $category->setCustomAttribute($key, $value);
            }
        }
        $category->save();
    }

    public function move()
    {
        $data = request()->post('categories');
        foreach ($data as $i => $categoryData) {
            $this->moveCategory($i, $categoryData, 0);

        }
    }

    protected function moveCategory($index, $data, $level, $parentId = null)
    {
        $category = $this->categoryRepository->getById($data['id']);
        $category->parent_id = $parentId;
        $category->sort_index = $index;
        $category->level = $level;
        $category->save();

        if (isset($data['children']) && count($data['children'])) {
            foreach ($data['children'] as $i => $child) {
                $this->moveCategory($i, $child, $level + 1, $data['id']);
            }
        }
    }

}
