<?php

namespace Shopen\Core\Support\Product\Sorting\Sorters;

use Shopen\Core\Support\Product\Sorting\ProductSorter;
use Shopen\Repositories\Product\ProductAttributeRepository;

class PopularitySorter implements ProductSorter
{
    protected string $key;

    public function __construct(private readonly ProductAttributeRepository $attributeRepository)
    {}

    public function setKey($key): static
    {
        $this->key = $key;
        return $this;
    }

    public function keys(): array
    {
        return ['popularity'];
    }

    public function label(string $key): ?string
    {
        return 'Od najpopularniejszych';
    }

    public function build(): array
    {
        return ['popularity' => 'desc'];
    }
}