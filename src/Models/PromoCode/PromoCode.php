<?php

namespace Shopen\Models\PromoCode;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Shopen\Enums\Promocode\ApplyType;
use Shopen\Enums\Promocode\DiscountType;
use Shopen\Models\Cart\Cart;
use Shopen\Models\Order\Order;
use Shopen\Models\Product\Product;
use Shopen\Models\User;
use Shopen\Repositories\Attribute\AttributeRepository;
use Shopen\Repositories\Category\CategoryRepository;

class PromoCode extends Model
{
    protected $fillable = [
        'code',
        'name',
        'description',
        'is_active',
        'discount_type',
        'discount_value',
        'max_discount_amount',
        'minimum_order_value',
        'applies_to',
        'applies_to_discounted',
        'for_logged_users_only',
        'usage_limit',
        'current_usage_count',
        'valid_from',
        'valid_to',
        'conditions_serialized',
    ];

    protected $casts = [
        'current_usage_count' => 'int',
        'valid_from' => 'datetime',
        'valid_to' => 'datetime',
        'conditions_serialized' => 'array',
        'is_active' => 'bool',
        'applies_to_discounted' => 'bool',
        'for_logged_users_only' => 'bool',
        'discount_type' => DiscountType::class,
        'applies_to' => ApplyType::class
    ];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function coupons(): HasMany
    {
        return $this->hasMany(PromoCodeCoupon::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeValid($query)
    {
        $now = Carbon::now();
        return $query->where('valid_from', '<=', $now)
            ->where(function ($q) use ($now) {
                $q->whereNull('valid_to')
                    ->orWhere('valid_to', '>=', $now);
            });
    }

    public function scopeAvailable($query)
    {
        return $query->active()
            ->valid()
            ->where(function ($q) {
                $q->whereNull('usage_limit')
                    ->orWhereColumn('current_usage_count', '<', 'usage_limit');
            });
    }

    public function isValid(): bool
    {
        return $this->is_active &&
            $this->isWithinValidDates() &&
            $this->canBeUsedBy(Auth::user());
    }

    public function isWithinValidDates(): bool
    {
        $now = Carbon::now();
        return $this->valid_from <= $now &&
            ($this->valid_to === null || $this->valid_to >= $now);
    }

    public function canBeUsedBy(?User $user): bool
    {
        if ($this->for_logged_users_only && !$user) {
            return false;
        }
        return true;
    }

    public function meetsMinimumOrderValue(float $orderValue): bool
    {
        return $orderValue >= $this->minimum_order_value;
    }

    public function getCartDiscount(Cart $cart)
    {
        if ($cart->isEmpty() || !$this->isValid()) {
            return 0;
        }
        $discount = 0;
        if ($this->applies_to === ApplyType::CART) {
            $total = $cart->totalPrice();
            if ($total < $this->minimum_order_value) {
                return 0;
            }
            $discount = $this->calculateDiscount($cart->totalPrice());
        } elseif ($this->applies_to === ApplyType::PER_ITEM) {
            foreach ($cart->items as $item) {
                if (!$this->isAppliedToProduct($item->product)) {
                    continue;
                }
                $discount += $this->calculateDiscount($item->final_price) * $item->quantity;
            }
        }
        return $discount;
    }

    public function isAppliedToProduct(Product $product): bool
    {
        if ($this->applies_to === ApplyType::PER_ITEM &&
            !$this->applies_to_discounted &&
            $product->price &&
            $product->price->isDiscounted()) {
            return false;
        }

        if (empty($this->conditionsAttributes) && empty($this->conditionsCategories)) {
            return true;
        }

        $attributeRepository = app(AttributeRepository::class);
        $categoryRepository = app(CategoryRepository::class);

        if (!empty($this->conditionsAttributes)) {
            foreach ($this->conditionsAttributes as $attributeCondition) {

                $attribute = $attributeRepository->getById($attributeCondition['attribute_id']);
                if (!$attribute) {
                    continue;
                }
                if ($product->getAttribute($attribute->code) != $attributeCondition['value']) {
                    return false;
                }
            }
        }

        if (!empty($this->conditionsCategories)) {
            foreach ($this->conditionsCategories as $categoryId) {
                $category = $categoryRepository->getById($categoryId);
                if (!$category || !$category->hasProduct($product)) {
                    return false;
                }
            }
        }

        return true;
    }

    public function calculateDiscount(float $value): float
    {
        if ($this->discount_type === DiscountType::PERCENTAGE) {
            $discount = ($value * $this->discount_value) / 100;

            if ($this->max_discount_amount !== null) {
                $discount = min($discount, $this->max_discount_amount);
            }

            return $discount;
        }

        return min($this->discount_value, $value);
    }

    public function getIsPercentageAttribute(): bool
    {
        return $this->discount_type === 'percentage';
    }

    public function getConditionsAttributesAttribute()
    {
        return isset($this->conditions) && isset($this->conditions['attributes']) ? $this->conditions['attributes'] : [];
    }

    public function getConditionsCategoriesAttribute()
    {
        return isset($this->conditions) && isset($this->conditions['categories']) ? $this->conditions['categories'] : [];
    }

    public function getConditionsAttribute()
    {
        return json_decode($this->conditions_serialized, true);
    }

    public function getFormattedDiscountAttribute(): string
    {
        if ($this->is_percentage) {
            return $this->discount_value . '%';
        }

        return number_format($this->discount_value, 2) . ' zł';
    }

    public function createCoupon(): PromoCodeCoupon
    {
        $code = mb_strtoupper(Str::random(7));
        while ($this->coupons()->where('code', $code)->exists()) {
            $code = mb_strtoupper(Str::random(7));
        }
        return $this->coupons()->create([
            'code' => $code
        ]);
    }
}
