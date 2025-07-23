<?php

namespace Shopen\Services;

use Shopen\Core\Payment\Methods\PaymentMethodInterface;
use Shopen\Core\Payment\PaymentMethodManager;
use Shopen\Models\Order\Order;
use Shopen\Models\Order\Payment;

class PaymentService
{

    public function __construct(
        private readonly PaymentMethodManager $paymentMethodManager,
    )
    {}

    public function getPaymentMethod(string $code): ?PaymentMethodInterface
    {
        return $this->paymentMethodManager->get($code);
    }

    public function processPayment(Order $order, string $paymentMethodCode, array $data = []): Payment
    {
        $paymentMethod = $this->getPaymentMethod($paymentMethodCode);

        if (!$paymentMethod) {
            throw new \InvalidArgumentException("Payment method '{$paymentMethodCode}' not found");
        }

        if (!$paymentMethod->isAvailable()) {
            throw new \InvalidArgumentException("Payment method '{$paymentMethodCode}' is not available");
        }

        return $paymentMethod->initializePayment($order, $data);
    }

    public function handleWebhook(string $paymentMethodCode, array $webhookData): ?Payment
    {
        $paymentMethod = $this->getPaymentMethod($paymentMethodCode);

        if (!$paymentMethod) {
            return null;
        }

        $payment = $paymentMethod->handleWebhook($webhookData);

        if ($payment && $payment->isCompleted()) {
            $this->markOrderAsPaid($payment->order);
        }

        return $payment;
    }

    public function markPaymentAsCompleted(Payment $payment, ?string $gatewayTransactionId = null): void
    {
        $payment->update([
            'status' => Payment::STATUS_COMPLETED,
            'gateway_transaction_id' => $gatewayTransactionId ?: $payment->gateway_transaction_id,
            'processed_at' => now(),
        ]);

        $this->markOrderAsPaid($payment->order);
    }

    public function markPaymentAsFailed(Payment $payment, ?string $reason = null): void
    {
        $payment->update([
            'status' => Payment::STATUS_FAILED,
            'notes' => $reason,
            'processed_at' => now(),
        ]);
    }

    private function markOrderAsPaid(Order $order): void
    {
        if ($order->status === 'pending') {
            $order->update(['status' => 'paid']);

            // Wyślij email o potwierdzeniu płatności
            // Mail::to($order->user->email)->send(new PaymentConfirmed($order));
        }
    }
}