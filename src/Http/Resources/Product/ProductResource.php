<?php

namespace Shopen\Http\Resources\Product;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Shopen\Http\Resources\Brand\BaseBrandResource;
use Shopen\Services\ShippingService;
use Shopen\Services\ShoppingListService;

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
            'brand' => BaseBrandResource::make($this->whenLoaded('brand')),
            'price' => ProductPriceResource::make($this->isConfigurable() ? $this->getPriceFrom() : $this->whenLoaded('price')),
            'url' => $this->getUrl(),
            'in_stock' => $this->isInStock(),
            'rating' => $this->rating,
            'reviews_count' => $this->reviews_count,
            'images' => $this->images,
            'image' => $this->image,
            'free_shipping' => app(ShippingService::class)->isFreeShippingAvailable($this->resource),
            'is_on_list' => app(ShoppingListService::class)->isProductOnAnyList($this->id),
            'shopping_list_ids' => app(ShoppingListService::class)->getProductListIds($this->id),
            'is_configurable' => $this->isConfigurable(),
            'is_new' => $this->is_new && $this->is_new_to && $this->is_new_to->isFuture()
        ];
        foreach ($this->resource->getCustomAttributes() as $key => $value) {
            if (is_null($value)) {
                continue;
            }
            $data['attributes'][$key] = $value;
        }
        return $data;
    }
}
