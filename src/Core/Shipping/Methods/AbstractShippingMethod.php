<?php

namespace Shopen\Core\Shipping\Methods;

use Illuminate\Support\Number;
use JsonSerializable;
use Shopen\Services\CartService;

abstract class AbstractShippingMethod implements JsonSerializable, ShippingMethodInterface
{
    public function __construct(
        protected CartService $cartService,
    )
    {}

    protected ?string $view = null;

    abstract public function getKey(): string;

    public function getName(): string
    {
        return config("shipping.{$this->getKey()}.name");
    }

    public function isVirtual()
    {
        return config("shipping.{$this->getKey()}.virtual", false);
    }

    public function getDescription(): ?string
    {
        return config("shipping.{$this->getKey()}.description");
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

    public function isFreeShippingAvailable(): bool
    {
        return (bool)config("shipping.{$this->getKey()}.free_shipping_available");
    }

    public function freeShippingThreshold(): int
    {
        return config("shipping.{$this->getKey()}.free_shipping_threshold") ?? 0;
    }

    public function getPrice(): float
    {
        if ($this->isFreeShippingAvailable() &&
            $this->cartService->getCart()->totalPrice() >= $this->freeShippingThreshold()
        ) {
            return 0;
        }
        return (float)config("shipping.{$this->getKey()}.price");
    }
}