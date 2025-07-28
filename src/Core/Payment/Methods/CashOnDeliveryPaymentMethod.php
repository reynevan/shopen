<?php

namespace Shopen\Core\Payment\Methods;

use Shopen\Core\Payment\Methods\AbstractPaymentMethod;
use Shopen\Models\Order\Order;
use Shopen\Models\Order\Payment;

class CashOnDeliveryPaymentMethod extends AbstractPaymentMethod
{
    public function initializePayment(Order $order, array $data = []): Payment
    {
        return $this->createPayment($order, Payment::STATUS_PENDING);
    }

    public function getKey(): string
    {
        return 'cash_on_delivery';
    }
}