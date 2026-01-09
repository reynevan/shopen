<?php

namespace Shopen\Core\Payment\Methods;

use Shopen\Enums\Payment\PaymentStatus;
use Shopen\Models\Order\Order;
use Shopen\Models\Order\Payment;

interface PaymentMethodInterface
{

    public function initializePayment(Order $order, array $data = []): Payment;

    public function initializeReturnPayment(Order $order, ?Payment $payment, $amount, array $data = []): Payment;

    public function requiresRedirect(): bool;

    public function getPaymentUrl(Payment $payment): ?string;

    public function handleWebhook(array $webhookData): ?Payment;

    public function checkPaymentStatus(Payment $payment): PaymentStatus;

    public function getName(): string;

    public function getPaymentDueDays(): int;

    public function getKey(): string;

    public function isAvailable(): bool;

    public function getPrice(): float;

    public function jsonSerialize(): array;
}