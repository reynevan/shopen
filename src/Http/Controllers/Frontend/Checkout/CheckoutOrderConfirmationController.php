<?php

namespace Shopen\Http\Controllers\Frontend\Checkout;

use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Shopen\Core\Payment\PaymentMethodManager;
use Shopen\Http\Resources\Order\OrderResource;
use Shopen\Models\Order\Order;

readonly class CheckoutOrderConfirmationController
{
    public function __construct(private PaymentMethodManager $paymentMethodManager)
    {
    }

    public function index(Order $order)
    {
        if (session('guest_order_id') !== $order->id) {
            if (!Auth::check() || Auth::id() !== $order->user_id) {
                return redirect('/');
            }
        }
        $order->load(['items.product', 'billingAddress', 'shippingAddress']);
        return Inertia::render('Frontend/Checkout/Confirmation', [
            'order' => OrderResource::make($order),
            'payment_method' => $this->paymentMethodManager->get($order->payment_method)
        ]);
    }
}