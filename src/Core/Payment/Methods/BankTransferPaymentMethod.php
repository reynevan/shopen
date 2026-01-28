<?php

namespace Shopen\Core\Payment\Methods;

use Shopen\Enums\Payment\PaymentStatus;
use Shopen\Models\Order\Order;
use Shopen\Models\Order\Payment;

class BankTransferPaymentMethod extends AbstractPaymentMethod
{
    public function initializePayment(Order $order, array $data = []): Payment
    {
        return $this->createPayment($order, $order->total_amount, false, [
            'transfer_title' => 'Zamówienie nr ' . $order->order_number,
        ]);
    }
    public function initializeReturnPayment(Order $order, ?Payment $payment, $amount, array $data = []): Payment
    {
        return $this->createPayment($order, $amount, true, [
            'transfer_title' => 'Zamówienie nr ' . $order->order_number,
        ]);
    }

    public function getTransferDetails()
    {
        return $this->getConfigField('transfer_details');
    }

    public function getAdditionalFields(): array
    {
        return [
            'transfer_details' => [
                'key' => 'transfer_details',
                'label' => 'Dane do przelewu',
                'value' => $this->getTransferDetails(),
                'input' => 'textarea'
            ]
        ];
    }

    public function checkPaymentStatus(Payment $payment): PaymentStatus
    {
        return $payment->status;
    }

    public function getKey(): string
    {
        return 'bank_transfer';
    }
}