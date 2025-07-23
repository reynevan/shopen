<?php

namespace Shopen\Blocks\Frontend\Init;

use Shopen\Blocks\Block;
use Shopen\Core\Shipping\ShippingMethodManager;

class Shipping extends Block
{
    public function __construct(private readonly ShippingMethodManager $manager)
    {

    }

    public function getShippingMethods()
    {
        return $this->manager->getShippingMethods();
    }
}