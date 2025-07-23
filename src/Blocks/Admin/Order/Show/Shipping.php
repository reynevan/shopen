<?php

namespace Shopen\Blocks\Admin\Order\Show;

use Shopen\Blocks\Admin\Order\Show;
use Shopen\Core\Context;
use Shopen\Core\Shipping\ShippingMethodManager;

class Shipping extends Show
{
    public function __construct(
        private readonly ShippingMethodManager $shippingMethodManager,
        Context $context
    )
    {
        parent::__construct($context);
    }

    public function getShippingMethodName(): string
    {
        return $this->shippingMethodManager->get($this->getOrder()->shipping_method)->getName() ?? '-';
    }
}