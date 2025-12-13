<?php

namespace Shopen\Http\Resources\Product\List;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Shopen\Http\Resources\Brand\BaseBrandResource;
use Shopen\Http\Resources\Product\ProductPriceResource;
use Shopen\Http\Resources\Seo\SeoDetailResource;
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
            'price' => ProductPriceResource::make($this->isConfigurable() ? $this->getPriceFrom() : $this->whenLoaded('price')),
            'url' => $this->url,
            'in_stock' => $this->isInStock(),
            'images' => $this->images,
            'free_shipping' => app(ShippingService::class)->isFreeShippingAvailable($this->resource),
            'is_on_list' => app(ShoppingListService::class)->isProductOnAnyList($this->id),
            'is_configurable' => $this->isConfigurable(),
            'is_new' => $this->isNew(),
        ];
        if (config('shopen.product.reviews.enabled')) {
            $data['rating'] = $this->rating;
            $data['reviews_count'] = $this->reviews_count;
        }
        foreach ($this->resource->getCustomAttributes() as $key => $value) {
            if (is_null($value)) {
                continue;
            }
            $data['attributes'][$key] = $value;
        }
        return $data;
    }
}
