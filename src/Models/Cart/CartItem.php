<?php

namespace Shopen\Models\Cart;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Shopen\Models\Product\Product;

class CartItem extends Model
{

    protected $fillable = [
        'cart_id',
        'product_id',
        'quantity',
        'price',
        'final_price',
        'total'
    ];

    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
