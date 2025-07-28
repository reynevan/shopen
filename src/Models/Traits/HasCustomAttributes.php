<?php

namespace Shopen\Models\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\Log;
use Shopen\Models\Attribute\Attribute;
use Shopen\Models\Attribute\AttributeOption;
use Shopen\Models\Category\Category;
use Shopen\Models\Product\Product;
use Shopen\Repositories\Attribute\AttributeRepository;
use Shopen\Repositories\Category\CategoryAttributeRepository;
use Shopen\Repositories\Product\ProductAttributeRepository;

trait HasCustomAttributes
{
    protected abstract function getAttributeClass(): string;

    protected abstract function getOriginalAttributes(): array;

    protected function beforeSave(): ?bool
    {
        return true;
    }

    protected function afterSave(): ?bool
    {
        return true;
    }

    protected array $customAttributes = [];

    public function getEntityType(): string {
        return self::ENTITY_TYPE;
    }

    public function toArray()
    {
        return $this->withoutRecursion(
            fn () => array_merge($this->attributesToArray(), $this->relationsToArray(), $this->getCustomAttributes()),
            fn () => $this->attributesToArray(),
        );
    }

    public function setAttribute($key, $value)
    {
        if (in_array($key, $this->getOriginalAttributes())) {
            return parent::setAttribute($key, $value);
        }
        return $this->setCustomAttribute($key, $value);
    }

    public function setCustomAttribute($key, $value): static
    {
        $this->customAttributes[$key] = $value;
        return $this;
    }

    public function getCustomAttributes(): array
    {
        return $this->customAttributes;
    }

    public function getAttribute($key)
    {
        $value = parent::getAttribute($key);
        if ($value) {
            return $value;
        }
        if (isset($this->customAttributes[$key])) {
            return $this->customAttributes[$key];
        }
        $attribute = $this->getAttributeClass()::query()->where('code', $key)->first();
        if (!$attribute) {
            return null;
        }
        return $this->loadAttribute($attribute);
    }

    public function getAttributeTextValue($attribute)
    {
        if (!is_a($attribute, Attribute::class)) {
            $attribute = $this->getAttributeClass()::query()->where('code', $attribute)->first();
        }
        if (!$attribute) {
            return null;
        }
        $valueModel = $attribute->getValueModel();
        $attributeValue = $valueModel::query()->where('entity_id', $this->id)->where('attribute_id', $attribute->id)->first();
        if (!$attributeValue) {
            return null;
        }
        if (!$attribute->isSelectable()) {
            return $attributeValue->value;
        }
        $option = AttributeOption::query()->where('attribute_id', $attribute->id)->where('id', $attributeValue->value)->first();
        return $option->value ?? null;
    }

    public function save(array $options = [])
    {
        if (!$this->beforeSave()) {
            return false;
        }

        if (!parent::save($options)) {
            return false;
        }

        foreach ($this->customAttributes as $attrCode => $value) {
            $attribute = $this->getAttributeClass()::query()->where('code', $attrCode)->first();
            if (!$attribute) {
                continue;
            }
            if ($attribute->isSelectable()) {
                if (!is_array($value)) {
                    $value = [$value];
                }
                $valueModel = $attribute->getValueModel();
                $valueModel::query()->where('entity_id', $this->id)->where('attribute_id', $attribute->id)->delete();
                foreach ($value as $v) {
                    if (is_string($v)) {
                        $v = AttributeOption::query()->where('attribute_id', $attribute->id)->where('value', $v)->first()?->id;
                    }
                    $this->saveAttributeValue($attribute, $v, true);
                }
            } else {
                $this->saveAttributeValue($attribute, $value);
            }
        }

        return $this->afterSave();
    }

