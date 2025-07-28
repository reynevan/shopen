<?php

namespace Shopen\Http\Controllers\Admin\Product;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
use Shopen\Http\Resources\Admin\Category\CategoryResource;
use Shopen\Http\Resources\Admin\Product\ProductResource;
use Shopen\Http\Resources\Attribute\AttributeResource;
use Shopen\Jobs\RecalculateProductPrice;
use Shopen\Models\Product\Product;
use Shopen\Repositories\Category\CategoryRepository;
use Shopen\Repositories\Product\ProductAttributeRepository;
use Shopen\Services\CustomAttributesService;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

readonly class ProductEditController
{
    public function __construct(
        protected CustomAttributesService $customAttributesService,
        protected ProductAttributeRepository $productAttributeRepository,
        protected CategoryRepository $categoryRepository,
    )
    {}

    public function edit(Product $product): Response
    {
        $this->customAttributesService->loadAllAttributes($product);
        $product->load('price');
        return Inertia::render('Admin/Product/Edit', [
            'product' => ProductResource::make($product),
            'attributes' => fn () => AttributeResource::collection($this->productAttributeRepository->getAll()),
            'categories' => fn () => CategoryResource::collection($this->categoryRepository->getAll(0)),
        ]);
    }

    public function update(Product $product): RedirectResponse
    {
        DB::beginTransaction();
        try {
            $data = request()->post('product');

            $images = request()->post('images');

            $price = $data['price'];

            foreach ($data['attributes'] as $key => $value) {
                $product->setCustomAttribute($key, $value);
            }
            $product->fill(Arr::except($data, ['images', 'price', 'attributes', 'url_key']));
            $product->save();

            $product->createUrlRewrite($data['url_key']);

            $product->categories()->sync($data['category_ids'] ?? []);

            $product->setPrice($price);

            RecalculateProductPrice::dispatch($product->id);

            $this->updateImages($product, $images);

            DB::commit();
        } catch (\Exception $e) {
            Log::error($e);
            DB::rollBack();
        }
        return back();
    }

    protected function updateImages(Product $product, $imagesData): void
    {
        $product->media()->whereNotIn('id', Arr::pluck($imagesData, 'id'))->delete();

        $images = [];
        usort($imagesData, function ($a, $b) {
            return ($a['order'] ?? 0) < ($b['order'] ?? 0) ? 1 : -1;
        });
        foreach ($imagesData ?? [] as $image) {
            if (isset($image['id'])) {
                $images[] = $image['id'];
                continue;
            }
            $media = $product
                ->addMedia(Storage::disk('public')->path($image['path']))
                ->toMediaCollection();
            $images[] = $media->id;

        }
        Media::setNewOrder($images);
    }
}