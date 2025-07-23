<?php

namespace Shopen\Core\Shipping\Methods;

use Illuminate\Support\Number;
use JsonSerializable;

abstract class AbstractShippingMethod implements JsonSerializable, ShippingMethodInterface
{
    protected ?string $view = null;

    abstract public function getKey(): string;

    abstract public function getName(): string;

    abstract public function getPrice(): float;

    public function getDescription(): ?string
    {
        return null;
    }

    public function getComponent(): ?string
    {
        return null;
    }

    public function jsonSerialize(): array
    {
        return [
            'key' => $this->getKey(),
            'name' => $this->getName(),
            'description' => $this->getDescription(),
            'price' => Number::currency($this->getPrice()),
            'component' => $this->getComponent(),
        ];
    }
}