<?php

namespace Shopen\Core\Support\Product\Sorting\Sorters;

use Shopen\Core\Support\Product\Sorting\ProductSorter;

class PriceSorter implements ProductSorter
{

    protected string $key;

    public function keys(): array
    {
        return ['price_asc', 'price_desc'];
    }

    public function setKey($key): static
    {
        $this->key = $key;
        return $this;
    }

    public function label(string $key): ?string
    {
        return match ($key) {
            'price_asc' => 'Od najtańszych',
            'price_desc' => 'Od najdroższych',
            default => null,
        };
    }


    public function build(): array
    {
        $direction = $this->key === 'price_asc' ? 'ASC' : 'DESC';
        return ['price' => $direction];
    }
}