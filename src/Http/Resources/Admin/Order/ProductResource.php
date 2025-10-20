<?php

namespace Shopen\Http\Resources\Admin\Order;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Shopen\Http\Resources\Admin\PromoCode\PromoCodeResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'sku' => $this->sku,
            'url_key' => $this->urlRewrite?->request_path,
            'image' => $this->resource->getThumbnailUrl(),
            'variant_attributes' => $this->resource->getVariantAttributes(),
            'attributes' => $this->resource->getCustomAttributes(),
            'promo_code' => PromoCodeResource::make($this->whenLoaded('promoCode')),
        ];
    }
}