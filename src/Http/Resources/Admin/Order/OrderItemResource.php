<?php

namespace Shopen\Http\Resources\Admin\Order;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Number;
use Shopen\Http\Resources\Admin\Product\ProductResource;

class OrderItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'product' => ProductResource::make($this->resource->product),
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
