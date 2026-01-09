<?php

namespace Shopen\Http\Controllers\Admin\Order;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;
use Inertia\Response;
use Shopen\Core\Payment\PaymentMethodManager;
use Shopen\Enums\Order\OrderStatus;
use Shopen\Enums\Payment\PaymentStatus;
use Shopen\Http\Requests\Admin\Order\ShipRequest;
use Shopen\Http\Requests\Admin\Order\UpdateOrderStatusRequest;
use Shopen\Http\Resources\Admin\Order\OrderResource;
use Shopen\Mail\Order\OrderPlaced;
use Shopen\Mail\Order\OrderProcessing;
use Shopen\Mail\Order\OrderRefunded;
use Shopen\Mail\Order\OrderShipped;
use Shopen\Mail\Order\OrderVouchers;
use Shopen\Models\Order\Order;
use Shopen\Models\Order\Payment;
use Shopen\Repositories\Order\OrderRepository;
use Shopen\Mail\Order\OrderCancelled;
use Shopen\Mail\Order\OrderDelivered;
use Throwable;

readonly class OrderShowController
{
    public function __construct(
        protected OrderRepository $orderRepository,
        protected PaymentMethodManager $paymentMethodManager
    )
    {}

    public function show(Order $order): Response
    {
        $order->load([
            'shippingAddress',
            'billingAddress',
            'payments' => fn ($q) => $q->orderBy('created_at', 'asc'),
            'items.product',
            'items.product.promoCode',
            'items.promoCodeCoupons',
            'statusHistoryItems',
            'promoCodeCoupon.promoCode',
            'invoices'
        ]);

        return Inertia::render('Admin/Order/Show', [
            'order' => OrderResource::make($order),
            'orderStatusOptions' => OrderStatus::options(),
            'paymentStatusOptions' => PaymentStatus::options(),
        ]);
    }

    public function updateStatus(UpdateOrderStatusRequest $request, Order $order): RedirectResponse
    {
        $data = $request->validated();
        $order->status = $data['status'];
        $order->save();
        $order->statusHistoryItems()->create($data);

        if (!$data['email_notification']) {
            return back();
        }

        $contactEmail = $order->getCustomerEmail();
        if (!$contactEmail) {
            return back();
        }
        switch ($order->status) {
            case OrderStatus::NEW:
                Mail::to($contactEmail)->queue(new OrderPlaced($order, $data['comment']));
                break;
            case OrderStatus::PROCESSING:
                Mail::to($contactEmail)->queue(new OrderProcessing($order, $data['comment']));
                break;
            case OrderStatus::SHIPPED:
                Mail::to($contactEmail)->queue(new OrderShipped($order, $data['comment']));
                break;
            case OrderStatus::DELIVERED:
                Mail::to($contactEmail)->queue(new OrderDelivered($order, $data['comment']));
                break;
            case OrderStatus::CANCELLED:
                Mail::to($contactEmail)->queue(new OrderCancelled($order, $data['comment']));
                break;
            case OrderStatus::REFUNDED:
                Mail::to($contactEmail)->queue(new OrderRefunded($order, $data['comment']));
                break;
        }

        return back();
    }

    public function updateShipping(ShipRequest $request, Order $order)
    {
        $data = $request->validated();
        $order->shipping_tracking_code = $data['shipping_tracking_code'] ?? null;
        $order->status = OrderStatus::SHIPPED;
        $order->shipped_at = Carbon::now();
        $order->save();

        return back();
    }

    public function updatePaymentStatus(Order $order, Payment $payment): RedirectResponse
    {
        if ($payment->order_id !== $order->id) {
            return back();
        }
        $payment->status = request('status');
        $payment->save();
        return back();
    }

    public function refreshPaymentStatus(Order $order, Payment $payment): RedirectResponse
    {
        if ($payment->order_id !== $order->id) {
            return back();
        }
        $method = $this->paymentMethodManager->get($payment->payment_method);
        if (!$method) {
            return back();
        }
        $payment->status = $method->checkPaymentStatus($payment);
        $payment->save();
        return back();
    }

    public function sendVouchersEmail(Order $order)
    {
        $contactEmail = $order->getCustomerEmail();
        if (!$contactEmail) {
            return back();
        }
        $vouchers = [];
        $order->load(['items.product.promoCode', 'items.promoCodeCoupons']);

        DB::beginTransaction();
        try {
            foreach ($order->items as $item) {
                if (!$item->product || !$item->product->promoCode || $item->promoCodeCoupons->isEmpty()) {
                    continue;
                }
                $voucher = [
                    'name' => $item->product->getCustomAttributeValue('name'),
                    'codes' => []
                ];
                foreach ($item->promoCodeCoupons as $coupon) {
                    $voucher['codes'][] = $coupon->code;
                }
                $vouchers[] = $voucher;
                $item->promo_code_coupon_email_sent = true;
                $item->save();
            }
            Mail::to($contactEmail)->queue(new OrderVouchers($order, $vouchers));
            DB::commit();
        } catch (Throwable $e) {
            Log::error($e);
            return back()->with('error', 'Wystąpił błąd.');
        }
        return back();
    }
}