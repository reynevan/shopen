<?php

namespace Shopen\Models\Category\Attribute\Value;

use Illuminate\Database\Eloquent\Model;

class CategoryAttributeBool extends CategoryAttributeValue
{
    protected $table = 'category_attribute_bool';


    protected function casts(): array
    {
        return [
            'value' => 'boolean',
        ];
    }
}
