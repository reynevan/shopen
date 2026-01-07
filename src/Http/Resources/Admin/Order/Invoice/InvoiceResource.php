<?php

namespace Shopen\Http\Resources\Admin\Order\Invoice;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Number;
use Shopen\Core\Payment\PaymentMethodManager;
use Shopen\Core\Shipping\ShippingMethodManager;
use Shopen\Http\Resources\Admin\Order\OrderResource;
use Shopen\Http\Resources\User\AddressResource;

class InvoiceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'order_id' => $this->order_id,
            'order' => OrderResource::make($this->whenLoaded('order')),
            'items' => InvoiceItemResource::collection($this->whenLoaded('items')),
            'billing_address' => AddressResource::make($this->whenLoaded('billingAddress')),
            'invoice_number' => $this->invoice_number,
            'total_amount' => Number::currency($this->total_amount),
            'total_net_amount' => Number::currency($this->total_net_amount),
            'shipping_amount' => Number::currency($this->shipping_amount),
            'payment_amount' => Number::currency($this->payment_amount),
            'tax_amount' => Number::currency($this->tax_amount),
            'left_to_pay_amount' => Number::currency(abs($this->left_to_pay_amount)),
            'amount_to_return' => $this->left_to_pay_amount < 0 ? abs($this->left_to_pay_amount) : 0,
            'discount_amount' => $this->discount_amount,
            'created_at' => $this->created_at->format('d-m-Y H:i:s'),
            'issue_date' => $this->created_at->format('d-m-Y'),
            'payment_due_date' => $this->payment_due_date?->format('d-m-Y'),
            'shipping_method_label' => $this->shipping_method_label,
            'payment_method_label' => $this->payment_method_label,
            'tax_rates' => $this->when($this->relationLoaded('items'),  $this->getTaxRates()),
            'file_url' => Storage::url($this->file_path),
            'is_correction' => $this->is_correction,
            'base_invoice' => InvoiceResource::make($this->whenLoaded('baseInvoice')),
            'is_address_corrected' => $this->isAddressCorrected(),
            'has_items_corrected' => $this->hasItemsCorrected()
        ];
    }
}
