<?php

namespace Shopen\Http\Resources\Admin\Order;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Number;
use Shopen\Core\Payment\PaymentMethodManager;
use Shopen\Core\Shipping\ShippingMethodManager;
use Shopen\Http\Resources\Admin\PromoCode\PromoCodeCouponResource;
use Shopen\Http\Resources\User\AddressResource;

class PaymentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'amount' => Number::currency($this->amount),
            'status' => $this->status,
            'status_label' => $this->status_label,
            'gateway_transaction_id' => $this->gateway_transaction_id
        ];
    }
}
