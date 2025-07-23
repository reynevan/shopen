<?php

namespace Shopen\Models\Product\Attribute\Value;


class ProductAttributeBool extends ProductAttributeValue
{
    protected $table = 'product_attribute_bool';

    protected $casts = [
        'value' => 'bool',
    ];
}
