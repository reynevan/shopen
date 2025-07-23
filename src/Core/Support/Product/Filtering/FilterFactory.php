<?php

namespace Shopen\Core\Support\Product\Filtering;

class FilterFactory
{
    public static function make(string $key, $value): FilterInterface
    {
        return match ($key) {
            'price_min' => new PriceFilter($value ?? null, null),
            'price_max' => new PriceFilter(null, $value ?? null),
            'category' => new CategoryFilter($value),
            default => new AttributeFilter($key, $value),
        };
    }
}