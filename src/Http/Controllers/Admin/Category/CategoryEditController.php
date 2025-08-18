<?php

namespace Shopen\Http\Controllers\Admin\Category;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
use Shopen\Http\Requests\Admin\Category\UpdateCategoryRequest;
use Shopen\Http\Resources\Admin\Category\CategoryResource;
use Shopen\Http\Resources\Attribute\AttributeResource;
use Shopen\Models\Category\Category;
use Shopen\Repositories\Category\CategoryAttributeRepository;
use Shopen\Repositories\Category\CategoryRepository;
use Shopen\Services\CustomAttributesService;

readonly class CategoryEditController
{
    public function __construct(
        protected CategoryRepository $categoryRepository,
        protected CustomAttributesService $customAttributesService,
        protected CategoryAttributeRepository $categoryAttributeRepository,
    )
    {}

    public function edit(Category $category): Response
    {
        $categories = $this->categoryRepository->getAll(0);
        $this->customAttributesService->preloadCategoryAttributes();
        $attributes = $this->categoryAttributeRepository->getAll();
        $category->seo = $category->getSeoForWebsite(1);

        return Inertia::render('Admin/Category/Index', [
            'categories' => CategoryResource::collection($categories),
            'category' => CategoryResource::make($category),
            'attributes' => AttributeResource::collection($attributes)
        ]);
    }

    public function update(UpdateCategoryRequest $request, Category $category): RedirectResponse
    {
        $validated = $request->validated();
        foreach ($validated['attributes'] as $key => $value) {
            if ($value) {
                $category->setCustomAttribute($key, $value);
            }
        }

        if ($request->hasFile('image_menu')) {
            $category->clearMediaCollection('menu-image');
            $category
                ->addMedia($request->file('image_menu'))
                ->toMediaCollection('menu-image');

        } elseif ($validated['remove_image_menu']) {
            $category->clearMediaCollection('menu-image');
        }

        unset($validated['attributes']);
        $category->fill($validated);
        $category->save();

        return back();
    }
}