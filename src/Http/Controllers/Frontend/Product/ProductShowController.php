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
use Shopen\Models\UrlRewrite;
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
        protected ProductAttributeRepository    $productAttributeRepository
    )
    {
    }

    public function index(Product $product): Response
    {
        $this->recentlyViewedProductsService->add($product);

        $product->load(['price', 'relatedProducts.price', 'urlRewrite', 'brand']);

        $this->productRepository->loadAttributes($product);

        $product->image = $product->getThumbnailUrl();

        $user = Auth::user();

        return Inertia::render('Frontend/Product/Show', [
            'product' => fn() => ProductResource::make($product),
            'relatedProducts' => fn() => ProductResource::collection($this->productRepository->getRelatedProducts($product)),
            'reviews' => fn() => ProductReviewResource::collection(config('shopen.product.reviews.enabled') ? $this->productReviewRepository->getForProduct($product, request('opinie')) : []),
            'reviewsEnabled' => config('shopen.product.reviews.enabled'),
            'reviewSubmitted' => fn() => config('shopen.product.reviews.enabled') && $user && $product->reviews()->where('user_id', $user->id)->exists(),
            'images' => fn() => $product->getImagesUrls(),
            'variants' => fn() => $product->parent_id ? $this->getVariants($product) : ($product->isConfigurable() ? $this->getConfigurableAttributes($product) : []),
            'attributes' => fn() => AttributeResource::collection($this->productAttributeRepository->getVisibleInDetails()),
            'banners' => fn() => $this->bannerService->getForProduct($product),
            'sort' => fn() => request('opinie'),
            'recentlyViewedProducts' => fn() => ProductResource::collection($this->recentlyViewedProductsService->get(except: $product->id))
        ]);
    }


    protected function getVariants(Product $product)
    {
        return $this->productRepository->getProductVariants($product);
    }

    protected function getConfigurableAttributes(Product $product)
    {
        $attributes = $product->configurableAttributes;
        $data = [];
        $selectedAttributes = array_keys(request()->query());
        foreach ($attributes as $attribute) {

            $childProductIds = Product::query()
                ->active()
                ->where('parent_id', $product->id)
                ->when(count($selectedAttributes), function ($query) use ($attribute, $selectedAttributes) {
                    foreach (array_diff($selectedAttributes, [$attribute->code]) as $attrCode) {
                        $value = AttributeOption::getIdFromSlug(request($attrCode));
                        $query->filterByAttribute($attrCode, $value);
                    }
                })
                ->pluck('id')
                ->toArray();

            $usedOptions = ProductAttributeInt::query()
                ->where('attribute_id', $attribute->id)
                ->whereIn('entity_id', $childProductIds)
                ->get()
                ->pluck('value', 'entity_id')
                ->toArray();
            $options = [];
            foreach (array_unique($usedOptions) as $productId => $usedOption) {
                $option = AttributeOption::query()->where('id', $usedOption)->first();
                $optionProduct = Product::query()->active()->find($productId);
                if (!$optionProduct) {
                    continue;
                }
                if (array_count_values($usedOptions)[$usedOption] === 1) {
                    $url = $optionProduct->getUrl();
                } else {
                    $queryParams = request()->query();
                    $queryParams[$attribute->code] = $option->slug;
                    $url = $product->getUrl() . '?' . http_build_query($queryParams);
                }
                $optionData = [
                    'id' => $option->id,
                    'attribute_value' => $option->value,
                    'url' => $url,
                    'is_selected' => AttributeOption::getIdFromSlug(request($attribute->code)) === $option->id,
                ];
                if ($attribute->is_color) {
                    $optionData['color'] = $option->color;
                }
                $options[] = $optionData;
            }

            $data[] = [
                'attribute' => [
                    'id' => $attribute->id,
                    'name' => $attribute->name,
                    'code' => $attribute->code,
                    'is_color' => $attribute->is_color,
                ],
                'products' => $options
            ];
        }
        return $data;
    }


}