<?php

namespace Shopen\Http\Controllers\Admin\Api;

use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Shopen\Http\Requests\Admin\Product\StoreProductRequest;
use Shopen\Http\Requests\Admin\Product\UpdateProductRequest;
use Shopen\Http\Resources\Admin\Product\BaseProductResource;
use Shopen\Http\Resources\Admin\Product\ProductResource;
use Shopen\Jobs\RecalculateProductPrice;
use Shopen\Models\Product\Product;
use Shopen\Repositories\Product\ProductRepository;

class ProductsController
{
    public function __construct(
        private ProductRepository $productRepository
    )
    {}

    public function index()
    {
        $products = $this->productRepository->getPaginated(
            request('sort', 'id'),
            request('dir', 'asc'),
            request('q'),
            ['name', 'is_active']);

        return BaseProductResource::collection($products);
    }

    public function storeVariant(StoreProductRequest $request): ProductResource
    {
        $request->validated();

        $data = request()->post();

        $this->validateVariant($data);

        DB::beginTransaction();
        try {

            $price = $data['price'];

            $product = new Product();

            foreach ($data['attributes'] as $key => $value) {
                $product->setCustomAttribute($key, $value);
            }
            $product->fill($data);

            $product->save();

            $product->createUrlRewrite($data['url_key'] ?? null);

            $product->categories()->sync($data['category_ids'] ?? []);

            $product->setPrice($price);

            RecalculateProductPrice::dispatch($product->id);

            DB::commit();
        } catch (Exception $e) {
            Log::error($e);
            DB::rollBack();
            throw ValidationException::withMessages([
                'product' => 'Wystąpił błąd przy zapisywaniu produktu'
            ]);
        }
        return ProductResource::make($product);
    }

    public function updateVariant(UpdateProductRequest $request, Product $product): ProductResource|RedirectResponse
    {
        $data = $request->validated();

        $this->validateVariant($data, $product);

        DB::beginTransaction();
        try {

            $price = $data['price'];

            foreach ($data['attributes'] as $key => $value) {
                $product->setCustomAttribute($key, $value);
            }
            $product->fill(Arr::only($data, ['sku', 'ean', 'brand_id']));
            $product->save();

            $product->createUrlRewrite($data['url_key'] ?? null);

            $product->setPrice($price);

            RecalculateProductPrice::dispatch($product->id);

            DB::commit();
        } catch (\Exception $e) {
            Log::error($e);
            DB::rollBack();
            throw ValidationException::withMessages([
                'product' => 'Wystąpił błąd przy zapisywaniu produktu'
            ]);
        }
        return ProductResource::make($product);
    }

    /**
     * @throws ValidationException
     */
    protected function validateVariant($data, ?Product $product = null): void
    {
        $variantExists = Product::query()->tap(function (Builder $query) use ($data, $product) {

            $parent = Product::query()->findOrFail($product?->parent_id ?? $data['parent_id']);
            $attributes = $parent->configurableAttributes;

            foreach ($attributes as $attribute) {
                $query->filterByAttribute($attribute->code, $data['attributes'][$attribute->code]);
            }
            if ($product) {
                $query->where('id', '<>', $product->id);
            }
        })->exists();
        if ($variantExists) {
            throw ValidationException::withMessages([
                'variant' => 'Konfiguracja już istnieje'
            ]);
        }
    }
}
