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
use Shopen\Http\Controllers\Admin\Product\Trait\HasMedia;
use Shopen\Http\Requests\Admin\Product\StoreProductRequest;
use Shopen\Http\Resources\Admin\Product\ProductDuplicateResource;
use Shopen\Http\Resources\Admin\Product\ProductResource;
use Shopen\Http\Resources\Attribute\AttributeResource;
use Shopen\Jobs\RecalculateProductPrice;
use Shopen\Models\Product\Product;
use Shopen\Repositories\Category\CategoryRepository;
use Shopen\Repositories\Product\ProductAttributeRepository;
use Shopen\Repositories\TaxClass\TaxClassRepository;
use Shopen\Services\CeneoService;
use Shopen\Services\CustomAttributesService;
use Shopen\Services\VoucherService;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

readonly class ProductCreateController
{

    use HasMedia;

    public function __construct(
        protected CustomAttributesService $customAttributesService,
        protected ProductAttributeRepository $productAttributeRepository,
        protected CategoryRepository $categoryRepository,
        protected VoucherService $voucherService,
        protected TaxClassRepository $taxClassRepository,
        protected CeneoService $ceneoService
    )
    {}

    public function create(): Response
    {
        $product = new Product();
        if (config('shopen.ceneo.default_category_id')) {
            $product->ceneo_category_id = config('shopen.ceneo.default_category_id');
        }
        return Inertia::render('Admin/Product/Create', [
            'product' => ProductResource::make($product),
            'attributes' => fn () => AttributeResource::collection($this->productAttributeRepository->getAll()),
            'categories' => fn () => $this->categoryRepository->getArray(),
            'ceneo_categories' => fn () => $this->ceneoService->getCategories(),
            'tax_classes' => fn () => $this->taxClassRepository->getAll()->select(['id', 'name'])->toArray(),
            'tab' => request()->query('tab', 'general')
        ]);
    }

    public function duplicate(Product $product): Response
    {
        $this->customAttributesService->preloadCategoryAttributes(['name']);
        $this->customAttributesService->loadAllAttributes($product);

        $product->load(['price', 'configurableAttributes']);


        return Inertia::render('Admin/Product/Create', [
            'product' => ProductDuplicateResource::make($product),
            'attributes' => fn () => AttributeResource::collection($this->productAttributeRepository->getAll()),
            'categories' => fn () => $this->categoryRepository->getArray(),
            'ceneo_categories' => fn () => $this->ceneoService->getCategories(),
            'tax_classes' => fn () => $this->taxClassRepository->getAll()->select(['id', 'name'])->toArray(),
            'tab' => request()->query('tab', 'general')
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

            $product->fill($data);

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

            if ($product->is_voucher && !$product->isConfigurable()) {
                $this->voucherService->createPromoCodeForProduct($product, $data['price']['price']);
            }

            $product->createOrUpdateSeoForStore(1, [
                'seo_title' => $data['seo_title'] ?? null,
                'seo_description' => $data['seo_description'] ?? null,
            ]);

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


}