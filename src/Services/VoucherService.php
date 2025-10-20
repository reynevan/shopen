<?php

namespace Shopen\Services;

use Shopen\Enums\Promocode\ApplyType;
use Shopen\Enums\Promocode\DiscountType;
use Shopen\Models\Product\Product;
use Shopen\Models\PromoCode\PromoCode;

class VoucherService
{
    public function createPromoCodeForProduct(Product $product, $price)
    {
        if (!$product->is_voucher) {
            return null;
        }
        $promoCode = new PromoCode();
        $promoCode->fill([
            'name' => $product->getCustomAttributeValue('name'),
            'is_active' => true,
            'discount_type' => DiscountType::FIXED,
            'discount_value' => $price,
            'applies_to' => ApplyType::CART,
            'applies_to_discounted' => true,
            'usage_limit' => 1
        ]);
        $promoCode->save();

        $product->promo_code_id = $promoCode->id;
        $product->save();
    }
}