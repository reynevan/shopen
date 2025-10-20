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
            'quantity' => $this->quantity,
            'price' => Number::currency($this->price),
            'final_price' => Number::currency($this->final_price),
            'total' => Number::currency($this->total),
            'tax_amount' => Number::currency($this->tax_amount),
            'discount' => Number::currency(-1 * $this->discount),
            'promo_code_discount_amount' => Number::currency(-1 * $this->promo_code_discount_amount),
            'promo_code_coupons' => PromoCodeCouponResource::collection($this->whenLoaded('promoCodeCoupons')),
            'promo_code_coupon_email_sent' => $this->promo_code_coupon_email_sent
        ];
    }
}
