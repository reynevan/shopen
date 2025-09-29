<?php

namespace Shopen\Http\Resources\Order;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Number;
use Shopen\Core\Payment\PaymentMethodManager;
use Shopen\Core\Shipping\ShippingMethodManager;
use Shopen\Http\Resources\User\AddressResource;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'order_number' => $this->order_number,
            'status' => $this->status,
            'status_label' => $this->status_label,
            'total_amount' => Number::currency($this->total_amount),
            'subtotal' => Number::currency($this->finalProductsAmount()),
            'shipping_amount' => Number::currency($this->shipping_amount),
            'payment_amount' => Number::currency($this->payment_amount),
            'promo_code_discount_amount' => Number::currency($this->promo_code_discount_amount),
            'promo_code' => PromoCodeResource::make($this->whenLoaded('promoCode')),
            'shipping_address' => AddressResource::make($this->whenLoaded('shippingAddress')),
            'billing_address' => AddressResource::make($this->whenLoaded('billingAddress')),
            'items' => OrderItemResource::collection($this->whenLoaded('items')),
            'placed_time' => $this->placedTime,
            'placed_date' => $this->placedDate,
            'shipping_method_label' => app(ShippingMethodManager::class)->get($this->shipping_method)?->getName() ?? '-',
            'payment_method_label' => app(PaymentMethodManager::class)->get($this->payment_method)?->getName() ?? '-',
            'delivery_point_code' => $this->delivery_point_code,
            'shipped_at' => $this->shipped_at,
            'shipping_tracking_code' => $this->shipping_tracking_code,
            'can_cancel' => $this->canBeCancelled()
        ];
    }
}
