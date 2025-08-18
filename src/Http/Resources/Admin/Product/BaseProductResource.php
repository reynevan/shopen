<?php

namespace Shopen\Http\Resources\Admin\Product;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BaseProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'sku' => $this->sku,
            'price' => ProductPriceResource::make($this->whenLoaded('price')),
            'stock_qty' => $this->stock_qty,
            'url_key' => $this->urlRewrite?->request_path,
            'is_configurable' => $this->resource->isConfigurable(),
            'image' => $this->resource->getThumbnailUrl(),
            'attributes' => $this->resource->getCustomAttributes(),
        ];
    }
}
