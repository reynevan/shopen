<?php

namespace Shopen\Http\Resources\Product;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Number;

class ProductPriceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'final_price' => Number::currency($this->final_price),
            'final_price_raw' => $this->final_price,
            'omnibus_price' => $this->discount_amount ? Number::currency($this->omnibus_price) : null
        ];
    }
}
