<?php

namespace Shopen\Services;

use Shopen\Core\Shipping\ShippingMethodManager;
use Shopen\Models\Product\Product;

class ShippingService
{
    public function __construct(
        protected ShippingMethodManager $shippingMethodManager,
        protected CartService $cartService,
    )
    {}

    public function isFreeShippingAvailable(Product $product)
    {
        $methods = $this->shippingMethodManager->getShippingMethods();
        foreach ($methods as $method) {
            if (!$method->isFreeShippingAvailable()) {
                continue;
            }
            if ($method->freeShippingThreshold() <= $product->price->final_price ?? 0) {
                return true;
            }
        }
        return false;
    }

    public function getAvailableShippingMethods(): array
    {
        $methods = $this->shippingMethodManager->getShippingMethods();
        $availableMethods = [];
        $isCartVirtual = $this->cartService->getCart()->hasOnlyVirtualItems();
        foreach ($methods as $method) {
            if (($isCartVirtual && $method->isVirtual()) || (!$isCartVirtual && !$method->isVirtual())) {
                $availableMethods[] = $method;
            }
        }
        return $availableMethods;
    }
}