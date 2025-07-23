<?php

namespace Shopen\Core\Shipping\Methods;

class PickupShipping extends AbstractShippingMethod
{
    public function getKey(): string
    {
        return 'shopen_pickup';
    }

    public function getName(): string
    {
        return 'Odbiór w punkcie';
    }

    public function getDescription(): ?string
    {
        return 'Dostawa w 2 dni';
    }

    public function getPrice(): float
    {
        return 20;
    }
}