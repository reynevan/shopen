<?php

namespace Shopen\Http\Controllers\Frontend\Checkout;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Number;
use Inertia\Inertia;
use Inertia\Response;
use Shopen\Core\Payment\PaymentMethodManager;
use Shopen\Core\Shipping\ShippingMethodManager;
use Shopen\Http\Resources\User\AddressResource;
use Shopen\Models\Address;
use Shopen\Services\CartService;

readonly class CheckoutIndexController
{
    public function __construct(
        private ShippingMethodManager $shippingMethodManager,
        private CartService           $cartService,
        private PaymentMethodManager  $paymentMethodManager,
    )
    {}

    public function index(): Response
    {
        return Inertia::render('Frontend/Checkout/Index', [
            'selectedShippingMethod' => fn () => $this->cartService->getCart()->shipping_method,
            'selectedPaymentMethod' => fn () => $this->cartService->getCart()->payment_method,
            'deliveryPoint' => fn () => $this->cartService->getCart()->delivery_point,
            'shippingMethods' => fn () => $this->shippingMethodManager->getShippingMethods(),
            'paymentMethods' => fn () => $this->paymentMethodManager->getPaymentMethods(),
            'summary' => fn() => $this->summary(),
            'addresses' => fn() => $this->getAddresses(),
            'selectedShippingAddress' => fn() => $this->cartService->getCart()?->shippingAddress->address_id ?? null,
            'selectedBillingAddress' => fn() => $this->cartService->getCart()?->billingAddress->address_id ?? null,
            'promoCode' => fn() => $this->cartService->getCart()->promoCode?->code
        ]);
    }

    protected function getAddresses()
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
        $shipping = 0;
        if ($shippingCode = $this->cartService->getCart()->shipping_method) {
            $method = $this->shippingMethodManager->get($shippingCode);
            if ($method) {
                $shipping = $method->getPrice();
            }
        }
        $discount = 0;
        if ($code = $this->cartService->getCart()->promoCode) {
            if ($code->isValid()) {
                $discount = $code->getCartDiscount($cart);
            } else {
                $this->cartService->setPromoCode(null);
            }
        }
        $total = $productsTotal + $shipping - $discount;

        return
            [
                'productsSubtotal' => Number::currency($productsSubtotal),
                'productsTotal' => Number::currency($productsTotal),
                'shipping' => Number::currency($shipping),
                'hasDiscount' => $discount > 0,
                'discount' => Number::currency($discount),
                'total' => Number::currency($total),
            ];
    }
}