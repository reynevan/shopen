<?php

namespace Shopen\Http\Controllers\Frontend\Product;

use Inertia\Inertia;
use Inertia\Response;
use Shopen\Http\Resources\Attribute\AttributeOptionResource;
use Shopen\Models\Attribute\AttributeOption;
use Shopen\Models\Product\Attribute\Value\ProductAttributeInt;
use Shopen\Models\Product\Product;
use Shopen\Repositories\Product\ProductRepository;
use Shopen\Services\BannerService;

readonly class ProductShowController
{
    public function __construct
    (
        protected ProductRepository $productRepository,
        protected BannerService              $bannerService,
    )
    {}

    public function index(Product $product): Response
    {
        $this->productRepository->loadAttributes($product);

        return Inertia::render('Frontend/Product/Show', [
            'product' => $product,
            'images' => $product->getImagesUrls(),
            'variants' => $this->getVariants($product),
            'configurableAttributes' => $this->getConfigurableAttributes($product),
            'attributes' => $product->getCustomAttributes(),
            'banners' => fn() => $this->bannerService->getForProduct($product)
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