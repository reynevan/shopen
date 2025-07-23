<?php

namespace Shopen\Blocks\Frontend\Category\Show;

use App\Support\ProductSorting\ProductSortRegistry;
use Shopen\Blocks\Block;
use Shopen\Models\Product\Product;

class ProductsList extends Block
{
    public function __construct(protected ProductSortRegistry $productSortRegistry)
    {}

    public function getSortOptions()
    {
        return $this->productSortRegistry->allOptions();
    }
}