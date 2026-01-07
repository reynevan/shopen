<?php

namespace Shopen\Http\Resources\Admin\Order\Invoice;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Number;
use Shopen\Http\Resources\Admin\Order\ProductResource;
use Shopen\Http\Resources\Admin\PromoCode\PromoCodeCouponResource;

class InvoiceItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'product' => ProductResource::make($this->whenLoaded('product')),
            'sku' => $this->sku,
            'name' => $this->name,
            'unit' => $this->unit,
            'tax_rate' => $this->tax_rate,
            'quantity' => $this->quantity,
            'price' => $this->price,
            'price_net' => $this->price_net,
            'total' => $this->total,
            'total_net' => $this->total_net,
            'tax_amount' => $this->tax_amount,
            'discount_amount' => $this->discount_amount,
            'discount_amount_net' => $this->discount_amount_net,
        ];
    }
}
