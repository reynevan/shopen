<?php

namespace Shopen\Http\Controllers\Frontend\Checkout;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Inertia\Inertia;
use Shopen\Core\Payment\PaymentMethodManager;
use Shopen\Services\CartService;
use Shopen\Services\OrderService;

readonly class CheckoutOrderController
{
    public function __construct(
        protected OrderService $orderService,
        protected CartService $cartService,
        protected PaymentMethodManager $paymentMethodManager,
    )
    {

    }
    public function placeOrder()
    {
        $data = request()->post();

        $errors = [];
        $cart = $this->cartService->getCart();
        if (!$cart->shipping_method) {
            $errors['shippingMethod'] = 'Wybierz metodę dostawy';
        }
        if (!$cart->payment_method) {
            $errors['paymentMethod'] = 'Wybierz metodę płatności';
        }
        if (!$cart->shippingAddress) {
            $errors['shippingAddress'] = 'Uzupełnij adres dostawy';
        }
        if ($data['customBillingAddress'] && !$cart->billingAddress) {
            $errors['billingAddress'] = 'Uzupełnij dane do płatności';
        }
        if (($promoCode = $cart->promoCode) && !$promoCode->isValid()){
            $errors['code'] = 'Nieprawidłowy kod promocyjny';
        }

        if (count($errors)) {
            return back()->withErrors($errors);
        }

        $order = $this->orderService->createOrder(
            Auth::id(),
            $data['customBillingAddress'],
            [
                'notes' => $data['notes'] ?? null
            ]
        );

        $paymentMethod = $this->paymentMethodManager->get($order->payment_method);

        $payment = $paymentMethod->initializePayment($order);

        if ($paymentMethod->requiresRedirect()) {
            return Inertia::location($paymentMethod->getPaymentUrl($payment));
        }

        return redirect(route('checkout.success', $order->uuid));

    }
}