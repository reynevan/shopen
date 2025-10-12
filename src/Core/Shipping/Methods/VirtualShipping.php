<?php

namespace Shopen\Core\Shipping\Methods;

class VirtualShipping extends AbstractShippingMethod
{
    public function getKey(): string
    {
        return 'shopen_virtual';
    }
}