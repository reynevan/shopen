<?php

namespace Shopen\Http\Controllers\Frontend\Payment\Payu;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use OpenPayU_Order;
use OpenPayU_Exception;
use Shopen\Models\Order\Payment;

class PayuNotifyController
{
    public function index(Request $request): Response
    {
        try {
            $body = $request->getContent();
            $headers = $request->headers->all();

            // Weryfikacja sygnatury PayU
            if (!$this->verifySignature($body, $headers)) {
                Log::warning('PayU: Invalid signature', ['body' => $body]);
                return response('Invalid signature', 400);
            }

            $data = json_decode($body, true);

            if (!isset($data['order']['orderId'])) {
                Log::error('PayU: Missing orderId', ['data' => $data]);
                return response('Missing orderId', 400);
            }

            // Pobierz szczegóły zamówienia z PayU
            $response = OpenPayU_Order::retrieve($data['order']['orderId']);

            if ($response->getStatus() !== 'SUCCESS') {
                Log::error('PayU: Failed to retrieve order', ['orderId' => $data['order']['orderId']]);
                return response('Failed to retrieve order', 500);
            }

            $order = $response->getResponse()->orders[0];
            $extOrderId = $order->extOrderId; // Twoje ID zamówienia
            $status = $order->status;

            // Znajdź zamówienie w bazie
            $payment = Payment::query()->where('gateway_transaction_id', $extOrderId)->first();

            if (!$payment) {
                Log::error('PayU: Payment not found', ['extOrderId' => $extOrderId]);
                return response('Payment not found', 404);
            }

            // Aktualizuj status płatności
            $this->updatePaymentStatus($payment, $status, $order);

            Log::info('PayU: Notification processed', [
                'extOrderId' => $extOrderId,
                'status' => $status
            ]);

            return response('OK', 200);

        } catch (OpenPayU_Exception $e) {
            Log::error('PayU Exception: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response('PayU error', 500);
        } catch (\Exception $e) {
            Log::error('PayU Notify Exception: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response('Server error', 500);
        }
    }

    private function verifySignature(string $body, array $headers): bool
    {
        $signature = $headers['openpayu-signature'][0] ?? null;

        if (!$signature) {
            return false;
        }

        parse_str($signature, $signatureData);
        $incomingSignature = $signatureData['signature'] ?? null;

        if (!$incomingSignature) {
            return false;
        }

        $secondKey = config('payment.payu.signature_key');
        $calculatedSignature = hash('sha256', $body . $secondKey);

        return hash_equals($calculatedSignature, $incomingSignature);
    }

    private function updatePaymentStatus($payment, string $status, $order): void
    {
        switch ($status) {
            case 'COMPLETED':
                $payment->status = Payment::STATUS_COMPLETED;
                $payment->processed_at = now();
                break;

            case 'PENDING':
                $payment->status = Payment::STATUS_PENDING;
                break;

            case 'WAITING_FOR_CONFIRMATION':
                $payment->status = Payment::STATUS_PENDING;
                break;

            case 'CANCELED':
                $payment->status = Payment::STATUS_CANCELLED;
                break;

            default:
                $payment->status = Payment::STATUS_FAILED;
                break;
        }

        $payment->payu_order_id = $order->orderId;
        $payment->save();
    }
}