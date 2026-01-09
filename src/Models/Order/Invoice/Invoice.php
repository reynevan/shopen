<?php

namespace Shopen\Models\Order\Invoice;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Number;
use Shopen\Core\Payment\PaymentMethodManager;
use Shopen\Core\Shipping\ShippingMethodManager;
use Shopen\Models\Order\Order;

class Invoice extends Model
{
    protected $fillable = [
        'uuid',
        'order_id',
        'invoice_number',
        'status',
        'shipping_method',
        'payment_method',
        'discount_amount',
        'shipping_amount',
        'payment_amount',
        'total_amount',
        'total_net_amount',
        'tax_amount',
        'payment_due_date',
        'paid_amount',
        'left_to_pay_amount',
        'correction_reason',
        'is_correction',
        'base_invoice_id',
        'correction_payment_method'
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'shipping_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'payment_due_date' => 'date',
        'paid_amount' => 'decimal:2',
        'left_to_pay_amount' => 'decimal:2',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function billingAddress(): HasOne
    {
        return $this->hasOne(InvoiceAddress::class);
    }

    public function baseInvoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class, 'base_invoice_id');
    }

    public function correctionInvoices(): HasMany
    {
        return $this->hasMany(Invoice::class, 'base_invoice_id');
    }

    public function getFileNameAttribute()
    {
        $name = preg_replace('/\D+/', '_', $this->invoice_number);
        $name = trim($name, '_');
        return 'FV_' . $this->id . '_' . $name . '.pdf';
    }

    public function getFilePathAttribute()
    {
        $numberParts = explode('/', $this->invoice_number);
        $pathParts = [
            array_pop($numberParts),
            array_pop($numberParts)
        ];
        return 'faktury/' . implode('/', $pathParts) . '/' . $this->file_name;
    }

    public function getTaxRates()
    {
        $rates = [];
        foreach ($this->items as $item) {
            if (!isset($rates[$item->tax_rate])) {
                $rates[$item->tax_rate] = [
                    'rate' => $item->tax_rate,
                    'amount' => 0,
                    'amount_net' => 0,
                    'tax_amount' => 0
                ];
            }
            $rates[$item->tax_rate]['amount'] += $item->total;
            $rates[$item->tax_rate]['amount_net'] += $item->total_net;
            $rates[$item->tax_rate]['tax_amount'] += $item->tax_amount;
        }
        foreach ($rates as $i => $rate) {
            foreach ($rate as $k => $v) {
                if ($k === 'rate') {
                    continue;
                }
                $rates[$i][$k] = Number::currency($v);
            }
        }
        return $rates;
    }

    public function getPaymentMethodLabelAttribute()
    {
        if ($this->is_correction) {
            return $this->correction_payment_method;
        }
        return $this->payment_method ? app(PaymentMethodManager::class)->get($this->payment_method)?->getName() ?? '-' : null;
    }

    public function getShippingMethodLabelAttribute()
    {
        return $this->shipping_method ? app(ShippingMethodManager::class)->get($this->shipping_method)?->getName() ?? '-' : null;
    }

    public function isAddressCorrected(): bool
    {
        if (!$this->baseInvoice || !$this->is_correction) {
            return false;
        }
        if (!$this->baseInvoice->billingAddress || !$this->billingAddress) {
            return false;
        }
        $baseAddress = $this->baseInvoice->billingAddress;
        $address = $this->billingAddress;
        $attributes = ['first_name', 'last_name', 'email', 'company', 'company_nip', 'address_line', 'city', 'postal_code', 'country', 'phone'];
        foreach ($attributes as $attribute) {
            if ($baseAddress->getAttribute($attribute) !== $address->getAttribute($attribute)) {
                return true;
            }
        }
        return false;
    }

    public function hasItemsCorrected(): bool
    {
        if (!$this->baseInvoice || !$this->is_correction) {
            return false;
        }
        if ($this->items->count() !== $this->baseInvoice->items->count()) {
            return true;
        }
        $attributes = ['name', 'quantity', 'price', 'price_net', 'discount_amount', 'discount_amount_net', 'tax_rate', 'tax_amount', 'unit'];
        foreach ($this->items as $item) {
            foreach ($attributes as $attribute) {
                if ($item->getAttribute($attribute) !== $item->baseItem->getAttribute($attribute)) {
                    return true;
                }
            }
        }
        return false;
    }

}