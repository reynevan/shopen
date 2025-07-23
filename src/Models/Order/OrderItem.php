<?php

namespace Shopen\Models\Order;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Shopen\Models\Product\Product;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'sku',
        'name',
        'quantity',
        'price',
        'final_price',
        'total',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'final_price' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function getDiscountAttribute()
    {
        return $this->price - $this->final_price;
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}