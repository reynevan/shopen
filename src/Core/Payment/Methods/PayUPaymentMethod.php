<?php

namespace Shopen\Core\Payment\Methods;

use Illuminate\Support\Facades\Log;
use Shopen\Models\Order\Order;
use Shopen\Models\Order\Payment;

class PayUPaymentMethod extends AbstractPaymentMethod
{
    public function initializePayment(Order $order, array $data = []): Payment
    {
        $payment = $this->createPayment($order, Payment::STATUS_PENDING);

        try {
            $response = $this->createPayUOrder($order, $payment);

            $payment->update([
                'gateway_transaction_id' => $response['orderId'] ?? null,
                'gateway_data' => $response,
            ]);

            return $payment;
        } catch (\Exception $e) {
            $payment->update([
                'status' => Payment::STATUS_FAILED,
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

    public function handleWebhook(array $webhookData): ?Payment
    {
        if (!isset($webhookData['order']['orderId'])) {
            return null;
        }

        $payment = Payment::where('gateway_transaction_id', $webhookData['order']['orderId'])->first();

        if (!$payment) {
            Log::warning('PayU webhook: Payment not found', $webhookData);
            return null;
        }

        $newStatus = $this->mapPayUStatusToPaymentStatus($webhookData['order']['status']);

        $payment->update([
            'status' => $newStatus,
            'gateway_data' => array_merge($payment->gateway_data ?? [], $webhookData),
            'processed_at' => $newStatus === Payment::STATUS_COMPLETED ? now() : $payment->processed_at,
        ]);

        return $payment;
    }

    public function checkPaymentStatus(Payment $payment): string
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->getAccessToken(),
                'Content-Type' => 'application/json',
            ])->get($this->config['api_url'] . '/orders/' . $payment->gateway_transaction_id);

            if ($response->successful()) {
                $orderData = $response->json();
                return $this->mapPayUStatusToPaymentStatus($orderData['orders'][0]['status']);
            }
        } catch (\Exception $e) {
            Log::error('PayU status check failed', ['payment_id' => $payment->id, 'error' => $e->getMessage()]);
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
            'pos_id' => config('payment.payu.pos_id'),
            'signature_key' => config('payment.payu.signature_key'),
            'oauth_client_id' => config('payment.payu.oauth_client_id'),
            'oauth_client_secret' => config('payment.payu.oauth_client_secret'),
            'api_url' => config('payment.payu.api_url', 'https://secure.snd.payu.com/api/v2_1'),
            'continue_url' => config('payment.payu.continue_url'),
            'notify_url' => config('payment.payu.notify_url'),
        ];
    }

    private function createPayUOrder(Order $order, Payment $payment): array
    {
        $orderData = [
            'notifyUrl' => $this->config['notify_url'],
            'continueUrl' => $this->config['continue_url'],
            'customerIp' => request()->ip(),
            'merchantPosId' => $this->config['pos_id'],
            'description' => 'Order ' . $order->order_number,
            'currencyCode' => 'PLN',
            'totalAmount' => intval($order->total_amount * 100), // PayU expects amount in grosze
            'extOrderId' => $payment->transaction_id,
            'products' => $this->buildPayUProducts($order),
            'buyer' => $this->buildPayUBuyer($order),
        ];

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->getAccessToken(),
            'Content-Type' => 'application/json',
        ])->post($this->config['api_url'] . '/orders', $orderData);

        if (!$response->successful()) {
            throw new \Exception('PayU API error: ' . $response->body());
        }

        return $response->json();
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
        $billingAddress = $order->billingAddress();

        return [
            'email' => $order->user->email,
            'firstName' => $billingAddress->first_name ?? $order->user->first_name,
            'lastName' => $billingAddress->last_name ?? $order->user->last_name,
            'phone' => $billingAddress->phone ?? '',
        ];
    }

    private function getAccessToken(): string
    {
        $response = Http::asForm()->post($this->config['api_url'] . '/oauth/authorize', [
            'grant_type' => 'client_credentials',
            'client_id' => $this->config['oauth_client_id'],
            'client_secret' => $this->config['oauth_client_secret'],
        ]);

        if (!$response->successful()) {
            throw new \Exception('Failed to get PayU access token');
        }

        return $response->json()['access_token'];
    }

    private function mapPayUStatusToPaymentStatus(string $payuStatus): string
    {
        return match ($payuStatus) {
            'NEW', 'PENDING' => Payment::STATUS_PENDING,
            'WAITING_FOR_CONFIRMATION' => Payment::STATUS_PROCESSING,
            'COMPLETED' => Payment::STATUS_COMPLETED,
            'CANCELED' => Payment::STATUS_CANCELLED,
            'REJECTED' => Payment::STATUS_FAILED,
            default => Payment::STATUS_PENDING,
        };
    }

    public function getPrice(): float
    {
        return 0;
    }
}