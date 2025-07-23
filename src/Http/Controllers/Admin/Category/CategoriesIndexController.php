<?php

namespace Shopen\Http\Controllers\Admin\Category;

use Illuminate\Http\RedirectResponse;
use Shopen\Repositories\Category\CategoryAttributeRepository;
use Shopen\Repositories\Category\CategoryRepository;
use Shopen\Services\CustomAttributesService;

readonly class CategoriesIndexController
{
    public function __construct(
        protected CategoryRepository $categoryRepository,
        protected CustomAttributesService $customAttributesService,
        protected CategoryAttributeRepository $categoryAttributeRepository,
    )
    {}

    public function index(): RedirectResponse
    {
        $category = $this->categoryRepository->getAll(0)->first();
        return redirect(route('admin.categories.edit', $category));
    }
}