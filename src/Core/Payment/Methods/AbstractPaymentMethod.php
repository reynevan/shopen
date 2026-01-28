<?php

namespace Shopen\Core\Payment\Methods;

use Illuminate\Support\Number;
use JsonSerializable;
use Shopen\Enums\Payment\PaymentStatus;
use Shopen\Models\Order\Order;
use Shopen\Models\Order\Payment;
use Shopen\Services\CartService;
use Shopen\Services\ConfigService;


abstract class AbstractPaymentMethod implements JsonSerializable, PaymentMethodInterface
{
    protected array $config;

    public function __construct(
        protected CartService $cartService,
        protected ConfigService $configService,
    )
    {
        $this->config = $this->getDefaultConfig();
    }
    protected function getConfigField($field)
    {
        return $this->configService->get("payment/{$this->getKey()}/$field") ?? config("payment.{$this->getKey()}.$field");
    }

    protected function getConfigBoolField($field)
    {
        $isActive = $this->configService->get("payment/{$this->getKey()}/$field");
        if (!is_null($isActive)) {
            return $isActive;
        }
        return config("payment.{$this->getKey()}.$field");
    }

    protected function createPayment(Order $order, $amount, bool $isReturn = false, array $additionalData = []): Payment
    {
        return Payment::create([
            'order_id' => $order->id,
            'payment_method' => $this->getKey(),
            'amount' => $amount,
            'status' => PaymentStatus::PENDING,
            'transaction_id' => $this->generateTransactionId(),
            'is_return' => $isReturn,
            'gateway_data' => $additionalData,
        ]);
    }

    protected function generateTransactionId(): string
    {
        return 'TXN_' . uniqid() . '_' . time();
    }

    public function getName(): string
    {
        return $this->getConfigField('name');
    }

    public function getTitle(): string
    {
        return $this->getConfigField('title');
    }

    public function getPaymentDueDays(): int
    {
        return $this->getConfigField('due_days');
    }

    public function getBlock(): string
    {
        return 'payment.payment-method';
    }

    public function isActive(): bool
    {
        return $this->getConfigBoolField('active');
    }

    public function isAvailable(): bool
    {
        if (!$this->isActive()) {
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
        return $this->configService->get("payment/{$this->getKey()}/description") ?? config("payment.{$this->getKey()}.description");
    }

    public function getAdditionalFields(): array
    {
        return [];
    }

    public function jsonSerialize(): array
    {
        return [
            'key' => $this->getKey(),
            'active' => $this->isActive(),
            'name' => $this->getName(),
            'title' => $this->getTitle(),
            'price' => Number::currency($this->getPrice()),
            'description' => $this->getDescription(),
            'additional_fields' => $this->getAdditionalFields(),
        ];
    }

    public function toArray(): array
    {
        return [
            'key' => $this->getKey(),
            'active' => $this->isActive(),
            'name' => $this->getName(),
            'title' => $this->getTitle(),
            'price' => $this->getPrice(),
            'description' => $this->getDescription(),
            'additional_fields' => $this->getAdditionalFields(),
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
        return (float)$this->getConfigField('price');
    }

    public function handleWebhook(array $webhookData): ?Payment
    {
        return null;
    }
}