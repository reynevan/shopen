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
}