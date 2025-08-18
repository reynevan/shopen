<?php

namespace Shopen\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Log;
use Shopen\Enums\Address\AddressType;
use Shopen\Models\Address;
use Shopen\Models\Cart\Cart;
use Shopen\Models\Cart\CartAddress;
use Shopen\Models\PromoCode;

class CartService
{
    protected const COOKIE_NAME = 'cart_uuid';
    protected const COOKIE_LIFETIME = 60 * 24 * 30;

    protected ?Cart $cart = null;

    public function __construct(protected Request $request)
    {
        $this->initializeCart();
    }

    protected function initializeCart(): void
    {
        $user = Auth::user();
        if ($user) {
            $this->cart = Cart::query()
                ->with(['items', 'items.product', 'items.product.urlRewrite'])
                ->where('user_id', $user->id)
                ->first();
            $this->mergeGuestCartIfExists();
        } else {
            $this->setGuestCartIfExists();
        }
    }

    protected function setGuestCartIfExists(): void
    {
        $uuid = $this->request->cookie(self::COOKIE_NAME);
        if ($uuid) {
            $this->cart = Cart::where('uuid', $uuid)->whereNull('user_id')->first();
        }
    }

    protected function createCart(): Cart
    {
        if ($this->cart) {
            return $this->cart;
        }
        $user = Auth::user();
        if ($user) {
            $this->cart = Cart::create([
                'user_id' => $user->id,
                'uuid' => null
            ]);
            $this->mergeGuestCartIfExists();
            $this->setDefaultAddress();
        } else {
            $this->cart = Cart::create();
            Cookie::queue(
                Cookie::make(
                    self::COOKIE_NAME,
                    $this->cart->uuid,
                    self::COOKIE_LIFETIME
                )
            );
        }
        return $this->cart;
    }

    protected function mergeGuestCartIfExists(): void
    {
        $uuid = $this->request->cookie(self::COOKIE_NAME);
        if (!$uuid) {
            return;
        }

        $guestCart = Cart::where('uuid', $uuid)->whereNull('user_id')->first();
        if (!$guestCart) {
            return;
        }
        if (!$guestCart->items) {
            $guestCart->delete();

            Cookie::queue(
                Cookie::forget(self::COOKIE_NAME)
            );
            return;
        }

        if (!$this->cart) {
            $this->cart = Cart::create(['user_id' => Auth::id(), 'uuid' => null]);
            $this->setDefaultAddress();
        }

        foreach ($guestCart->items as $item) {
            $this->cart->addProduct(
                $item->product_id,
                $item->quantity,
                $item->price,
                $item->final_price,
            );
        }

        $guestCart->delete();

        Cookie::queue(
            Cookie::forget(self::COOKIE_NAME)
        );
    }

    protected function setDefaultAddress(): void
    {
        if (!Auth::user()?->addresses) {
            return;
        }

        if (!$this->cart->shippingAddress && $userAddress = Auth::user()?->defaultShippingAddress()) {
            $this->setAddress($userAddress, AddressType::SHIPPING);
        }
    }

    public function getCart(): ?Cart
    {
        return $this->cart ?: new Cart();
    }

    public function hasCart(): bool
    {
        return $this->cart !== null;
    }

    public function addToCart(int $productId, int $quantity, float $price, float $finalPrice): void
    {
        $this->createCart();

        $this->cart->addProduct($productId, $quantity, $price, $finalPrice);
    }

    public function removeFromCart(int $itemId): void
    {
        $this->createCart();

        $this->cart->removeItem($itemId);
    }

    public function updateItem(int $itemId, int $quantity): void
    {
        $this->createCart();

        $this->cart->updateItemQty($itemId, $quantity);
    }

    public function setAddress(Address $address, AddressType $type): void
    {
        $this->createCart();

        if ($type === AddressType::SHIPPING && $this->cart->shippingAddress) {
            $this->cart->shippingAddress->delete();
        }

        if ($type === AddressType::BILLING && $this->cart->billingAddress) {
            $this->cart->billingAddress->delete();
        }

        CartAddress::create(
            [
                'address_id' => $address->id ?? null,
                'cart_id' => $this->cart->id,
                'type' => $type,
                'first_name' => $address->first_name,
                'last_name' => $address->last_name,
                'company' => $address->company,
                'company_nip' => $address->company_nip,
                'address_line' => $address->address_line,
                'city' => $address->city,
                'postal_code' => $address->postal_code,
                'country' => $address->country,
                'phone' => $address->phone,
                'email' => $address->email,
            ]
        );
    }

    public function setShippingMethod($shippingMethod, $deliveryPoint = null)
    {
        $this->createCart();

        $this->cart->shipping_method = $shippingMethod;
        $this->cart->delivery_point = $deliveryPoint;
        $this->cart->save();
    }

    public function setPaymentMethod($paymentMethod)
    {
        $this->createCart();

        $this->cart->payment_method = $paymentMethod;
        $this->cart->save();
    }

    public function setPromoCode(?PromoCode $code)
    {
        $this->createCart();

        $this->cart->promo_code_id = $code->id ?? null;
        $this->cart->save();
    }
}