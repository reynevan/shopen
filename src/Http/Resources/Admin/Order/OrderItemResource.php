<?php

namespace Shopen\Http\Resources\Admin\Order;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Number;
use Shopen\Http\Resources\Admin\PromoCode\PromoCodeCouponResource;

class OrderItemResource extends JsonResource
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
            'returned_quantity' => $this->returned_quantity,
            'available_to_return_quantity' => $this->quantity - $this->returned_quantity,
            'price' => $this->final_price,
            'price_net' => $this->final_price_net,
            'total' => $this->total,
            'total_net' => $this->total_net,
            'tax_amount' => $this->tax_amount,
            'discount' => $this->discount,
            'discount_amount' => $this->promo_code_discount_amount,
            'discount_amount_net' => $this->promo_code_discount_amount_net,
            'promo_code_coupons' => PromoCodeCouponResource::collection($this->whenLoaded('promoCodeCoupons')),
            'promo_code_coupon_email_sent' => $this->promo_code_coupon_email_sent
        ];
    }
}
