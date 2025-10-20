<?php

namespace Shopen\Models\PromoCode;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Throwable;

class PromoCodeCoupon extends Model
{

    protected $table = 'promo_code_coupons';

    protected $fillable = [
        'code',
    ];

    public function promoCode(): BelongsTo
    {
        return $this->belongsTo(PromoCode::class);
    }

    public function hasUsageLeft(): bool
    {
        try {
            return $this->promoCode->usage_limit === null || $this->usage_count < $this->promoCode->usage_limit;
        } catch (Throwable $e) {
            return false;
        }
    }
}
