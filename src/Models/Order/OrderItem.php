<?php

namespace Shopen\Models\Order;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Shopen\Enums\Promocode\ApplyType;
use Shopen\Models\Product\Product;
use Shopen\Models\PromoCode\PromoCode;
use Shopen\Models\PromoCode\PromoCodeCoupon;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'sku',
        'name',
        'quantity',
        'price',
        'final_price',
        'total',
        'tax_amount',
        'promo_code_discount_amount'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'final_price' => 'decimal:2',
        'total' => 'decimal:2',
        'promo_code_coupon_email_sent' => 'boolean'
    ];

    public function getDiscountAttribute()
    {
        return $this->price - $this->final_price;
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function promoCodeCoupons(): BelongsToMany
    {
        return $this->belongsToMany(PromoCodeCoupon::class);
    }

    public function calcTotalAmount(?PromoCode $promoCode)
    {
       $promoCodeDiscountAmount = 0;
        if ($promoCode && $promoCode->applies_to === ApplyType::PER_ITEM && $promoCode->isAppliedToProduct($this->product)) {
            $promoCodeDiscountAmount = $this->quantity * $promoCode->calculateDiscount($this->final_price);
        }
        return $this->final_price * $this->quantity - $promoCodeDiscountAmount;
    }
}