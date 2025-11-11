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
use Shopen\Http\Resources\Admin\TaxClass\TaxClassResource;
use Shopen\Http\Resources\Attribute\AttributeResource;
use Shopen\Jobs\RecalculateProductPrice;
use Shopen\Models\Product\Product;
use Shopen\Repositories\Brand\BrandRepository;
use Shopen\Repositories\Category\CategoryRepository;
use Shopen\Repositories\Product\ProductAttributeRepository;
use Shopen\Repositories\TaxClass\TaxClassRepository;
use Shopen\Services\CustomAttributesService;

readonly class ProductEditController
{
    public function __construct(
        protected CustomAttributesService    $customAttributesService,
        protected ProductAttributeRepository $productAttributeRepository,
        protected CategoryRepository         $categoryRepository,
        protected BrandRepository           $brandRepository,
        protected TaxClassRepository $taxClassRepository,
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
            'categories' => fn() => $this->categoryRepository->getArray(),
            'brands' => fn() => $this->brandRepository->getAll()->select(['id', 'name'])->toArray(),
            'variants' => fn() => $this->getVariants($product),
            'tax_classes' => fn () => $this->taxClassRepository->getAll()->select(['id', 'name'])->toArray(),
        ]);
    }

    public function update(Product $product): RedirectResponse
    {

        DB::beginTransaction();
        try {
            $data = request()->post();

            $images = request()->post('images');

            $price = $data['price'];

            foreach ($data['attributes'] as $key => $value) {
                $product->setCustomAttribute($key, $value);
            }

            $product->fill($data);
            $product->save();

            $product->createUrlRewrite($data['url_key'] ?? null);

            $product->categories()->sync($data['category_ids'] ?? []);

            if (!$product->isConfigurable()) {
                $product->setPrice($price);
            }

            $product->relatedProducts()->sync($data['related_products_ids'] ?? []);
            $product->crossSells()->sync($data['cross_sell_ids'] ?? []);
            $product->upSells()->sync($data['up_sell_ids'] ?? []);

            RecalculateProductPrice::dispatch($product->id);

            $this->updateImages($product, $images ?? []);

            foreach ($product->variants as $variant) {
                $variant->searchable();
            }

            $product->createOrUpdateSeoForWebsite(1, [
                'seo_title' => $data['seo_title'] ?? null,
                'seo_description' => $data['seo_description'] ?? null,
            ]);

            DB::commit();
        } catch (\Exception $e) {
            Log::error($e);
            DB::rollBack();
            return back()->with('error', 'Wystąpił błąd przy zapisywaniu produktu');
        }
        return back()->with('success', 'Produkt został zaktualizowany');
    }

    protected function getVariants(Product $product)
    {
        if (!$product->isConfigurable()) {
            return null;
        }
        $variants = $product->variants->load(['price']);
        $this->customAttributesService->loadAttributesToCollection($variants, ['name', ...$product->configurableAttributes()->pluck('code')->toArray()]);
        return ProductResource::collection($variants);
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