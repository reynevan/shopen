<?php

namespace Shopen\Http\Controllers\Admin\Product;

use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;
use Shopen\Http\Requests\Admin\Product\StoreProductRequest;
use Shopen\Http\Resources\Admin\Product\ProductResource;
use Shopen\Http\Resources\Attribute\AttributeResource;
use Shopen\Jobs\RecalculateProductPrice;
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
        return Inertia::render('Admin/Product/Create', [
            'product' => ProductResource::make(new Product()),
            'attributes' => fn () => AttributeResource::collection($this->productAttributeRepository->getAll()),
            'categories' => fn () => $this->categoryRepository->getArray(),
        ]);
    }

    public function createConfiguration(Product $product): RedirectResponse|Response
    {
        if (!$product->isConfigurable()) {
            return redirect(route('admin.products.create'));
        }
        return Inertia::render('Admin/Product/Create', [
            'parent' => fn () => ProductResource::make($product),
            'product' => fn () => ProductResource::make(new Product()),
            'attributes' => fn () => AttributeResource::collection($this->productAttributeRepository->getAll()),
            'categories' => fn () => $this->categoryRepository->getArray(),
        ]);
    }

    public function store(StoreProductRequest $request): RedirectResponse
    {
        $request->validated();

        $data = request()->post();

        DB::beginTransaction();
        try {
            $images = request()->post('images');

            $price = $data['price'];

            $product = new Product();

            foreach ($data['attributes'] as $key => $value) {
                $product->setCustomAttribute($key, $value);
            }
            $product->fill(Arr::only($data, ['sku', 'ean', 'type', 'uses_stock', 'stock_qty', 'in_stock', 'visibility', 'parent_id', 'is_virtual', 'brand_id']));

            $product->save();

            $product->createUrlRewrite($data['url_key'] ?? null);

            $product->categories()->sync($data['category_ids'] ?? []);

            if (!$product->isConfigurable()) {
                $product->setPrice($price);
            }

            if ($product->isConfigurable()) {
                $product->configurableAttributes()->sync(Arr::pluck($data['configurable_attributes'] ?? [], 'id'));
            }
            $product->relatedProducts()->sync($data['related_products_ids'] ?? []);
            $product->crossSells()->sync($data['cross_sell_ids'] ?? []);
            $product->upSells()->sync($data['up_sell_ids'] ?? []);

            RecalculateProductPrice::dispatch($product->id);

            $this->updateImages($product, $images ?? []);

            foreach ($product->variants as $variant) {
                $variant->searchable();
            }

            DB::commit();
        } catch (Exception $e) {
            Log::error($e);
            DB::rollBack();
            throw ValidationException::withMessages([
                'product' => 'Wystąpił błąd przy zapisywaniu produktu'
            ]);
        }
        if ($product->parent) {
            return back()->with('success', 'Produkt został utworzony');
        }
        return redirect(route('admin.products.edit', $product))->with('success', 'Produkt został utworzony');
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