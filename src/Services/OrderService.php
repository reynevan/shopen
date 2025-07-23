<?php

namespace Shopen\Services;

use Shopen\Core\Payment\PaymentMethodManager;
use Shopen\Enums\Address\AddressType;
use Shopen\Enums\Order\OrderStatus;
use Shopen\Models\Cart\Cart;
use Shopen\Models\Order\Order;
use Shopen\Models\Order\OrderItem;
use Shopen\Models\Order\OrderAddress;
use Shopen\Models\Address;
use Shopen\Mail\OrderPlaced;
use Shopen\Mail\OrderStatusChanged;
use Shopen\Core\Shipping\ShippingMethodManager;
use Shopen\Models\Product\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use Shopen\Models\PromoCode;

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

            $promoCode = $this->getPromoCode($cart);

            if ($promoCode) {
                $promoCode->increment('current_usage_count');
            }

            $totals = $this->calculateTotals($cart, $promoCode);

            $order = Order::create([
                'user_id' => $userId,
                'promo_code_id' => $promoCode->id ?? null,
                'order_number' => Order::generateOrderNumber(),
                'status' => OrderStatus::NEW,
                'shipping_method' => $cart->shipping_method,
                'delivery_point_code' => $options['delivery_point_code'] ?? null,
                'payment_method' => $cart->payment_method,
                'subtotal' => $totals['subtotal'],
                'shipping_amount' => $totals['shipping_amount'],
                'payment_amount' => $totals['payment_amount'],
                'discount_amount' => $totals['discount_amount'],
                'promo_code_discount_amount' => $totals['promo_code_discount_amount'],
                'total_amount' => $totals['total_amount'],
                'notes' => $options['notes'] ?? null,
            ]);

            $this->createOrderItems($order, $cart, $promoCode);

            $this->createOrderAddresses($order, $customBillingAddress);

            $this->updateStock($cart);

            $cart->delete();

            $this->sendOrderPlacedNotification($order);

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

        $this->sendOrderStatusChangedNotification($order, $oldStatus);

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

    private function getPromoCode(Cart $cart)
    {
        $promoCode = $cart->promoCode;
        if ($promoCode && (!$promoCode->isValid() || !$promoCode->meetsMinimumOrderValue($cart->totalPrice()))) {
            throw ValidationException::withMessages([
                'promo_code' => "Nieprawidłowy kod promocyjny"
            ]);
        }
        return $promoCode;
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
            'total_amount' => $subtotal + $shippingAmount - $promoCodeDiscountAmount,
        ];
    }


    private function createOrderItems(Order $order, $cart, ?PromoCode $promoCode): void
    {
        foreach ($cart->items as $item) {
            $product = Product::find($item->product_id);
            $finalPrice = $item->final_price;

            $promoCodeDiscountAmount = 0;
            if ($promoCode && $promoCode->isAppliedToProduct($product)) {
                $promoCodeDiscountAmount = $item->quantity * $promoCode->calculateDiscount($finalPrice);
            }

            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item->product_id,
                'sku' => $product->sku,
                'name' => $product->name,
                'quantity' => $item->quantity,
                'price' => $item->price,
                'final_price' => $finalPrice,
                'promo_code_discount_amount' => $promoCodeDiscountAmount,
                'total' => $item->final_price * $item->quantity,
            ]);
        }
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
            'email' => $address->email,
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
                Mail::to($email)->send(new OrderPlaced($order));
            }
        } catch (\Exception $e) {
            logger()->error('Failed to send order placed email', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    private function sendOrderStatusChangedNotification(Order $order, string $oldStatus): void
    {
        try {
            $email = $order->getCustomerEmail();
            if ($email) {
                Mail::to($email)->send(new OrderStatusChanged($order, $oldStatus));
            }
        } catch (\Exception $e) {
            logger()->error('Failed to send order status changed email', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);
        }
    }
}