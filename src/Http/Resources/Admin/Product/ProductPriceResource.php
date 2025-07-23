<?php

namespace Shopen\Http\Resources\Admin\Product;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductPriceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'price' => $this->price,
            'final_price' => $this->final_price,
            'special_price' => $this->special_price,
            'special_price_from' => $this->special_price_from?->format('Y-m-d'),
            'special_price_to' => $this->special_price_to?->format('Y-m-d'),
        ];
    }
}
