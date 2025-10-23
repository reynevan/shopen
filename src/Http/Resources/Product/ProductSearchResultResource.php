<?php

namespace Shopen\Http\Resources\Product;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Shopen\Http\Resources\Brand\BrandResource;
use Shopen\Http\Resources\MediaResource;
use Shopen\Http\Resources\Product\Review\ProductReviewResource;
use Shopen\Services\ShippingService;
use Shopen\Services\ShoppingListService;

class ProductSearchResultResource extends JsonResource
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
            'brand' => BrandResource::make($this->whenLoaded('brand')),
            'price' => ProductPriceResource::make($this->whenLoaded('price')),
            'url' => $this->getUrl(),
            'in_stock' => $this->isInStock(),
            'images' => $this->images,
        ];
        if (config('shopen.product.reviews.enabled')) {
            $data['rating'] = $this->rating;
            $data['reviews_count'] = $this->reviews_count;
        }
        foreach ($this->resource->getCustomAttributes() as $key => $value) {
            $data['attributes'][$key] = $value;
        }
        return $data;
    }
}
