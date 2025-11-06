<?php

namespace Shopen\Http\Resources\Admin\Product;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Shopen\Http\Resources\Admin\Attribute\AttributeResource;
use Shopen\Http\Resources\Admin\Product\Price\ProductPriceResource;
use Shopen\Http\Resources\Admin\PromoCode\PromoCodeResource;
use Shopen\Http\Resources\MediaResource;

class ProductDuplicateResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => null,
            'is_virtual' => $this->is_virtual,
            'is_voucher' => $this->is_voucher,
            'is_new' => $this->is_new,
            'is_new_to' => $this->is_new_to?->format('d-m-Y'),
            'sku' => null,
            'type' => $this->type,
            'visible_individually' => $this->visible_individually,
            'ean' => $this->ean,
            'uses_stock' => $this->uses_stock,
            'stock_qty' => $this->stock_qty,
            'brand_id' => $this->brand_id,
            'images' => [],
            'price' => ProductPriceResource::make($this->whenLoaded('price')),
            'url_key' => $this->urlRewrite?->request_path,
            'attributes' => $this->resource->getCustomAttributes(),
            'configurable_attributes' => AttributeResource::collection($this->whenLoaded('configurableAttributes')),
            'variant_attributes' => $this->resource->getVariantAttributes(),
            'category_ids' => $this->categories->pluck('id')->toArray(),
            'is_configurable' => $this->resource->isConfigurable(),
            'related_products' => [],
            'related_products_ids' => [],
            'cross_sell_products' => [],
            'cross_sell_ids' => [],
            'up_sell_products' => [],
            'up_sell_ids' => [],
            'tax_class_id' => $this->tax_class_id
        ];
    }
}
