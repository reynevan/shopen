<?php

namespace Shopen\Models\Attribute;

use Illuminate\Database\Eloquent\Model;
use Shopen\Models\Traits\HasSlug;

class AttributeOption extends Model
{
    use HasSlug;

    protected $fillable = [
        'attribute_id',
        'value',
        'color'
    ];

    protected function slugAttribute(): string {
        return 'value';
    }
}
