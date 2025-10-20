<?php

namespace Shopen\Http\Controllers\Frontend\Checkout;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Number;
use Illuminate\Support\Str;
use Shopen\Core\Payment\PaymentMethodManager;
use Shopen\Core\Shipping\ShippingMethodManager;
use Shopen\Enums\Address\AddressType;
use Shopen\Http\Requests\Frontend\Checkout\UpdateAddressRequest;
use Shopen\Models\Cart\CartAddress;
use Shopen\Models\PromoCode\PromoCodeCoupon;
use Shopen\Services\CartService;

readonly class CheckoutUpdateController
{
    public function __construct(
        protected CartService $cartService,
        protected ShippingMethodManager $shippingMethodManager,
        protected PaymentMethodManager $paymentMethodManager,
    )
    {}

    public function selectShippingAddress(): RedirectResponse
    {
            $address = Auth::user()
                ->shippingAddresses()
                ->where('id', request('id'))
                ->first();
            if ($address) {
                $this->cartService->setAddress($address, AddressType::SHIPPING);
            }
            return back();
    }

    public function selectBillingAddress(): RedirectResponse
    {
            $address = Auth::user()
                ->billingAddresses()
                ->where('id', request('id'))
                ->first();
            if ($address) {
                $this->cartService->setAddress($address, AddressType::BILLING);
            }
            return back();
    }

    public function updateShippingAddress(UpdateAddressRequest $request): RedirectResponse
    {
        $data = $request->validated();
        if ($data['id'] ?? false) {
            $address = Auth::user()->shippingAddresses()->where('id', $data['id'])->first();
            if (!$address) {
                abort(403);
            }
            $address->update($data);
        } else {
            $address = new CartAddress($data);
        }
        $this->cartService->setAddress($address, AddressType::SHIPPING);
        return back();
    }

    public function updateBillingAddress(UpdateAddressRequest $request): RedirectResponse
    {
        $data = $request->validated();
        if ($data['id'] ?? false) {
            $address = Auth::user()->billingAddresses()->where('id', $data['id'])->first();
            if (!$address) {
                abort(403);
            }
            $address->update($data);
        } else {
            $address = new CartAddress($data);
        }
        $this->cartService->setAddress($address, AddressType::BILLING);
        return back();
    }

    public function updateShippingMethod(): RedirectResponse
    {
        if (!request('shippingMethod')) {
            return back();
        }
        $shippingMethod = $this->shippingMethodManager->get(request('shippingMethod'));
        if (!$shippingMethod) {
            return back();
        }
        $deliveryPoint = null;
        if (request('deliveryPoint')) {
            $deliveryPoint = [
                'name' => Str::substr(request('deliveryPoint')['name'] ?? '', 0, 20),
                'location_description' => Str::substr(request('deliveryPoint')['location_description'] ?? '', 0, 255),
                'address' => [
                    'line1' => Str::substr(request('deliveryPoint')['address']['line1'] ?? '', 0, 255),
                    'line2' => Str::substr(request('deliveryPoint')['address']['line2'] ?? '', 0, 255)
                ],
            ];

        }
        $this->cartService->setShippingMethod($shippingMethod->getKey(), $deliveryPoint);

        $paymentMethodCode = $this->cartService->getCart()->payment_method;
        if ($paymentMethodCode) {
            $paymentMethod = $this->paymentMethodManager->get($paymentMethodCode);
            if (!$paymentMethod->isAvailable()) {
                $this->cartService->setPaymentMethod(null);
            }
        }
        return back();
    }

    public function updatePaymentMethod(): RedirectResponse
    {
        if (!request('paymentMethod')) {
            return back();
        }
        $paymentMethod = $this->paymentMethodManager->get(request('paymentMethod'));
        if (!$paymentMethod) {
            return back();
        }
        $this->cartService->setPaymentMethod($paymentMethod->getKey());
        return back();
    }

    public function updatePromoCode(): RedirectResponse
    {
        $code = request('code');
        if (!$code) {
            $this->cartService->setPromoCodeCoupon(null);
            return back();
        }
        $code = PromoCodeCoupon::query()->where('code', $code)->first();
        if (!$code || !$code->hasUsageLeft()) {
            return back()->withErrors(['code' => 'Nieprawidłowy kod']);
        }
        $promoCode = $code->promoCode;
        if (!$promoCode || !$promoCode->isValid()) {
            return back()->withErrors(['code' => 'Nieprawidłowy kod']);
        }
        if (!$promoCode->getCartDiscount($this->cartService->getCart())) {
            return back()->withErrors(['code' => 'Brak produktów w koszyku spełniających warunki kodu promocyjnego']);
        }
        if (!$promoCode->meetsMinimumOrderValue($this->cartService->getCart()->totalPrice())) {
            return back()->withErrors(['code' => 'Minimalna kwota zamówienia dla tego kodu to ' . Number::currency($promoCode->minimum_order_value)]);
        }
        $this->cartService->setPromoCodeCoupon($code);
        return back();
    }
}