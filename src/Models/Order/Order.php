<?php

namespace Shopen\Models\Order;

use Illuminate\Container\Container;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Shopen\Core\Payment\PaymentMethodManager;
use Shopen\Core\Shipping\ShippingMethodManager;
use Shopen\Enums\Order\OrderStatus;
use Shopen\Enums\Payment\PaymentStatus;
use Shopen\Models\Order\Invoice\Invoice;
use Shopen\Models\PromoCode\PromoCodeCoupon;
use Shopen\Models\User;
use Shopen\Pagination\LengthAwarePaginator;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'user_id',
        'promo_code_coupon_id',
        'order_number',
        'status',
        'shipping_method',
        'delivery_point_code',
        'shipping_tracking_code',
        'payment_method',
        'subtotal',
        'promo_code_discount_amount',
        'discount_amount',
        'shipping_amount',
        'payment_amount',
        'total_amount',
        'tax_amount',
        'notes',
        'shipped_at',
        'delivered_at'
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'shipping_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
        'status' => OrderStatus::class,
    ];

    protected function paginator($items, $total, $perPage, $currentPage, $options)
    {
        return Container::getInstance()->makeWith(LengthAwarePaginator::class, compact(
            'items', 'total', 'perPage', 'currentPage', 'options'
        ));
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(OrderAddress::class);
    }

    public function shippingAddress()
    {
        return $this->hasOne(OrderAddress::class)->where('type', 'shipping');
    }

    public function billingAddress()
    {
        return $this->hasOne(OrderAddress::class)->where('type', 'billing');
    }

    public function promoCodeCoupon(): BelongsTo
    {
        return $this->belongsTo(PromoCodeCoupon::class);
    }

    public function statusHistoryItems(): HasMany
    {
        return $this->hasMany(OrderStatusHistory::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function latestPayment()
    {
        return $this->payments()->latest()->first();
    }

    public function getItemsCountAttribute(): int
    {
        return $this->items->sum('quantity');
    }

    public function getPlacedTimeAttribute()
    {
        return $this->created_at->toLocalDateTime();
    }

    public function getPlacedDateAttribute()
    {
        return $this->created_at->format('Y-m-d');
    }

    public function isGuestOrder(): bool
    {
        return is_null($this->user_id);
    }

    public function isPaid(): bool
    {
        return $this->payments()->where('status', PaymentStatus::COMPLETED->value)->exists();
    }

    public function getUserFirstName()
    {
        if ($this->user && $this->user->first_name) {
            return $this->user->first_name;
        }
        if ($this->billingAddress && $this->billingAddress->first_name) {
            return $this->billingAddress->first_name;
        }
        if ($this->shippingAddress && $this->shippingAddress->first_name) {
            return $this->shippingAddress->first_name;
        }
        return null;
    }

    public function getStatusLabelAttribute(): string
    {
        return $this->status->label();
    }

    public function hasVouchers(): bool
    {
        return $this->items()->whereHas('product', function (Builder $query) {
            $query->where('is_voucher', true);
        })->exists();
    }

    public static function getStatusOptions(): array
    {
        return OrderStatus::options();
    }

    public function getCustomerEmail(): ?string
    {
        if ($this->isGuestOrder()) {
            $billingAddress = $this->billingAddress();
            return $billingAddress ? $billingAddress->email : null;
        }
        
        return $this->user ? $this->user->email : null;
    }

    public static function generateOrderNumber(): string
    {
        return date('Y') . str_pad(static::whereYear('created_at', date('Y'))->count() + 1, 6, '0', STR_PAD_LEFT);
    }

    public function scopeSort(Builder $query, $sortField, $sortDir)
    {
        $query->when(in_array($sortField, ['id', 'created_at']), function ($query) use ($sortField, $sortDir) {
            $query->orderBy($sortField, $sortDir);
        });
    }

    public function finalProductsAmount(): float
    {
        $total = 0.0;
        foreach ($this->items as $item) {
            $total += $item->total;
        }
        return $total;
    }

    public function getShippingMethodName()
    {
        if (!$this->shipping_method) {
            return null;
        }
        $shippingMethod = app(ShippingMethodManager::class)->get($this->shipping_method);
        return $shippingMethod ? $shippingMethod->getName() : null;
    }

    public function getPaymentMethodName()
    {
        if (!$this->payment_method) {
            return null;
        }
        $paymentMethod = app(PaymentMethodManager::class)->get($this->payment_method);
        return $paymentMethod ? $paymentMethod->getName() : null;
    }

    public function canBeCancelled(): bool
    {
        return $this->status === OrderStatus::NEW;
    }

    public function canBePaid(): bool
    {
        $paymentMethod = app(PaymentMethodManager::class)->get($this->payment_method);
        return $this->status === OrderStatus::NEW && $paymentMethod->requiresRedirect();
    }
}