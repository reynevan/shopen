<?php

namespace Shopen\Http\Controllers\Frontend\Product;

use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;
use Shopen\Http\Resources\Attribute\AttributeOptionResource;
use Shopen\Http\Resources\Attribute\AttributeResource;
use Shopen\Http\Resources\Product\ProductResource;
use Shopen\Http\Resources\Product\Review\ProductReviewResource;
use Shopen\Models\Attribute\AttributeOption;
use Shopen\Models\Product\Attribute\Value\ProductAttributeInt;
use Shopen\Models\Product\Product;
use Shopen\Repositories\Product\ProductAttributeRepository;
use Shopen\Repositories\Product\ProductRepository;
use Shopen\Repositories\Product\Review\ProductReviewRepository;
use Shopen\Services\BannerService;
use Shopen\Services\RecentlyViewedProductsService;

readonly class ProductShowController
{
    public function __construct
    (
        protected ProductRepository             $productRepository,
        protected BannerService                 $bannerService,
        protected ProductReviewRepository       $productReviewRepository,
        protected RecentlyViewedProductsService $recentlyViewedProductsService,
        protected ProductAttributeRepository     $productAttributeRepository,
    )
    {
    }

    public function index(Product $product): Response
    {
        $this->recentlyViewedProductsService->add($product);

        $this->productRepository->loadAttributes($product);
        $product->load(['price']);
        $product->image = $product->getThumbnailUrl();

        $reviews = config('shopen.product.reviews.enabled') ? $this->productReviewRepository->getForProduct($product, request('opinie')) : [];

        return Inertia::render('Frontend/Product/Show', [
            'product' => fn () => ProductResource::make($product),
            'reviews' => fn () => ProductReviewResource::collection($reviews),
            'reviewsEnabled' => config('shopen.product.reviews.enabled'),
            'reviewSubmitted' => fn() => config('shopen.product.reviews.enabled') && Auth::check() && $product->reviews()->where('user_id', Auth::id())->exists(),
            'images' => fn () => $product->getImagesUrls(),
            'variants' => fn () => $this->getVariants($product),
            'configurableAttributes' => fn () => $this->getConfigurableAttributes($product),
            'attributes' => fn () => AttributeResource::collection($this->productAttributeRepository->getVisibleInDetails()),
            'banners' => fn() => $this->bannerService->getForProduct($product),
            'sort' => fn () => request('opinie'),
            'recentlyViewedProducts' => fn() => ProductResource::collection($this->recentlyViewedProductsService->get(except: $product->id))
        ]);
    }

    protected function getVariants(Product $product)
    {

        return $this->productRepository->getProductVariants($product);
    }

    protected function getConfigurableAttributes(Product $product)
    {
        $childProductIds = $product->variants->pluck('id')->toArray();
        $attributes = $product->configurableAttributes;
        $data = [];
        foreach ($attributes as $attribute) {
            $usedOptions = ProductAttributeInt::query()
                ->where('attribute_id', $attribute->id)
                ->whereIn('entity_id', $childProductIds)
                ->select('value');
            $options = AttributeOption::query()
                ->whereIn('id', $usedOptions)
                ->get();
            $data[] = [
                'attribute' => [
                    'id' => $attribute->id,
                    'name' => $attribute->name,
                    'code' => $attribute->code,
                ],
                'options' => AttributeOptionResource::collection($options)->toArray(request())
            ];
        }
        return $data;
    }


}