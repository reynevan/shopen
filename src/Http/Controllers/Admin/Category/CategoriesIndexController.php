<?php

namespace Shopen\Http\Controllers\Admin\Category;

use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Shopen\Http\Resources\Admin\Category\CategoryResource;
use Shopen\Http\Resources\Attribute\AttributeResource;
use Shopen\Repositories\Category\CategoryAttributeRepository;
use Shopen\Repositories\Category\CategoryRepository;
use Shopen\Services\CustomAttributesService;

readonly class CategoriesIndexController
{
    public function __construct(
        protected CategoryRepository $categoryRepository,
        protected CategoryAttributeRepository $categoryAttributeRepository,
    )
    {}

    public function index(): Response
    {
        return Inertia::render('Admin/Category/Index', [
            'categories' => fn() => $this->categoryRepository->getArray(),
            'attributes' => fn() => AttributeResource::collection($this->categoryAttributeRepository->getAll())
        ]);
    }
}