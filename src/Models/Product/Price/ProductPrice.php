<?php

namespace Shopen\Models\Product\Price;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductPrice extends Model
{
    protected $fillable = [
        'price',
        'final_price',
        'special_price',
        'special_price_from',
        'special_price_to',
    ];

    protected function casts(): array
    {
        return [
            'special_price_from' => 'date',
            'special_price_to' => 'date',
        ];
    }

    public function priceRule(): BelongsTo
    {
        return $this->belongsTo(ProductPriceRule::class, 'rule_id');
    }

    public function isDiscounted(): bool
    {
        return $this->price > $this->final_price;
    }
}
