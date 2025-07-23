<?php

namespace Shopen\Models\Category\Attribute\Value;

use Illuminate\Database\Eloquent\Model;

class CategoryAttributeValue extends Model
{
    protected $fillable = [
        'entity_id',
        'value'
    ];
}
