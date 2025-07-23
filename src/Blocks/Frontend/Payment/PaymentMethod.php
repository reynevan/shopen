<?php

namespace Shopen\Blocks\Frontend\Payment;

use Shopen\Blocks\Block;

class PaymentMethod extends Block
{
    public function __construct(private array $data)
    {}

    public function getPaymentMethod()
    {
        return $this->data['payment_method'];
    }
}