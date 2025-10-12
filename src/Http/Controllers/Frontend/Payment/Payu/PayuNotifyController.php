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

            if (!$this->verifySignature($body, $headers)) {
                Log::warning('PayU: Invalid signature', ['body' => $body, 'headers' => $headers]);
                return response('Invalid signature', 400);
            }

            $data = json_decode($body, true);

            if (!isset($data['order']['orderId'])) {
                Log::error('PayU: Missing orderId', ['data' => $data]);
                return response('Missing orderId', 400);
            }

            $orderId = $data['order']['orderId'];
            $status = $data['order']['status'];

            $payment = Payment::query()->where('gateway_transaction_id', $orderId)->first();

            if (!$payment) {
                Log::error('PayU: Payment not found', ['gateway_transaction_id' => $orderId]);
                return response('Payment not found', 404);
            }

            $this->updatePaymentStatus($payment, $status);

            Log::info('PayU: Notification processed', [
                'gateway_transaction_id' => $orderId,
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
        $incomingSignature = $this->extractSignature($headers['x-openpayu-signature'][0] ?? null);

        if (!$incomingSignature) {
            return false;
        }

        $secondKey = config('payment.payu.signature_key');
        $calculatedSignature = md5($body . $secondKey);

        return hash_equals($calculatedSignature, $incomingSignature);
    }

    private function extractSignature(string $header): ?string
    {
        parse_str(str_replace(';', '&', $header), $parts);

        return $parts['signature'] ?? null;
    }

    private function updatePaymentStatus($payment, string $status): void
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
        $payment->save();
    }
}