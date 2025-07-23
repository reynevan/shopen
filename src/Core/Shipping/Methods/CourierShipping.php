<?php

namespace Shopen\Core\Shipping\Methods;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use JsonSerializable;

class CourierShipping extends AbstractShippingMethod
{
    public function getKey(): string
    {
        return 'shopen_courier';
    }

    public function getName(): string
    {
        return 'Kurier';
    }

    public function getDescription(): ?string
    {
        return 'Wysyłka w 24h';
    }

    public function getPrice(): float
    {
        return 15;
    }
}