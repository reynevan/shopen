<?php

namespace Shopen\Blocks\Frontend\Checkout;

use Shopen\Blocks\Block;
use Shopen\Core\Payment\PaymentMethodManager;
use Shopen\Core\Shipping\ShippingMethodManager;

class Checkout extends Block
{

    public function __construct(
        private readonly ShippingMethodManager $shippingMethodManager,
        private readonly PaymentMethodManager $paymentMethodManager,
    )
    {

    }

    public function getShippingMethods(): array
    {
        return $this->shippingMethodManager->getShippingMethods();
    }

    public function getPaymentMethods(): array
    {
        return $this->paymentMethodManager->getPaymentMethods();
    }
}