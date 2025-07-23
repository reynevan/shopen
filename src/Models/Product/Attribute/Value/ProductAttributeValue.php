<?php

namespace Shopen\Models\Product\Attribute\Value;

use Illuminate\Database\Eloquent\Model;

class ProductAttributeValue extends Model
{
    protected $fillable = [
        'entity_id'
    ];

    protected $table = 'product_attribute_string';
}
