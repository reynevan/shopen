<?php

namespace Shopen\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Shopen\Core\Payment\PaymentMethodManager;
use Shopen\Core\Shipping\ShippingMethodManager;
use Shopen\Enums\Address\AddressType;
use Shopen\Enums\Order\OrderStatus;
use Shopen\Enums\Promocode\ApplyType;
use Shopen\Mail\Order\OrderPlaced;
use Shopen\Mail\Order\OrderStatusChanged;
use Shopen\Models\Address;
use Shopen\Models\Cart\Cart;
use Shopen\Models\Cart\CartItem;
use Shopen\Models\Order\Order;
use Shopen\Models\Order\OrderAddress;
use Shopen\Models\Order\OrderItem;
use Shopen\Models\Product\Product;
use Shopen\Models\PromoCode\PromoCode;

readonly class OrderService
{
    public function __construct(
        private CartService $cartService,
        private ShippingMethodManager $shippingMethodManager,
        private PaymentMethodManager $paymentMethodManager
    ) {}

    public function createOrder(
        ?int $userId,
        bool $customBillingAddress = false,
        array $options = []
    ): Order {
        $cart = $this->cartService->getCart();

        if (!$cart || $cart->items()->count() === 0) {
            throw ValidationException::withMessages([
                'cart' => 'Koszyk jest pusty'
            ]);
        }

        return DB::transaction(function () use (
            $userId, $cart, $options, $customBillingAddress
        ) {
            $this->validateStock($cart);

            $coupon = $this->getPromoCodeCoupon($cart);
            if ($coupon) {
                $coupon->increment('usage_count');
            }

            $totals = $this->calculateTotals($cart, $coupon->promoCode ?? null);
            $order = Order::create([
                'user_id' => $userId,
                'promo_code_coupon_id' => $coupon->id ?? null,
                'order_number' => Order::generateOrderNumber(),
                'status' => OrderStatus::NEW,
                'shipping_method' => $cart->shipping_method,
                'delivery_point_code' => $cart->delivery_point['name'] ?? null,
                'payment_method' => $cart->payment_method,
                'subtotal' => $totals['subtotal'],
                'shipping_amount' => $totals['shipping_amount'],
                'payment_amount' => $totals['payment_amount'],
                'discount_amount' => $totals['discount_amount'],
                'promo_code_discount_amount' => $totals['promo_code_discount_amount'] ?? 0,
                'total_amount' => $totals['total_amount'],
                'tax_amount' => $totals['tax_amount'],
                'notes' => $options['notes'] ?? null,
                'uuid' => Str::uuid()
            ]);

            $this->createOrderItems($order, $cart, $coupon->promoCode ?? null);

            $this->createOrderAddresses($order, $customBillingAddress);

            $this->createOrderStatusItem($order);

            $this->updateStock($cart);

            $cart->delete();

            $this->sendOrderPlacedNotification($order);

            session(['guest_order_id' => $order->id]);

            return $order->load(['items', 'addresses']);
        });
    }

    public function updateOrderStatus(Order $order, OrderStatus $status): Order
    {
        $oldStatus = $order->status;

        $order->update([
            'status' => $status,
            'shipped_at' => $status === OrderStatus::SHIPPED ? now() : $order->shipped_at,
            'delivered_at' => $status === OrderStatus::DELIVERED ? now() : $order->delivered_at,
        ]);

        if ($status === OrderStatus::CANCELLED && $oldStatus !== OrderStatus::CANCELLED) {
            $this->restoreStock($order);
        }

        $this->sendOrderStatusChangedNotification($order);

        return $order;
    }

    public function cancelOrder(Order $order): Order
    {
        if (in_array($order->status, [OrderStatus::DELIVERED, OrderStatus::CANCELLED, OrderStatus::REFUNDED])) {
            throw ValidationException::withMessages([
                'order' => 'Nie można anulować zamówienia o statusie: ' . $order->statusLabel
            ]);
        }

        return $this->updateOrderStatus($order, OrderStatus::CANCELLED);
    }

    private function getPromoCodeCoupon(Cart $cart)
    {
        $coupon = $cart->promoCodeCoupon;
        if (!$coupon) {
            return null;
        }
        $promoCode = $coupon->promoCode;
        if ($promoCode && (!$promoCode->isValid() || !$promoCode->meetsMinimumOrderValue($cart->totalPrice())) || !$coupon->hasUsageLeft()) {
            throw ValidationException::withMessages([
                'promo_code' => "Nieprawidłowy kod promocyjny"
            ]);
        }
        return $coupon;
    }

    private function validateStock($cart): void
    {
        foreach ($cart->items as $item) {
            $product = Product::find($item->product_id);

            if (!$product) {
                throw ValidationException::withMessages([
                    'product' => "Produkt ID {$item->product_id} nie istnieje"
                ]);
            }

            if ($product->uses_stock && !$product->in_stock) {
                throw ValidationException::withMessages([
                    'stock' => "Produkt {$product->name } jest niedostępny"
                ]);
            }

            if ($product->uses_stock && $product->stock_qty < $item->quantity) {
                throw ValidationException::withMessages([
                    'stock' => "Niewystarczająca ilość produktu {$product->sku}. Dostępne: {$product->stock_qty}, zamówione: {$item->quantity}"
                ]);
            }
        }
    }

    /**
     * @throws ValidationException
     */
    private function calculateTotals($cart, ?PromoCode $promoCode = null): array
    {
        $subtotal = $cart->items->sum(fn($item) => $item->final_price * $item->quantity);

        $shippingAmount = 0;
        $shippingMethod = $this->shippingMethodManager->get($cart->shipping_method);
        if ($shippingMethod) {
            $shippingAmount = $shippingMethod->getPrice();
        }

        $paymentAmount = 0;
        $paymentMethod = $this->paymentMethodManager->get($cart->payment_method);
        if ($paymentMethod) {
            $paymentAmount = $paymentMethod->getPrice();
        }

        $discountAmount = $cart->items->sum(fn($item) => ($item->price - $item->final_price) * $item->quantity);
        $promoCodeDiscountAmount = 0;


        if ($promoCode) {
            if ($promoCode->minimum_order_value > $subtotal) {
                throw ValidationException::withMessages([
                    'promo_code' => "Minimalna wartość zamówienia dla tego kodu to: {$promoCode->minimum_order_value} zł"
                ]);
            }

            if ($promoCode->for_logged_users_only && !$cart->user_id) {
                throw ValidationException::withMessages([
                    'promo_code' => "Ten kod jest dostępny tylko dla zalogowanych użytkowników."
                ]);
            }

            $promoCodeDiscountAmount = $promoCode->getCartDiscount($cart);
        }

        return [
            'subtotal' => $subtotal,
            'shipping_amount' => $shippingAmount,
            'payment_amount' => $paymentAmount,
            'discount_amount' => $discountAmount,
            'promo_code_discount_amount' => $promoCodeDiscountAmount,
            'total_amount' => $subtotal + $shippingAmount + $paymentAmount - $promoCodeDiscountAmount,
            'tax_amount' => $this->calculateTotalTaxes($cart, $shippingAmount, $promoCode),
        ];
    }

    private function calculateTotalTaxes(Cart $cart, $shippingAmount, ?PromoCode $promoCode = null): float
    {
        $tax = .0;
        $itemsTotalValue = $cart->totalPrice();
        foreach ($cart->items as $item) {
            $product = $item->product;
            $promoCodeDiscountAmount = 0;
            if ($promoCode && $promoCode->applies_to === ApplyType::PER_ITEM && $promoCode->isAppliedToProduct($product)) {
                $promoCodeDiscountAmount = $item->quantity * $promoCode->calculateDiscount($item->final_price);
            } elseif ($promoCode && $promoCode->applies_to === ApplyType::CART) {
                $promoCodeDiscountAmount = round($promoCode->discount_value * $item->final_price * $item->quantity / $itemsTotalValue, 2);
            }
            $total = $item->final_price * $item->quantity - $promoCodeDiscountAmount;
            $tax += $product->taxClass ? $total * $product->taxClass->rate / 100 : 0;
        }
        if ($shippingAmount > 0) {
            $tax += $shippingAmount * config('shopen.tax.shipping.rate', 23) / 100;
        }
        return $tax;
    }


    private function createOrderItems(Order $order, $cart, ?PromoCode $promoCode): void
    {
        $itemsTotalValue = $cart->totalPrice();
        foreach ($cart->items as $item) {
            $product = Product::find($item->product_id);
            $finalPrice = $item->final_price;

            $promoCodeDiscountAmount = 0;
            $promoCodeDiscountAmountTotal = 0;
            if ($promoCode && $promoCode->applies_to === ApplyType::PER_ITEM && $promoCode->isAppliedToProduct($product)) {
                $promoCodeDiscountAmount = $promoCode->calculateDiscount($finalPrice);
                $promoCodeDiscountAmountTotal = round($item->quantity * $promoCodeDiscountAmount, 2);
            } elseif ($promoCode && $promoCode->applies_to === ApplyType::CART) {
                $promoCodeDiscountAmount = round($promoCode->discount_value * $item->final_price / $itemsTotalValue, 2);
                $promoCodeDiscountAmountTotal = round($promoCodeDiscountAmount * $item->quantity, 2);
            }
            $total = $item->final_price * $item->quantity - $promoCodeDiscountAmountTotal;

            $tax = $product->taxClass
                ? $total * ($product->taxClass->rate / (100 + $product->taxClass->rate))
                : 0;

            $orderItem = OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item->product_id,
                'sku' => $product->sku,
                'name' => $product->name,
                'quantity' => $item->quantity,
                'price' => $item->price,
                'final_price' => $finalPrice,
                'promo_code_discount_amount' => $promoCodeDiscountAmount,
                'total' => $total,
                'tax_amount' => $tax,
                'tax_rate' => $product->taxClass->rate,
                'unit' => $product->unit ?? config('shopen.product.default_unit')
            ]);

            if ($product->is_voucher && $product->promoCode) {
                for ($i = 0; $i < $item->quantity; $i++) {
                    $promoCodeCoupon = $product->promoCode->createCoupon();
                    $orderItem->promoCodeCoupons()->attach($promoCodeCoupon->id);
                }
            }
        }
    }

    private function calculateOrderItemTax(CartItem $cartItem, ?PromoCode $promoCode): float
    {

    }

    private function createOrderStatusItem(Order $order): void
    {
        $order->statusHistoryItems()->create(['status' => OrderStatus::NEW]);
    }

    private function createOrderAddresses(Order $order, $customBillingAddress): void
    {
        $cart = $this->cartService->getCart();
        if ($customBillingAddress && !$cart->billingAddress) {
            throw ValidationException::withMessages([
                'billing_address' => "Podaj dane do płatności."
            ]);
        }
        $this->createOrderAddress($order, AddressType::SHIPPING, $cart->shippingAddress);

        $this->createOrderAddress($order, AddressType::BILLING, $customBillingAddress ? $cart->billingAddress : $cart->shippingAddress);
    }

    private function createOrderAddress(Order $order, AddressType $type, Address $address): void
    {
        OrderAddress::create([
            'order_id' => $order->id,
            'type' => $type,
            'first_name' => $address->first_name,
            'last_name' => $address->last_name,
            'company' => $address->company,
            'company_nip' => $address->company_nip,
            'address_line' => $address->address_line,
            'city' => $address->city,
            'postal_code' => $address->postal_code,
            'country' => $address->country ?? 'Polska',
            'phone' => $address->phone,
            'email' => $address->email ?? $order->user?->email ?? null,
        ]);
    }

    private function updateStock($cart): void
    {
        foreach ($cart->items as $item) {
            $product = Product::find($item->product_id);

            if ($product && $product->uses_stock) {
                $newQty = $product->stock_qty - $item->quantity;
                $product->update([
                    'stock_qty' => max(0, $newQty),
                    'in_stock' => $newQty > 0,
                ]);
            } else {
                $product->searchable();
            }
        }
    }

    private function restoreStock(Order $order): void
    {
        foreach ($order->items as $item) {
            $product = Product::find($item->product_id);

            if ($product && $product->uses_stock) {
                $newQty = $product->stock_qty + $item->quantity;
                $product->update([
                    'stock_qty' => $newQty,
                    'in_stock' => true,
                ]);
            }
        }
    }


    private function sendOrderPlacedNotification(Order $order): void
    {
        try {
            $email = $order->getCustomerEmail();
            if ($email) {
                Mail::to($email)->queue(new OrderPlaced($order));
            }
        } catch (\Exception $e) {
            logger()->error('Failed to send order placed email', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    private function sendOrderStatusChangedNotification(Order $order): void
    {
        try {
            $email = $order->getCustomerEmail();
            if ($email) {
                Mail::to($email)->queue(new OrderStatusChanged($order));
            }
        } catch (\Exception $e) {
            logger()->error('Failed to send order status changed email', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);
        }
    }
}