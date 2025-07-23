<?php

namespace Shopen\Models\Product\Attribute\Value;


class ProductAttributeDecimal extends ProductAttributeValue
{
    protected $table = 'product_attribute_decimal';

    protected $casts = [
        'value' => 'float',
    ];
}
