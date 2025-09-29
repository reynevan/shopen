<?php

namespace Shopen\Models\Product\Price;

use Carbon\Carbon;
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
        'omnibus_price',
        'product_id',
        'rule_id'
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

    public function getOmnibusPrice()
    {
        if ($this->price === $this->final_price) {
            return null;
        }
        $currentPriceFromDate = ProductPriceHistoryItem::query()
            ->where('product_id', $this->product_id)
            ->latest()
            ->first();
        $lowestHistoryPrice = ProductPriceHistoryItem::query()
            ->where('product_id', $this->product_id)
            ->where('valid_to', '>', Carbon::make($currentPriceFromDate->valid_from)->subDays(30))
            ->min('price');

        return $lowestHistoryPrice ? $lowestHistoryPrice : $this->price;
    }

    public function getDiscountAmountAttribute()
    {
        if (!$this->omnibus_price || $this->final_price > $this->omnibus_price) {
            return null;
        }
        return $this->omnibus_price - $this->final_price;
    }

    public function getDiscountPercentAttribute()
    {
        if (!$this->omnibus_price || $this->final_price > $this->omnibus_price) {
            return null;
        }

        return ceil(100 * $this->discount_amount / $this->omnibus_price);
    }
}
