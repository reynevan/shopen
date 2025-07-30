<?php

namespace Shopen\Http\Resources\Product;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Number;

class ProductPriceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $discountAmount = $this->discount_amount;
        $precision = 2;
        if ($discountAmount == (int)$discountAmount) {
            $precision = 0;
        }
        return [
            'price' => Number::currency($this->price),
            'final_price' => Number::currency($this->final_price),
            'final_price_raw' => $this->final_price,
            'omnibus_price' => $this->discount_amount ? Number::currency($this->omnibus_price) : null,
            'discount_amount' =>  $this->discount_amount ? Number::currency($this->discount_amount, '', null, $precision) : null,
            'discount_percent' =>  $this->discount_percent,
        ];
    }
}
