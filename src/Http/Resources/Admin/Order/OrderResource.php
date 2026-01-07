<?php

namespace Shopen\Http\Resources\Admin\Order;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Number;
use Shopen\Core\Payment\PaymentMethodManager;
use Shopen\Core\Shipping\ShippingMethodManager;
use Shopen\Http\Resources\Admin\Order\Invoice\InvoiceResource;
use Shopen\Http\Resources\Admin\PromoCode\PromoCodeCouponResource;
use Shopen\Http\Resources\User\AddressResource;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'order_number' => $this->order_number,
            'status' => $this->status,
            'created_at' => $this->created_at->translatedFormat('M d, Y H:i:s'),
            'status_label' => $this->status_label,
            'status_history' => StatusHistoryResource::collection($this->whenLoaded('statusHistoryItems')),
            'total_amount' => Number::currency($this->total_amount - $this->returned_amount - $this->shipping_amount_returned),
            'subtotal' => Number::currency($this->subtotal),
            'returned_amount' => $this->returned_amount > 0 ? Number::currency($this->returned_amount) : null,
            'shipping_amount' => Number::currency($this->shipping_amount),
            'shipping_amount_returned' => $this->shipping_amount_returned > 0 ? Number::currency($this->shipping_amount_returned) : null,
            'payment_amount' => Number::currency($this->payment_amount),
            'tax_amount' => Number::currency($this->tax_amount),
            'promo_code_discount_amount' => Number::currency(-1 * $this->promo_code_discount_amount),
            'shipping_address' => AddressResource::make($this->whenLoaded('shippingAddress')),
            'billing_address' => AddressResource::make($this->whenLoaded('billingAddress')),
            'items' => OrderItemResource::collection($this->whenLoaded('items')),
            'shipping_method_label' => app(ShippingMethodManager::class)->get($this->shipping_method)?->getName() ?? '-',
            'shipping_method_trackable' => app(ShippingMethodManager::class)->get($this->shipping_method)?->isTrackable() ?? false,
            'payment_method_label' => app(PaymentMethodManager::class)->get($this->payment_method)?->getName() ?? '-',
            'delivery_point_code' => $this->delivery_point_code,
            'shipped_at' => $this->shipped_at?->translatedFormat('M d, Y H:i:s'),
            'shipping_tracking_code' => $this->shipping_tracking_code,
            'items_count' => $this->items_count,
            'promo_code_coupon' => PromoCodeCouponResource::make($this->whenLoaded('promoCodeCoupon')),
            'payments' => PaymentResource::collection($this->whenLoaded('payments')),
            'has_vouchers' => $this->hasVouchers(),
            'invoices' => InvoiceResource::collection($this->whenLoaded('invoices')),
            'placed_date' => $this->placed_date,
        ];
    }
}
