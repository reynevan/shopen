<?php

namespace Shopen\Http\Resources\Admin\Product;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Shopen\Http\Resources\Attribute\AttributeResource;
use Shopen\Http\Resources\MediaResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'sku' => $this->sku,
            'ean' => $this->ean,
            'images' => MediaResource::collection($this->resource->getMedia()),
            'media' => $this->resource->getImagesUrls(),
            'price' => ProductPriceResource::make($this->whenLoaded('price')),
            'url_key' => $this->urlRewrite?->request_path,
            'attributes' => $this->resource->getCustomAttributes(),
            'variant_attributes' => $this->resource->getVariantAttributes(),
            'category_ids' => $this->categories->pluck('id')->toArray(),
            'is_configurable' => $this->resource->isConfigurable(),
        ];
    }
}
