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

    protected $casts = [
        'is_filterable' => 'bool',
        'is_searchable' => 'bool',
        'is_system' => 'bool',
        'is_required' => 'bool',
        'is_visible_in_details' => 'bool',
        'is_used_on_product_page' => 'bool',
        'is_used_in_list' => 'bool',
        'is_color' => 'bool',
    ];

    protected $fillable = [
        'name',
        'sort_order',
        'entity_type',
        'backend_type',
        'frontend_type',
        'code',
        'units',
        'is_filterable',
        'is_searchable',
        'is_required',
        'is_visible_in_details',
        'is_used_on_product_page',
        'is_used_in_list',
        'is_color'
    ];

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

    public function sortedOptions()
    {
        return $this->hasMany(AttributeOption::class, 'attribute_id')->orderBy('value');
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

    public function setFrontendTypeAttribute($value)
    {
        $backendTypes = [
            'bool' => 'bool',
            'select' => 'int',
            'multiselect' => 'int',
            'number' => 'int',
            'text' => 'string',
            'textarea' => 'text',
            'price' => 'decimal',
            'date' => 'date',
        ];
        $this->attributes['frontend_type'] = strtolower($value);
        $this->attributes['backend_type'] = strtolower($backendTypes[$value] ?? null);
    }

}
