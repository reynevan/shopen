<?php

namespace Shopen\Core\Shipping\Methods;

class Paczkomat extends AbstractShippingMethod implements ShippingMethodInterface
{
    public function getKey(): string
    {
        return 'paczkomaty';
    }

    public function jsonSerialize(): array
    {
        $data = parent::jsonSerialize();
        $data['token'] = config('shipping.paczkomaty.geo_token');
        return $data;
    }
}
