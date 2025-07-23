<?php

namespace Shopen\Blocks\Frontend\Shipping;

use Shopen\Blocks\Block;

class ShippingMethod extends Block
{
    public function __construct(private array $data)
    {}

    public function getShippingMethod()
    {
        return $this->data['shipping_method'];
    }
}