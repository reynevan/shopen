<?php

namespace Shopen\Http\Controllers\Admin\Category;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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
use Throwable;

readonly class CategoryEditController
{
    public function __construct(
        protected CategoryRepository $categoryRepository,
        protected CustomAttributesService $customAttributesService,
        protected CategoryAttributeRepository $categoryAttributeRepository,
    )
    {}

    public function edit($id)
    {
        $category = Category::find($id);
        if (!$category) {
            return redirect(route('admin.categories.index'));
        }
        $this->customAttributesService->preloadCategoryAttributes();
        $category->seo = $category->getSeoForStore(1);

        return Inertia::render('Admin/Category/Index', [
            'categories' => fn() => $this->categoryRepository->getArray($category->id),
            'category' => fn() => CategoryResource::make($category),
            'attributes' => fn() => AttributeResource::collection($this->categoryAttributeRepository->getAll())
        ]);
    }

    public function update(UpdateCategoryRequest $request, Category $category): RedirectResponse
    {
        DB::beginTransaction();
        try {
            $validated = $request->validated();
            foreach ($validated['attributes'] as $key => $value) {
                $category->setCustomAttribute($key, $value);
            }

            if ($request->hasFile('image_menu')) {
                $category->clearMediaCollection('menu-image');
                $category
                    ->addMedia($request->file('image_menu'))
                    ->preservingOriginal()
                    ->toMediaCollection('menu-image');

            } elseif ($validated['remove_image_menu']) {
                $category->clearMediaCollection('menu-image');
            }
            unset($validated['attributes']);
            $category->setParentId($validated['parent_id'] ?? null);
            $category->fill($validated);
            $category->save();
            $category->urlRewrites()->delete();
            $category->generateUrlRewrite();
            DB::commit();
            return back()->with('success', 'Kategoria została zapisana.');
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return back()->with('error', 'Wystąpił błąd przy zapisie kategorii.');
        }
    }

    public function move(Category $category): RedirectResponse
    {
        $dir = request('dir');
        if (!$dir) {
            return back();
        }
        $index = $category->sort_index;
        $nextCategory = Category::query()
            ->where('level', $category->level)
            ->when($dir === 'up', function (Builder $query) use ($index) {
                $query
                    ->where('sort_index', '<', $index)
                    ->orderBy('sort_index', 'desc');
            })
            ->when($dir !== 'up', function (Builder $query) use ($index) {
                $query
                    ->where('sort_index', '>', $index)
                    ->orderBy('sort_index');
            })
            ->first();
        if ($nextCategory) {
            $newIndex = $nextCategory->sort_index;
            $nextCategory->sort_index = $index;
            $nextCategory->save();

            $category->sort_index = $newIndex;
            $category->save();
        }
        return back();
    }

    public function destroy(Category $category): RedirectResponse
    {
        $category->delete();
        return back()->with('success', 'Kategoria została usunięta.');
    }
}