<?php

namespace Shopen\Http\Controllers\Frontend\User\Order;

use Inertia\Inertia;
use Shopen\Core\Payment\PaymentMethodManager;
use Shopen\Models\Order\Order;

class UserOrderPayController
{
    public function __construct(protected PaymentMethodManager $paymentMethodManager)
    {}

    public function pay(Order $order)
    {
        $paymentMethod = $this->paymentMethodManager->get($order->payment_method);

        $payment = $paymentMethod->initializePayment($order, ['continueUrl' => route('user.orders.show', $order->uuid)]);

        return Inertia::location($paymentMethod->getPaymentUrl($payment));
    }
}