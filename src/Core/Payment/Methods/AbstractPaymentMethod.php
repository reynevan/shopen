<?php

namespace Shopen\Core\Payment\Methods;

use JsonSerializable;
use Shopen\Models\Order\Order;
use Shopen\Models\Order\Payment;


abstract class AbstractPaymentMethod implements JsonSerializable, PaymentMethodInterface
{
    protected array $config;

    public function __construct(array $config = [])
    {
        $this->config = array_merge($this->getDefaultConfig(), $config);
    }

    protected function createPayment(Order $order, string $status = 'pending', array $additionalData = []): Payment
    {
        return Payment::create([
            'order_id' => $order->id,
            'payment_method' => $this->getCode(),
            'amount' => $order->total_amount,
            'status' => $status,
            'transaction_id' => $this->generateTransactionId(),
            'gateway_data' => $additionalData,
        ]);
    }

    protected function generateTransactionId(): string
    {
        return 'TXN_' . uniqid() . '_' . time();
    }

    public function getBlock(): string
    {
        return 'payment.payment-method';
    }

    public function isAvailable(): bool {
        return config("payment.{$this->getKey()}.active");
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
          'description' => $this->getDescription()
        ];
    }

    abstract protected function getDefaultConfig(): array;
}