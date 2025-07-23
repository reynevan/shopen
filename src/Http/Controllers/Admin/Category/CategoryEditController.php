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
        if ($request->hasFile('image_desktop')) {
            if ($category->image_path_desktop) {
                Storage::disk('public')->delete($category->image_path_desktop);
            }
            $validated['image_path_desktop'] = $request->file('image_desktop')->store('categories', 'public');
        } elseif ($validated['remove_image_desktop']) {
            $category->image_path_desktop = null;
            if (Storage::exists('public/categories/' . $category->image_path_desktop)) {
                Storage::delete('public/categories/' . $category->image_path_desktop);
            }
        }

        if ($request->hasFile('image_mobile')) {
            if ($category->image_path_mobile) {
                Storage::disk('public')->delete($category->image_path_mobile);
            }
            $validated['image_path_mobile'] = $request->file('image_mobile')->store('categories', 'public');
        } elseif ($validated['remove_image_mobile']) {
            $category->image_path_mobile = null;
            if (Storage::exists('public/categories/' . $category->image_path_mobile)) {
                Storage::delete('public/categories/' . $category->image_path_mobile);
            }
        }

        unset($validated['attributes']);
        $category->fill($validated);
        $category->save();

        return back();
    }
}