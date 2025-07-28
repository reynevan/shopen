<?php

namespace Shopen\Models\Product\Price;

use Illuminate\Database\Eloquent\Model;

class ProductPriceHistoryItem extends Model
{
    protected $fillable = [
        'product_id',
        'price',
        'valid_from'
    ];
}
