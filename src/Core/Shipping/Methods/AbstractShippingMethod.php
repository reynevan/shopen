<?php

namespace Shopen\Core\Shipping\Methods;

use Illuminate\Support\Number;
use JsonSerializable;
use Shopen\Services\CartService;
use Shopen\Services\ConfigService;

abstract class AbstractShippingMethod implements JsonSerializable, ShippingMethodInterface
{
    public function __construct(
        protected CartService $cartService,
        protected ConfigService $configService,
    )
    {}

    protected ?string $view = null;

    abstract public function getKey(): string;

    protected function getConfigField($field)
    {
        return $this->configService->get("shipping/{$this->getKey()}/$field") ?? config("shipping.{$this->getKey()}.$field");
    }

    protected function getConfigBoolField($field)
    {
        $isActive = $this->configService->get("shipping/{$this->getKey()}/$field");
        if (!is_null($isActive)) {
            return $isActive;
        }
        return config("shipping.{$this->getKey()}.$field");
    }

    public function getName(): string
    {
        return $this->getConfigField('name');
    }

    public function getTitle(): string
    {
        return $this->getConfigField('title');
    }

    public function isActive(): bool
    {
        return $this->getConfigBoolField('active');
    }

    public function isVirtual()
    {
        return config("shipping.{$this->getKey()}.virtual", false);
    }

    public function isTrackable(): bool
    {
        return config("shipping.{$this->getKey()}.trackable", false);
    }

    public function getDescription(): ?string
    {
        return $this->getConfigField('description');
    }

    public function getComponent(): ?string
    {
        return null;
    }

    public function jsonSerialize(): array
    {
        return [
            'key' => $this->getKey(),
            'active' => $this->isActive(),
            'name' => $this->getName(),
            'title' => $this->getTitle(),
            'description' => $this->getDescription(),
            'price' => Number::currency($this->calculatePrice()),
            'component' => $this->getComponent()
        ];
    }

    public function toArray(): array
    {
        return [
            'key' => $this->getKey(),
            'active' => $this->isActive(),
            'name' => $this->getName(),
            'title' => $this->getTitle(),
            'description' => $this->getDescription(),
            'price' => $this->getPrice(),
            'component' => $this->getComponent(),
            'free_shipping_available' => $this->isFreeShippingAvailable(),
            'free_shipping_threshold' => $this->freeShippingThreshold()
        ];
    }

    public function isFreeShippingAvailable(): bool
    {
        return $this->getConfigBoolField('free_shipping_available');
    }

    public function freeShippingThreshold(): int
    {
        return (float)$this->getConfigField('free_shipping_threshold');
    }

    public function calculatePrice(): float
    {
        if ($this->isFreeShippingAvailable() &&
            $this->cartService->getCart()->totalPrice() >= $this->freeShippingThreshold()
        ) {
            return 0;
        }
        return (float)$this->getConfigField('price');
    }

    public function getPrice(): float
    {
        return (float)$this->getConfigField('price');
    }
}