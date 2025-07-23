<?php

namespace Shopen\Models\Product\Attribute;

use Shopen\Models\Attribute\Attribute;

class ProductAttribute extends Attribute
{
    protected $table = 'attributes';

    public static function query()
    {
        return parent::query()->where('entity_type', Attribute::ENTITY_TYPE_PRODUCT);
    }
}