<?php

namespace Shopen\Blocks\Admin\Order;

use Shopen\Blocks\Block;
use Shopen\Core\Context;
use Shopen\Models\Order\Order;

class Show extends Block
{
    public function __construct(
        private readonly Context $context
    )
    {}

    public function getOrder(): ?Order
    {
        return $this->context->getCurrentOrder();
    }
}