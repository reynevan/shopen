<?php

namespace Shopen\Http\Resources\Product;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Shopen\Http\Resources\Brand\BaseBrandResource;
use Shopen\Http\Resources\Seo\SeoDetailResource;
use Shopen\Services\ShippingService;
use Shopen\Services\ShoppingListService;
use Shopen\Services\StoreManager;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $seo = $this->resource->getSeoForStore(app(StoreManager::class)->getCurrentStore()->id);
        $data = [
            'id' => $this->id,
            'sku' => $this->sku,
            'brand' => BaseBrandResource::make($this->whenLoaded('brand')),
            'price' => ProductPriceResource::make($this->isConfigurable() ? $this->getPriceFrom() : $this->whenLoaded('price')),
            'url' => $this->getUrl(),
            'in_stock' => $this->isInStock(),
            'images' => $this->images,
            'image' => $this->image,
            'free_shipping' => app(ShippingService::class)->isFreeShippingAvailable($this->resource),
            'is_on_list' => app(ShoppingListService::class)->isProductOnAnyList($this->id),
            'shopping_list_ids' => app(ShoppingListService::class)->getProductListIds($this->id),
            'is_configurable' => $this->isConfigurable(),
            'is_new' => $this->isNew(),
            'meta_title' => $seo->seo_title ?? $this->resource->getCustomAttribute('name'),
            'meta_description' => strip_tags($seo->seo_description ?? $this->resource->getCustomAttribute('short_description')),
            'json_ld_image' => $this->resource->getJsonLdImageUrl(),
            'og_image' => $this->resource->getOgImageUrl(),
            'canonical_url' => $this->resource->getCanonicalUrl(),
            'max_cart_qty' => $this->getMaxAddToCartQty()
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
