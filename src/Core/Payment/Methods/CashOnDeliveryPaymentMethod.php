<?php

namespace Shopen\Core\Payment\Methods;

use Shopen\Core\Payment\Methods\AbstractPaymentMethod;
use Shopen\Enums\Payment\PaymentStatus;
use Shopen\Models\Order\Order;
use Shopen\Models\Order\Payment;

class CashOnDeliveryPaymentMethod extends AbstractPaymentMethod
{
    public function initializePayment(Order $order, array $data = []): Payment
    {
        return $this->createPayment($order, $order->total_amount);
    }

    public function initializeReturnPayment(Order $order, ?Payment $payment, $amount, array $data = []): Payment
    {
        return $this->createPayment($order, $order->total_amount, true);
    }

    public function getKey(): string
    {
        return 'cash_on_delivery';
    }
}