    protected function saveAttributeValue(Attribute $attribute, $value, $isMultiValue = false)
    {
        $valueModel = $attribute->getValueModel();
        $parameters = [
            'entity_id' => $this->id,
            'attribute_id' => $attribute->id,
        ];
        if (!$value || (is_array($value) && count($value) === 0)) {
            $valueModel::query()
                ->where($parameters)
                ->delete();
            return;
        }
        if ($isMultiValue) {
            $attributeValue = new $valueModel($parameters);
        } else {
            $attributeValue = $valueModel::query()
                ->where($parameters)
                ->firstOrNew($parameters);
        }
        $attributeValue->attribute_id = $attribute->id;
        $attributeValue->value = $value;
        $attributeValue->store_id = 1;
        $attributeValue->save();
    }

    public function scopeFilterByAttribute(Builder $query, $attribute, $value, $operator = 'and'): Builder
    {
        $attribute = $this->fetchAttribute($attribute);
        if (!$attribute) {
            return $query;
        }
        $valueTable = $this->getEntityType() . '_attribute_' . $attribute->backend_type;

        $subQuery = self::query()
            ->select($this->getTable() . '.id')
            ->leftJoin($valueTable, function (JoinClause $join) use ($valueTable, $attribute, $value) {
                $join
                    ->on("{$valueTable}.entity_id", '=', $this->getTable() . '.id')
                    ->where("{$valueTable}.attribute_id", '=', $attribute->id)
                    ->when(is_array($value), function (JoinClause $query) use ($valueTable, $value) {
                        $query->whereIn("{$valueTable}.value", $value);
                    });

            })
            ->where("{$valueTable}.value", $value);

        if (strtolower($operator) === 'or') {
            return $query->orWhereIn($this->getTable() . '.id', $subQuery);
        }
        return $query->whereIn($this->getTable() . '.id', $subQuery);
    }

    public function scopeOrderByAttribute(Builder $query, $attribute, $order): Builder
    {
        $attribute = $this->fetchAttribute($attribute);
        if (!$attribute) {
            return $query;
        }
        $valueTable = $this->getEntityType() . '_attribute_' . $attribute->backend_type;

        return $query
            ->leftJoin($valueTable, function ($join) use ($attribute, $valueTable) {
                $join->on('products.id', '=', $valueTable . '.entity_id')
                    ->where($valueTable . '.attribute_id', '=', $attribute->id);
            })
            ->orderBy($valueTable . '.value', $order);

    }

    public function loadAttributes($attributes): static
    {
        $time = microtime(true);
        foreach ($attributes as $attribute) {
            $this->loadAttribute($attribute);
        }

        config('app.debug') && Log::debug('[TIME] loadAttributes: ' . (microtime(true) - $time));
        return $this;
    }

    public function loadAttribute(Attribute|string $attribute)
    {
        $attribute = $this->fetchAttribute($attribute);
        $valueModel = $attribute->getValueModel();
        if ($attribute->frontend_type === 'multiselect') {
            $attributeValue = $valueModel::query()
                ->where('entity_id', $this->id)
                ->where('attribute_id', $attribute->id)
                ->pluck('value');
        } else {
            $attributeValue = $valueModel::query()
                ->where('entity_id', $this->id)
                ->where('attribute_id', $attribute->id)
                ->first();
            $attributeValue = $attributeValue->value ?? null;
        }
        $this->customAttributes[$attribute->code] = $attributeValue ?? null;
        if ($this->customAttributes[$attribute->code]) {
            return $this->customAttributes[$attribute->code];
        }
        if ($this->parent_id && $this->parent) {
            return $this->parent->getAttribute($attribute->code);
        }
        return null;
    }

    protected function fetchAttribute(string|Attribute $attribute): ?Attribute
    {
        if (is_a($attribute, Attribute::class)) {
            return $attribute;
        }
        return $this->getAttributeRepository()->getByCode($attribute);
    }

    protected function getAttributeRepository(): AttributeRepository
    {
        if ($this->getEntityType() === Product::ENTITY_TYPE) {
            return app(ProductAttributeRepository::class);
        }
        if ($this->getEntityType() === Category::ENTITY_TYPE) {
            return app(CategoryAttributeRepository::class);
        }
        return app(AttributeRepository::class);
    }


}