<?php

namespace Shopen\Core\Shipping\Methods;

interface ShippingMethodInterface
{
    public function getKey(): string;

    public function getName(): string;

    public function getTitle(): string;

    public function getPrice(): float;

    public function calculatePrice(): float;

    public function toArray(): array;

    public function jsonSerialize(): array;

    public function isFreeShippingAvailable(): bool;

    public function freeShippingThreshold(): int;
}