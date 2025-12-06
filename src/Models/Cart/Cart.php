<?php

namespace Shopen\Models\Cart;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Shopen\Core\Shipping\ShippingMethodManager;
use Shopen\Enums\Address\AddressType;
use Shopen\Models\PromoCode\PromoCode;
use Shopen\Models\PromoCode\PromoCodeCoupon;
use Shopen\Models\User;
use Shopen\Services\ShippingService;

class Cart extends Model
{

    protected $fillable = [
        'user_id',
        'uuid',
    ];

    protected static function booted(): void
    {
        static::creating(function (Cart $cart) {
            if (!$cart->uuid) {
                $cart->uuid = Str::uuid()->toString();
            }
        });
    }

    protected $casts = [
        'delivery_point' => 'json'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    public function promoCodeCoupon(): BelongsTo
    {
        return $this->belongsTo(PromoCodeCoupon::class);
    }

    public function shippingAddress()
    {
        return $this->hasOne(CartAddress::class)->where('type', AddressType::SHIPPING);
    }

    public function billingAddress()
    {
        return $this->hasOne(CartAddress::class)->where('type', AddressType::BILLING);
    }

    public function addProduct(int $productId, int $quantity, float $price, float $finalPrice): void
    {
        $item = $this->items()->where('product_id', $productId)->first();

        if ($item) {
            $item->quantity += $quantity;
            $item->price = $price;
            $item->final_price = $finalPrice;
            $item->save();
        } else {
            $this->items()->create([
                'product_id' => $productId,
                'quantity'   => $quantity,
                'price'      => $price,
                'final_price' => $finalPrice,
            ]);
        }
    }

    public function removeProduct(int $productId): void
    {
        $this->items()->where('product_id', $productId)->delete();
    }

    public function removeItem(int $itemId): void
    {
        $this->items()->where('id', $itemId)->delete();
    }

    public function updateItemQty(int $itemId, int $quantity): void
    {
        $this->items()->where('id', $itemId)->update(['quantity' => $quantity]);
    }

    public function itemCount(): int
    {
        return $this->items()->sum('quantity');
    }

    public function productCount($productId): int
    {
        return $this->items()->where('product_id', $productId)->sum('quantity');
    }

    public function isEmpty(): bool
    {
        return $this->items()->count() === 0;
    }

    public function subtotalPrice(): float
    {
        $total = 0.0;
        foreach ($this->items as $item) {
            $total += $item->price * $item->quantity;
        }
        return $total;
    }

    public function totalPrice(): float
    {
        $total = 0.0;
        foreach ($this->items as $item) {
            $total += $item->final_price * $item->quantity;
        }
        return $total;
    }

    public function hasOnlyVirtualItems(): bool
    {
        return $this
            ->items()
            ->whereHas('product', function ($query) {
                $query->where('is_virtual', '<>', true);
            })->count() === 0;
    }

    public function getShippingPrice()
    {
        if ($this->hasOnlyVirtualItems()) {
            return 0;
        }
        $shippingMethods = app(ShippingService::class)->getAvailableShippingMethods();
        $shipping = null;
        foreach ($shippingMethods as $shippingMethod) {
            $price = $shippingMethod->getPrice();
            $shipping = ($price < $shipping || $shipping === null) ? $price : $shipping;
        }
        return $shipping;
    }
}
