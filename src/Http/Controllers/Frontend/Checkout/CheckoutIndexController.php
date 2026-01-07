<?php

namespace Shopen\Http\Controllers\Frontend\Checkout;

use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Number;
use Inertia\Inertia;
use Inertia\Response;
use Shopen\Core\Payment\PaymentMethodManager;
use Shopen\Core\Shipping\ShippingMethodManager;
use Shopen\Http\Resources\Cart\CartResource;
use Shopen\Http\Resources\User\AddressResource;
use Shopen\Models\Address;
use Shopen\Services\CartService;
use Shopen\Services\ShippingService;

readonly class CheckoutIndexController
{
    public function __construct(
        private ShippingMethodManager $shippingMethodManager,
        private ShippingService $shippingService,
        private CartService           $cartService,
        private PaymentMethodManager  $paymentMethodManager,
    )
    {}

    public function index(): Response|Redirector|RedirectResponse
    {
        $cart = $this->cartService->getCart();
        if ($cart->isEmpty()) {
            return redirect('/');
        }

        return Inertia::render('Frontend/Checkout/Index', [
            'cart' => CartResource::make($this->cartService->getCart()),
            'selectedShippingMethod' => fn () => $cart->shipping_method,
            'selectedPaymentMethod' => fn () => $cart->payment_method,
            'deliveryPoint' => fn () => $cart->delivery_point,
            'shippingMethods' => fn () => $this->shippingService->getAvailableShippingMethods(),
            'paymentMethods' => fn () => $this->paymentMethodManager->getPaymentMethods(),
            'summary' => fn() => $this->summary(),
            'addresses' => fn() => $this->getAddresses(),
            'selectedShippingAddress' => fn() => $cart->shippingAddress->address_id ?? null,
            'selectedBillingAddress' => fn() => $cart->billingAddress->address_id ?? null,
            'promoCode' => fn() => $cart->promoCodeCoupon?->code,
            'notesEnabled' => config('shopen.checkout.notes.enabled'),
            'includeGeoWidget' => config('shipping.paczkomaty.active') && config('shipping.paczkomaty.geo_token'),
            'geoWidgetToken' => config('shipping.paczkomaty.geo_token')
        ]);
    }

    protected function getAddresses(): array
    {
        return [
            'shipping' => $this->getShippingAddresses(),
            'billing' => $this->getBillingAddresses(),
        ];
    }

    protected function getShippingAddresses()
    {
        $shippingAddress = $this->cartService->getCart()->shippingAddress;
        if (Auth::check()) {
            return Auth::user()->shippingAddresses->map(function (Address $address) use ($shippingAddress) {
                $address->is_selected = $shippingAddress && $address->id === $shippingAddress->address_id;
                return AddressResource::make($address);
            });
        }
        if ($shippingAddress) {
            $shippingAddress->id = null;
        }
        return $shippingAddress ? [AddressResource::make($shippingAddress)] : [];
    }

    protected function getBillingAddresses()
    {
        $billingAddress = $this->cartService->getCart()->billingAddress;
        if (Auth::check()) {
            return Auth::user()->billingAddresses->map(function (Address $address) use ($billingAddress) {
                $address->is_selected = $billingAddress && $address->id === $billingAddress->address_id;
                return AddressResource::make($address);
            });
        }
        if ($billingAddress) {
            $billingAddress->id = null;
        }
        return $billingAddress ? [AddressResource::make($billingAddress)] : [];
    }

    protected function summary(): array
    {
        $cart = $this->cartService->getCart();
        $productsTotal = $cart->totalPrice();
        $productsSubtotal = $cart->subtotalPrice();
        $shippingAmount = 0;
        if ($shippingCode = $this->cartService->getCart()->shipping_method) {
            $method = $this->shippingMethodManager->get($shippingCode);
            if ($method) {
                $shippingAmount = $method->getPrice();
            }
        }
        $paymentAmount = 0;
        if ($paymentCode = $this->cartService->getCart()->payment_method) {
            $method = $this->paymentMethodManager->get($paymentCode);
            if ($method) {
                $paymentAmount = $method->getPrice();
            }
        }
        $discount = 0;
        $coupon = $this->cartService->getCart()->promoCodeCoupon;
        if ($coupon && $coupon->hasUsageLeft()) {
            if ($code = $coupon->promoCode) {
                if ($code->isValid()) {
                    $discount = $code->getCartDiscount($cart);
                } else {
                    $this->cartService->setPromoCodeCoupon(null);
                }
            }
        }
        $total = $productsTotal + $shippingAmount + $paymentAmount - $discount;

        return
            [
                'productsSubtotal' => Number::currency($productsSubtotal),
                'productsTotal' => Number::currency($productsTotal),
                'shipping' => Number::currency($shippingAmount),
                'payment' => Number::currency($paymentAmount),
                'hasDiscount' => $discount > 0,
                'discount' => Number::currency($discount),
                'total' => Number::currency($total),
                'total_raw' => $total
            ];
    }
}