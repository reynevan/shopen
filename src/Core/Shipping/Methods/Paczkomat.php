<?php

namespace Shopen\Core\Shipping\Methods;

class Paczkomat extends AbstractShippingMethod implements ShippingMethodInterface
{
    protected float $price = 12;

    public function getKey(): string
    {
        return 'paczkomaty';
    }

    public function getComponent(): ?string
    {
        return 'PaczkomatyShippingMethod';
    }

    public function jsonSerialize(): array
    {
        $data = parent::jsonSerialize();
        $data['token'] = config('shipping.paczkomaty.geo_token');
        return $data;
    }
}
