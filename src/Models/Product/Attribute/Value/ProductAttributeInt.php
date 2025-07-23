<?php

namespace Shopen\Models\Product\Attribute\Value;


class ProductAttributeInt extends ProductAttributeValue
{
    protected $table = 'product_attribute_int';

    protected $casts = [
        'value' => 'int',
    ];
}
