<?php

namespace Shopen\Core\Payment\Methods;

use Shopen\Models\Order\Order;
use Shopen\Models\Order\Payment;

class GooglePayPaymentMethod extends AbstractPaymentMethod
{
    public function initializePayment(Order $order, array $data = []): Payment
    {
        return $this->createPayment($order, Payment::STATUS_PENDING);
    }

    public function getKey(): string
    {
        return 'google_pay';
    }

    public function getComponent(): ?string
    {
        return 'GooglePay';
    }
}