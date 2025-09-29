<?php

namespace Shopen\Http\Controllers\Admin\Category;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;
use Shopen\Http\Requests\Admin\Category\StoreCategoryRequest;
use Shopen\Http\Resources\Admin\Category\CategoryResource;
use Shopen\Http\Resources\Attribute\AttributeResource;
use Shopen\Models\Category\Category;
use Shopen\Repositories\Category\CategoryAttributeRepository;
use Shopen\Repositories\Category\CategoryRepository;
use Shopen\Services\CustomAttributesService;
use Throwable;

readonly class CategoryCreateController
{
    public function __construct(
        protected CategoryRepository $categoryRepository,
        protected CustomAttributesService $customAttributesService,
        protected CategoryAttributeRepository $categoryAttributeRepository,
    )
    {}

    public function create($parentCategoryId = null): Response
    {
        $category = new Category();
        $category->parent_id = $parentCategoryId;

        return Inertia::render('Admin/Category/Index', [
            'categories' => fn() => $this->categoryRepository->getArray($parentCategoryId),
            'category' => fn() => CategoryResource::make($category),
            'attributes' => fn() => AttributeResource::collection($this->categoryAttributeRepository->getAll())
        ]);
    }

    public function store(StoreCategoryRequest $request): RedirectResponse
    {
        DB::beginTransaction();
        try {
            $category = new Category();

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
            $category->setParentId($validated['parent_id'] ?? null);

            $category->save();
            $category->generateUrlRewrite();

            return redirect(route('admin.categories.edit', $category))->with('success', 'Kategoria została zapisana.');
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return back()->with('error', 'Wystąpił błąd przy zapisie kategorii.');
        }
    }
}