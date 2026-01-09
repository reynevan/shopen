<?php

namespace Shopen\Core\Payment\Methods;

use Shopen\Enums\Payment\PaymentStatus;
use Shopen\Models\Order\Order;
use Shopen\Models\Order\Payment;

class BankTransferPaymentMethod extends AbstractPaymentMethod
{
    public function initializePayment(Order $order, array $data = []): Payment
    {
        return $this->createPayment($order, PaymentStatus::PENDING, false, [
            'bank_account' => $this->config['bank_account'],
            'bank_name' => $this->config['bank_name'],
            'transfer_title' => 'Order ' . $order->order_number,
        ]);
    }
    public function initializeReturnPayment(Order $order, ?Payment $payment, $amount, array $data = []): Payment
    {
        return $this->createPayment($order, $order->total_amount, true, [
            'bank_account' => $this->config['bank_account'],
            'bank_name' => $this->config['bank_name'],
            'transfer_title' => 'Order ' . $order->order_number,
        ]);
    }

    public function checkPaymentStatus(Payment $payment): PaymentStatus
    {
        return $payment->status;
    }

    public function getKey(): string
    {
        return 'bank_transfer';
    }

    protected function getDefaultConfig(): array
    {
        return [
            'bank_account' => config('payment.bank_transfer.account_number'),
            'bank_name' => config('payment.bank_transfer.bank_name'),
        ];
    }
}