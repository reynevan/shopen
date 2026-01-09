<?php

namespace Shopen\Core\Payment\Methods;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use OpenPayU_Configuration;
use OpenPayU_Order;
use OpenPayU_Refund;
use Shopen\Enums\Payment\PaymentStatus;
use Shopen\Models\Order\Order;
use Shopen\Models\Order\Payment;

class PayUPaymentMethod extends AbstractPaymentMethod
{

    public function initializePayment(Order $order, array $data = []): Payment
    {
        $payment = $this->createPayment($order, $order->total_amount);

        try {
            $response = $this->createPayUOrder($order, $payment, $data);

            $payment->update([
                'gateway_transaction_id' => $response->orderId ?? null,
                'gateway_data' => $response,
            ]);

            return $payment;
        } catch (\Exception $e) {
            $payment->update([
                'status' => PaymentStatus::FAILED,
                'notes' => 'Failed to initialize PayU payment: ' . $e->getMessage(),
            ]);
            throw $e;
        }
    }

    public function initializeReturnPayment(Order $order, ?Payment $payment, $amount, array $data = []): Payment
    {
        $returnPayment = $this->createPayment($order, $amount, true);

        try {
            $response = $this->createPayURefund($order, $payment, $amount);
            $returnPayment->update([
                'gateway_transaction_id' => $response->getResponse()->orderId ?? null,
                'gateway_data' => $response->getResponse(),
            ]);
            return $returnPayment;
        } catch (\Exception $e) {
            $returnPayment->update([
                'status' => PaymentStatus::FAILED,
                'notes' => 'Failed to initialize PayU payment: ' . $e->getMessage(),
            ]);
            throw $e;
        }
    }

    public function requiresRedirect(): bool
    {
        return true;
    }

    public function getPaymentUrl(Payment $payment): ?string
    {
        $gatewayData = $payment->gateway_data;
        return $gatewayData['redirectUri'] ?? null;
    }

    public function checkPaymentStatus(Payment $payment): PaymentStatus
    {
        $this->initializePayu();
        $payUOrderId = $payment->gateway_data['orderId'] ?? null;
        if (!$payUOrderId) {
            return $payment->status;
        }
        $response = OpenPayU_Order::retrieve($payUOrderId);

        if ($response->getStatus() !== OpenPayU_Order::SUCCESS) {
            throw new \Exception('PayU API error: ' . $response->body());
        }
        if ($response->getStatus() === 'SUCCESS') {
            $orderData = $response->getResponse();
            return $this->mapPayUStatusToPaymentStatus($orderData->orders[0]->status ?? null);
        }
        return $payment->status;
    }

    public function getName(): string
    {
        return 'PayU';
    }

    public function getKey(): string
    {
        return 'payu';
    }

    public function isAvailable(): bool
    {
        return parent::isAvailable() && !empty($this->config['pos_id']) &&
            !empty($this->config['signature_key']) &&
            !empty($this->config['oauth_client_id']);
    }

    protected function getDefaultConfig(): array
    {
        return [
            'env' => config('payment.payu.env'),
            'pos_id' => config('payment.payu.pos_id'),
            'signature_key' => config('payment.payu.signature_key'),
            'oauth_client_id' => config('payment.payu.oauth_client_id'),
            'oauth_client_secret' => config('payment.payu.oauth_client_secret'),
            'api_url' => config('payment.payu.api_url'),
            'notify_url' => route('payu.notify'),
        ];
    }

    private function createPayURefund(Order $order, ?Payment $payment, $amount): ?\OpenPayU_Result
    {
        $this->initializePayu();
        return OpenPayU_Refund::create(
            $payment->gateway_transaction_id,
            "Zamówienie #{$order->order_number} - zwrot",
            intval($amount * 100)
        );
    }

    private function initializePayu(): void
    {
        OpenPayU_Configuration::setEnvironment($this->config['env']);
        OpenPayU_Configuration::setMerchantPosId($this->config['pos_id']);
        OpenPayU_Configuration::setSignatureKey($this->config['signature_key']);
        OpenPayU_Configuration::setOauthClientId($this->config['oauth_client_id']);
        OpenPayU_Configuration::setOauthClientSecret($this->config['oauth_client_secret']);
    }

    private function createPayUOrder(Order $order, Payment $payment, $data): object
    {
        $this->initializePayu();

        $orderData = [
            'notifyUrl' => $this->config['notify_url'],
            'continueUrl' => $data['continueUrl'] ?? route('checkout.success', $order),
            'customerIp' => request()->ip(),
            'merchantPosId' => $this->config['pos_id'],
            'description' => 'Order ' . $order->order_number,
            'currencyCode' => 'PLN',
            'totalAmount' => intval($order->total_amount * 100),
            'extOrderId' => $payment->transaction_id,
            'products' => $this->buildPayUProducts($order),
            'buyer' => $this->buildPayUBuyer($order),
        ];

        $response = OpenPayU_Order::create($orderData);

        if ($response->getStatus() !== OpenPayU_Order::SUCCESS) {
            throw new \Exception('PayU API error: ' . $response->body());
        }

        return $response->getResponse();
    }

    private function buildPayUProducts(Order $order): array
    {
        return $order->items->map(function ($item) {
            return [
                'name' => $item->name,
                'unitPrice' => intval($item->price * 100),
                'quantity' => $item->quantity,
            ];
        })->toArray();
    }

    private function buildPayUBuyer(Order $order): array
    {
        $billingAddress = $order->billingAddress;

        return [
            'email' => $billingAddress->email,
            'firstName' => $billingAddress->first_name ?? $order->user->first_name,
            'lastName' => $billingAddress->last_name ?? $order->user->last_name,
            'phone' => $billingAddress->phone ?? '',
        ];
    }

    private function mapPayUStatusToPaymentStatus(string $payuStatus): PaymentStatus
    {
        return match ($payuStatus) {
            'WAITING_FOR_CONFIRMATION' => PaymentStatus::PROCESSING,
            'COMPLETED' => PaymentStatus::COMPLETED,
            'CANCELED' => PaymentStatus::CANCELLED,
            'REJECTED' => PaymentStatus::FAILED,
            default => PaymentStatus::PENDING,
        };
    }

    public function getPrice(): float
    {
        return 0;
    }
}