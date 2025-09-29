<?php

namespace Shopen\Http\Controllers\Admin\Product;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
use Shopen\Http\Requests\Admin\Product\StoreProductRequest;
use Shopen\Http\Resources\Admin\Category\BaseCategoryResource;
use Shopen\Http\Resources\Admin\Category\CategoryResource;
use Shopen\Http\Resources\Admin\Product\ProductResource;
use Shopen\Http\Resources\Attribute\AttributeResource;
use Shopen\Jobs\RecalculateProductPrice;
use Shopen\Models\Product\Price\ProductPrice;
use Shopen\Models\Product\Product;
use Shopen\Repositories\Category\CategoryRepository;
use Shopen\Repositories\Product\ProductAttributeRepository;
use Shopen\Services\CustomAttributesService;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

readonly class ProductCreateController
{
    public function __construct(
        protected CustomAttributesService $customAttributesService,
        protected ProductAttributeRepository $productAttributeRepository,
        protected CategoryRepository $categoryRepository,
    )
    {}

    public function create(): Response
    {
        $product = new Product();
        return Inertia::render('Admin/Product/Create', [
            'product' => ProductResource::make($product),
            'attributes' => fn () => AttributeResource::collection($this->productAttributeRepository->getAll()),
            'categories' => fn () => BaseCategoryResource::collection($this->categoryRepository->getAll(0)),
        ]);
    }

    public function store(StoreProductRequest $request, Product $product): RedirectResponse
    {
        $request->validated();

        $validated = request()->validate([
            'cross_sell_ids'   => 'nullable|array',
            'cross_sell_ids.*' => 'exists:products,id',
            'up_sell_ids'      => 'nullable|array',
            'up_sell_ids.*'    => 'exists:products,id',
            'related_ids'      => 'nullable|array',
            'related_ids.*'    => 'exists:products,id',
        ]);

        DB::beginTransaction();
        try {
            $data = request()->post();

            $images = request()->post('images');

            $price = $data['price'];

            foreach ($data['attributes'] as $key => $value) {
                $product->setCustomAttribute($key, $value);
            }
            $product->fill(Arr::only($data, ['sku', 'ean', 'type', 'uses_stock', 'stock_qty', 'in_stock']));

            $product->save();

            $product->createUrlRewrite($data['url_key']);

            $product->categories()->sync($data['category_ids'] ?? []);

            $product->setPrice($price);

            if ($product->isConfigurable()) {
                $product->configurableAttributes()->sync(Arr::pluck($data['configurable_attributes'] ?? [], 'id'));
            }
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
        return back()->with('success', 'Produkt został utworzony');
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