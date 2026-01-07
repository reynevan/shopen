<?php

namespace Shopen\Core\Payment\Methods;

use JsonSerializable;
use Shopen\Enums\Payment\PaymentStatus;
use Shopen\Models\Order\Order;
use Shopen\Models\Order\Payment;
use Shopen\Services\CartService;


abstract class AbstractPaymentMethod implements JsonSerializable, PaymentMethodInterface
{
    protected array $config;

    public function __construct(
        protected CartService $cartService,
    )
    {
        $this->config = $this->getDefaultConfig();
    }

    protected function createPayment(Order $order, PaymentStatus $status = PaymentStatus::PENDING, array $additionalData = []): Payment
    {
        return Payment::create([
            'order_id' => $order->id,
            'payment_method' => $this->getKey(),
            'amount' => $order->total_amount,
            'status' => $status,
            'transaction_id' => $this->generateTransactionId()
        ]);
    }

    protected function generateTransactionId(): string
    {
        return 'TXN_' . uniqid() . '_' . time();
    }

    public function getComponent(): ?string
    {
        return null;
    }

    public function getName(): string
    {
        return config("payment.{$this->getKey()}.name");
    }

    public function getPaymentDueDays(): int
    {
        return intval(config("payment.{$this->getKey()}.due_days", 0));
    }

    public function getBlock(): string
    {
        return 'payment.payment-method';
    }

    public function isAvailable(): bool
    {
        if (!config("payment.{$this->getKey()}.active")) {
            return false;
        }
        $selectedShippingMethod = $this->cartService->getCart()->shipping_method;
        if (!$selectedShippingMethod) {
            return true;
        }
        $shippingMethods = config("payment.{$this->getKey()}.available_shipping_methods");
        if ($shippingMethods === '*') {
            return true;
        }
        if (!is_array($shippingMethods)) {
            $shippingMethods = [$shippingMethods];
        }
        return in_array($selectedShippingMethod, $shippingMethods);
    }

    public function getDescription(): ?string
    {
        return null;
    }

    public function jsonSerialize(): array
    {
        return [
            'key' => $this->getKey(),
            'name' => $this->getName(),
            'price' => $this->getPrice(),
            'description' => $this->getDescription(),
            'component' => $this->getComponent(),
        ];
    }

    protected function getDefaultConfig(): array
    {
        return [];
    }

    public function getPaymentUrl(Payment $payment): ?string
    {
        return null;
    }

    public function requiresRedirect(): bool
    {
        return false;
    }

    public function checkPaymentStatus(Payment $payment): PaymentStatus
    {
        return $payment->status;
    }

    public function getPrice(): float
    {
        return config("payment.{$this->getKey()}.price") ?? 0;
    }

    public function handleWebhook(array $webhookData): ?Payment
    {
        return null;
    }
}