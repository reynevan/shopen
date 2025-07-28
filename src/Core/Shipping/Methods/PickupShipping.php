<?php

namespace Shopen\Core\Shipping\Methods;

class PickupShipping extends AbstractShippingMethod
{
    public function getKey(): string
    {
        return 'shopen_pickup';
    }
}