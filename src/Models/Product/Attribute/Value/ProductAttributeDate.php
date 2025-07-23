<?php

namespace Shopen\Models\Product\Attribute\Value;


class ProductAttributeDate extends ProductAttributeValue
{
    protected $table = 'product_attribute_date';

    protected $casts = [
        'value' => 'date',
    ];
}
