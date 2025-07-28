<?php

namespace Shopen\Http\Resources\Product;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Shopen\Http\Resources\MediaResource;
use Shopen\Http\Resources\Product\Review\ProductReviewResource;

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
            'in_stock' => $this->isInStock(),
            'rating' => $this->rating ?? 0,
            'reviews_count' => $this->reviews_count,
            'images' => $this->images
        ];
        foreach ($this->resource->getCustomAttributes() as $key => $value) {
            $data['attributes'][$key] = $value;
        }
        return $data;
    }
}
