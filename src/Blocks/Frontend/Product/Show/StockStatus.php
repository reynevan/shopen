<?php

namespace Shopen\Blocks\Frontend\Product\Show;

use Shopen\Blocks\Block;
use Shopen\Core\Context;

class StockStatus extends Block
{
    public function __construct(private readonly Context $context)
    {}

    public function getProduct()
    {
        return $this->context->getCurrentProduct();
    }

    public function isInStock()
    {
        return $this->getProduct()->in_stock;
    }
}