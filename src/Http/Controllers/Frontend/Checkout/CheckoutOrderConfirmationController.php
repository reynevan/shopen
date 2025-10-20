<?php

namespace Shopen\Http\Controllers\Frontend\Checkout;

use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Shopen\Http\Resources\Order\OrderResource;
use Shopen\Models\Order\Order;

readonly class CheckoutOrderConfirmationController
{
    public function index(Order $order)
    {
        if (session('guest_order_id') !== $order->id) {
            if (!Auth::check() || Auth::id() !== $order->user_id) {
                return redirect('/');
            }
        }
        $order->load('items.product');
        return Inertia::render('Frontend/Checkout/Confirmation', [
            'order' => OrderResource::make($order),
        ]);
    }
}