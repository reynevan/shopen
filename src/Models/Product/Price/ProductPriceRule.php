<?php

namespace Shopen\Models\Product\Price;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Shopen\Models\Product\Product;

class ProductPriceRule extends Model
{
    protected $fillable = [
        'name',
        'from_date',
        'to_date',
        'priority',
        'is_enabled',
        'discount_type',
        'discount_amount',
    ];

    protected function casts(): array
    {
        return [
            'from_date' => 'date',
            'to_date' => 'date',
        ];
    }

    public function getConditionsAttribute()
    {
        return json_decode($this->conditions_serialized, true);
    }

    public function calculateFinalPrice($price)
    {
        if ($this->discount_type === 'percent') {
            return max($price * (100 - $this->discount_amount) / 100, 0);
        } elseif ($this->discount_type === 'amount') {
            return max($price - $this->discount_amount, 0);
        }
        return $price;
    }

    public function isActive(): bool
    {
        $now = new Carbon('now');
        return $this->is_enabled && $now->betweenIncluded($this->from_date, $this->to_date);
    }

    public function getMatchingProducts()
    {
        return $this->getMatchingProductsQuery()->get();
    }

    public function getMatchingProductsQuery()
    {
        return Product::query()
            ->where(function (Builder $query) {
                foreach ($this->conditions['attributes'] as $attrCondition) {
                    $attribute = $this->attributeRepository->getById($attrCondition['attribute_id']);
                    $query->filterByAttribute($attribute, $attrCondition['value'], 'or');
                }
            })
            ->whereHas('categories', function ($query) {
                $query->whereIn('categories.id', $this->conditions['categories']);
            });
    }
}
