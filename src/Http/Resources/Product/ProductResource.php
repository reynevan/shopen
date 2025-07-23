<?php

namespace Shopen\Http\Resources\Product;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Shopen\Http\Resources\MediaResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = [
            'id' => $this->id,
            'sku' => $this->sku,
            'price' => ProductPriceResource::make($this->whenLoaded('price')),
            'url' => $this->getUrl(),
            'in_stock' => $this->isInStock()
        ];
        foreach ($this->resource->getCustomAttributes() as $key => $value) {
            $data[$key] = $value;
        }
        return $data;
    }
}
