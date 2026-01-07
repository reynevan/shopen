<?php

namespace Shopen\Models\Order\Invoice;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Shopen\Models\Product\Product;

class InvoiceItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'nivoice_id',
        'product_id',
        'sku',
        'name',
        'quantity',
        'price',
        'final_price',
        'total',
        'tax_amount',
        'promo_code_discount_amount',
        'unit',
        'tax_rate'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'final_price' => 'decimal:2',
        'total' => 'decimal:2'
    ];

    public function getDiscountAttribute()
    {
        return $this->price - $this->final_price;
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function baseItem(): BelongsTo
    {
        return $this->belongsTo(InvoiceItem::class, 'base_invoice_item_id');
    }
}