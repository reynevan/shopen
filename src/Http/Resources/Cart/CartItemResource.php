<?php

namespace Shopen\Http\Resources\Cart;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Number;
use Shopen\Http\Resources\Attribute\FilterResource;
use Shopen\Models\Category\Category;
use Shopen\Models\Product\Product;

class CartItemResource extends JsonResource
{

    public function toArray(Request $request): array
    {
        $product = $this->resource->product;

        return [
            'id' => $this->id,
            'quantity' => $this->quantity,
            'price' => Number::currency($this->price),
            'total_price' => Number::currency(round($this->price * $this->quantity, 2)),
            'final_price' => Number::currency($this->final_price),
            'final_price_raw' => $this->final_price,
            'total_final_price' => Number::currency(round($this->final_price * $this->quantity, 2)),
            'total_final_price_raw' => round($this->final_price * $this->quantity, 2),
            'product' => [
                'id' => $product->id,
                'sku' => $product->sku,
                'name' => $product->getCustomAttribute('name'),
                'image' => $product->getThumbnail(),
                'attributes' => $product->getVariantAttributes(),
                'url' => $product->getUrl(),
            ]
        ];
    }

}
