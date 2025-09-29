<?php

namespace Shopen\Http\Resources\Admin\Product;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Shopen\Http\Resources\Admin\Attribute\AttributeResource;
use Shopen\Http\Resources\Admin\Product\Price\ProductPriceResource;
use Shopen\Http\Resources\MediaResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'sku' => $this->sku,
            'type' => $this->type,
            'ean' => $this->ean,
            'brand_id' => $this->brand_id,
            'images' => MediaResource::collection($this->resource->getMedia()),
            'price' => ProductPriceResource::make($this->whenLoaded('price')),
            'url_key' => $this->urlRewrite?->request_path,
            'attributes' => $this->resource->getCustomAttributes(),
            'configurable_attributes' => AttributeResource::collection($this->whenLoaded('configurableAttributes')),
            'variant_attributes' => $this->resource->getVariantAttributes(),
            'category_ids' => $this->categories->pluck('id')->toArray(),
            'is_configurable' => $this->resource->isConfigurable(),
            'related_products' => BaseProductResource::collection($this->whenLoaded('relatedProducts')),
            'related_products_ids' => $this->whenLoaded('relatedProducts', $this->relatedProducts()->pluck('related_product_id')->toArray()),
            'cross_sell_products' => BaseProductResource::collection($this->whenLoaded('crossSells')),
            'cross_sell_ids' => $this->whenLoaded('crossSells', $this->crossSells()->pluck('cross_sell_product_id')->toArray()),
            'up_sell_products' => BaseProductResource::collection($this->whenLoaded('upSells')),
            'up_sell_ids' => $this->whenLoaded('upSells', $this->upSells()->pluck('up_sell_product_id')->toArray()),
        ];
    }
}
