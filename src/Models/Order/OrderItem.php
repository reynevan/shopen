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
        'promo_code_discount_amount',
        'unit',
        'tax_rate'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'final_price' => 'decimal:2',
        'total' => 'decimal:2',
        'promo_code_coupon_email_sent' => 'boolean',
        'promo_code_discount_amount' => 'decimal:2',
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

    public function getFinalPriceNetAttribute()
    {
        return $this->getNetValue($this->final_price);
    }

    public function getTotalNetAttribute()
    {
        return $this->getNetValue($this->total);
    }

    public function getPromoCodeDiscountAmountNetAttribute()
    {
        return $this->getNetValue($this->promo_code_discount_amount);
    }

    protected function getNetValue($value)
    {
        if (!$value) {
            return 0;
        }

        $taxRate = $this->tax_rate ?? 0;

        if ($taxRate <= 0) {
            return (float) $value;
        }

        return round($value / (1 + $taxRate / 100), 2);
    }
}