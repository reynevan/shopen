<?php

namespace Shopen\Models\Category\Attribute;

use Shopen\Models\Attribute\Attribute;

class CategoryAttribute extends Attribute
{
    protected $table = 'attributes';

    protected $casts = [
        'is_required' => 'bool',
    ];

    public static function query()
    {
        return parent::query()->where('entity_type', Attribute::ENTITY_TYPE_CATEGORY);
    }
}