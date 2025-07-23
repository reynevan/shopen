<?php

namespace Shopen\Http\Resources\Order;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Number;
use Shopen\Http\Resources\Product\ProductResource;

class OrderItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $product = $this->resource->product;
        return [
            'id' => $this->id,
            'product' => [
                'id' => $product->id,
                'name' => $product->name,
                'url' => $product->getUrl(),
                'image' => $product->getThumbnailUrl(),
            ],
            'sku' => $this->sku,
            'name' => $this->name,
            'quantity' => $this->quantity,
            'price' => Number::currency($this->price),
            'final_price' => Number::currency($this->final_price),
            'total' => Number::currency($this->total),
            'discount' => Number::currency($this->discount),
        ];
    }
}
