<?php

namespace Shopen\Http\Controllers\Admin\Product;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
use Shopen\Http\Resources\Admin\Category\BaseCategoryResource;
use Shopen\Http\Resources\Admin\Product\ProductResource;
use Shopen\Http\Resources\Attribute\AttributeResource;
use Shopen\Jobs\RecalculateProductPrice;
use Shopen\Models\Product\Product;
use Shopen\Repositories\Brand\BrandRepository;
use Shopen\Repositories\Category\CategoryRepository;
use Shopen\Repositories\Product\ProductAttributeRepository;
use Shopen\Services\CustomAttributesService;

readonly class ProductEditController
{
    public function __construct(
        protected CustomAttributesService    $customAttributesService,
        protected ProductAttributeRepository $productAttributeRepository,
        protected CategoryRepository         $categoryRepository,
        protected BrandRepository           $brandRepository,
    )
    {
    }

    public function edit(Product $product): Response
    {
        $this->customAttributesService->preloadCategoryAttributes(['name']);
        $this->customAttributesService->loadAllAttributes($product);
        $product->load(['price', 'relatedProducts.price', 'crossSells.price', 'upSells.price', 'configurableAttributes']);

        if (!$product->relatedProducts->isEmpty()) {
            $this->customAttributesService->loadUsedInListAttributesToCollection($product->relatedProducts);
        }

        return Inertia::render('Admin/Product/Edit', [
            'product' => ProductResource::make($product),
            'attributes' => fn() => AttributeResource::collection($this->productAttributeRepository->getAll()),
            'categories' => fn() => BaseCategoryResource::collection($this->categoryRepository->getAll(0)),
            'brands' => fn() => $this->brandRepository->getAll()->select(['id', 'name'])->toArray(),
        ]);
    }

    public function update(Product $product): RedirectResponse
    {
        $validated = request()->validate([
            'cross_sell_ids' => 'nullable|array',
            'cross_sell_ids.*' => 'exists:products,id',
            'up_sell_ids' => 'nullable|array',
            'up_sell_ids.*' => 'exists:products,id',
            'related_ids' => 'nullable|array',
            'related_ids.*' => 'exists:products,id',
        ]);

        DB::beginTransaction();
        try {
            $data = request()->post();

            $images = request()->post('images');

            $price = $data['price'];

            foreach ($data['attributes'] as $key => $value) {
                $product->setCustomAttribute($key, $value);
            }
            $product->fill(Arr::only($data, ['sku', 'ean', 'brand_id']));
            $product->save();

            $product->createUrlRewrite($data['url_key']);

            $product->categories()->sync($data['category_ids'] ?? []);

            $product->setPrice($price);

            $product->relatedProducts()->sync($data['related_products_ids'] ?? []);
            $product->crossSells()->sync($data['cross_sell_ids'] ?? []);
            $product->upSells()->sync($data['up_sell_ids'] ?? []);

            RecalculateProductPrice::dispatch($product->id);

            $this->updateImages($product, $images);

            foreach ($product->variants as $variant) {
                $variant->searchable();
            }

            DB::commit();
        } catch (\Exception $e) {
            Log::error($e);
            DB::rollBack();
            return back()->with('error', 'Wystąpił błąd przy zapisywaniu produktu');
        }
        return back()->with('success', 'Produkt został zaktualizowany');
    }

    protected function updateImages(Product $product, $imagesData): void
    {
        $product->media()->whereNotIn('id', Arr::pluck($imagesData, 'id'))->delete();

        usort($imagesData, function ($a, $b) {
            return ($a['order'] ?? 0) < ($b['order'] ?? 0) ? -1 : 1;
        });
        $order = 1;
        foreach ($imagesData ?? [] as $image) {
            if ($image['new'] ?? false) {
                if (!isset($image['path'])) {
                    continue;
                }
                $product
                    ->addMedia(Storage::disk('public')->path($image['path']))
                    ->setOrder($order++)
                    ->withCustomProperties(['gallery' => $image['gallery'] ?? false, 'thumbnail' => $image['thumbnail'] ?? false])
                    ->toMediaCollection();
            } elseif (isset($image['id'])) {
                $media = $product->media()->find($image['id']);
                if (!$media) {
                    continue;
                }
                $media->setCustomProperty('gallery', $image['gallery'] ?? false);
                $media->setCustomProperty('thumbnail', $image['thumbnail'] ?? false);
                $media->order_column = $order++;
                $media->save();
            }
        }
    }

    public function destroy(Product $product): RedirectResponse
    {
        DB::beginTransaction();
        try {
            foreach ($product->getMedia() as $mediaItem) {
                Storage::disk('public')->deleteDirectory($mediaItem->id);
            }
            $product->delete();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
        return back()->with('success', 'Produkt został usunięty');
    }
}