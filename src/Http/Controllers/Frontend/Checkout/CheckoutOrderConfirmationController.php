<?php

namespace Shopen\Http\Controllers\Frontend\Checkout;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Number;
use Inertia\Inertia;
use Inertia\Response;
use Shopen\Core\Payment\PaymentMethodManager;
use Shopen\Core\Shipping\ShippingMethodManager;
use Shopen\Http\Resources\User\AddressResource;
use Shopen\Models\Address;
use Shopen\Models\Order\Order;
use Shopen\Services\CartService;

readonly class CheckoutOrderConfirmationController
{
    public function index(Order $order)
    {
        if (session('guest_order_id') !== $order->id) {
            if (!Auth::check() || Auth::id() !== $order->user_id) {
                return redirect('/');
            }
        }
        return Inertia::render('Frontend/Checkout/Confirmation', [
            'order' => $order,
        ]);
    }
}