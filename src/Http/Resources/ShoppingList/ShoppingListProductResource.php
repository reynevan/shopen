<?php

namespace Shopen\Http\Resources\ShoppingList;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Shopen\Http\Resources\Product\ProductPriceResource;
use Shopen\Http\Resources\ShoppingList\ProductResource;

class ShoppingListProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'sku' => $this->sku,
            'name' => $this->name,
            'url' => $this->getUrl(),
            'price' => ProductPriceResource::make($this->price),
            'image' => $this->getThumbnailUrl(),
            'in_stock' => $this->isInStock(),
            'is_configurable' => $this->isConfigurable(),
        ];
    }

}
