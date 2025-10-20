<?php

namespace Shopen\Http\Resources\Order;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Number;
use Shopen\Core\Payment\PaymentMethodManager;
use Shopen\Core\Shipping\ShippingMethodManager;
use Shopen\Http\Resources\Payment\PaymentResource;
use Shopen\Http\Resources\PromoCode\PromoCodeCouponResource;
use Shopen\Http\Resources\User\AddressResource;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $paymentMethod = app(PaymentMethodManager::class)->get($this->payment_method);
        return [
            'billing_address' => AddressResource::make($this->whenLoaded('billingAddress')),
            'can_cancel' => $this->canBeCancelled(),
            'can_pay' => $paymentMethod->requiresRedirect(),
            'delivery_point_code' => $this->delivery_point_code,
            'id' => $this->id,
            'items' => OrderItemResource::collection($this->whenLoaded('items')),
            'order_number' => $this->order_number,
            'payment_method_label' => $paymentMethod?->getName() ?? '-',
            'payment_amount' => Number::currency($this->payment_amount),
            'payments' => PaymentResource::collection($this->whenLoaded('payments')),
            'placed_time' => $this->placedTime,
            'placed_date' => $this->placedDate,
            'promo_code_discount_amount' => Number::currency($this->promo_code_discount_amount),
            'promo_code' => PromoCodeCouponResource::make($this->whenLoaded('promoCodeCoupon')),
            'shipped_at' => $this->shipped_at,
            'shipping_address' => AddressResource::make($this->whenLoaded('shippingAddress')),
            'shipping_amount' => Number::currency($this->shipping_amount),
            'shipping_amount_raw' => $this->shipping_amount,
            'shipping_method_label' => app(ShippingMethodManager::class)->get($this->shipping_method)?->getName() ?? '-',
            'shipping_tracking_code' => $this->shipping_tracking_code,
            'status' => $this->status,
            'status_label' => $this->status_label,
            'subtotal' => Number::currency($this->finalProductsAmount()),
            'total_amount' => Number::currency($this->total_amount),
            'tax_amount' => Number::currency($this->tax_amount),
            'tax_amount_raw' => $this->tax_amount,
            'uuid' => $this->uuid,
            'email' => $this->getCustomerEmail()
        ];
    }
}
