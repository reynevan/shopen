<?php

namespace Shopen\Core\Payment\Methods;

use Shopen\Models\Order\Order;
use Shopen\Models\Order\Payment;

class BankTransferPaymentMethod extends AbstractPaymentMethod
{
    public function initializePayment(Order $order, array $data = []): Payment
    {
        return $this->createPayment($order, Payment::STATUS_PENDING, [
            'bank_account' => $this->config['bank_account'],
            'bank_name' => $this->config['bank_name'],
            'transfer_title' => 'Order ' . $order->order_number,
        ]);
    }

    public function requiresRedirect(): bool
    {
        return false;
    }

    public function getPaymentUrl(Payment $payment): ?string
    {
        return null;
    }

    public function handleWebhook(array $webhookData): ?Payment
    {
        return null;
    }

    public function checkPaymentStatus(Payment $payment): string
    {
        return $payment->status;
    }

    public function getName(): string
    {
        return 'Przelew';
    }

    public function getKey(): string
    {
        return 'bank_transfer';
    }

    public function isAvailable(): bool
    {
        return parent::isAvailable() && !empty($this->config['bank_account']);
    }

    protected function getDefaultConfig(): array
    {
        return [
            'bank_account' => config('payment.bank_transfer.account_number'),
            'bank_name' => config('payment.bank_transfer.bank_name'),
        ];
    }

    public function getPrice(): float
    {
        return 0;
    }
}