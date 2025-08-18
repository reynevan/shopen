<?php

namespace Shopen\Models\Attribute;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Shopen\Database\Factories\AttributeFactory;
use Shopen\Models\Traits\HasSlug;

class Attribute extends Model
{
    use HasFactory, HasSlug;

    const ENTITY_TYPE_PRODUCT = 'product';
    const ENTITY_TYPE_CATEGORY = 'category';

    protected static function newFactory()
    {
        return AttributeFactory::new();
    }

    public function getValueModel()
    {
        $type = ucfirst($this->entity_type);
        return "Shopen\Models\\{$type}\Attribute\Value\\{$type}Attribute" . ucfirst($this->backend_type);
    }

    public function scopeTypeProduct(Builder $query)
    {
        return $query->where('entity_type', self::ENTITY_TYPE_PRODUCT);
    }

    public function options()
    {
        return $this->hasMany(AttributeOption::class, 'attribute_id')->orderBy('sort_order');
    }

    public function renderInput($value)
    {
        return view('shopen::admin.elements.form.inputs.' . $this->frontend_type, ['value' => $value, 'attribute' => $this]);
    }

    public function getIsIndexableAttribute()
    {
        return $this->is_searchable || $this->is_sortable || $this->is_filterable;
    }

    public function isSelectable()
    {
        return in_array($this->frontend_type, ['select', 'multiselect']);
    }

    public function isMultiselect()
    {
        return $this->frontend_type === 'multiselect';
    }

}
