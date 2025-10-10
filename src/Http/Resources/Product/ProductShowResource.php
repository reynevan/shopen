<?php

namespace Shopen\Http\Resources\Product;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Shopen\Http\Resources\Brand\BrandResource;
use Shopen\Http\Resources\MediaResource;
use Shopen\Http\Resources\Product\Review\ProductReviewResource;
use Shopen\Services\ShippingService;
use Shopen\Services\ShoppingListService;

class ProductShowResource extends JsonResource
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
            'rating' => $this->rating,
            'reviews_count' => $this->reviews_count,
            'images' => $this->images,
            'image' => $this->image,
            'free_shipping' => app(ShippingService::class)->isFreeShippingAvailable($this->resource),
            'is_on_list' => app(ShoppingListService::class)->isProductOnAnyList($this->id),
            'shopping_list_ids' => app(ShoppingListService::class)->getProductListIds($this->id)
        ];
        return $data;
    }
}
