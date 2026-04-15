<?php

namespace Shopen\Models\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Shopen\Models\Attribute\Attribute;
use Shopen\Models\Attribute\AttributeOption;
use Shopen\Models\Category\Category;
use Shopen\Models\Product\Product;
use Shopen\Repositories\Attribute\AttributeRepository;
use Shopen\Repositories\Category\CategoryAttributeRepository;
use Shopen\Repositories\Product\ProductAttributeRepository;
use Shopen\Services\StoreManager;

trait HasCustomAttributes
{
    protected abstract function getAttributeClass(): string;

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
        if ($this->getAttributeRepository()->exists($key)) {
            return $this->setCustomAttribute($key, $value);
        }
        return parent::setAttribute($key, $value);
    }

    public function getAttribute($key)
    {
        if ($this->getAttributeRepository()->exists($key)) {
            return $this->getCustomAttribute($key);
        }
        return parent::getAttribute($key);
    }

    public function setCustomAttribute($key, $value): static
    {
        $this->customAttributes[$key] = $value;
        return $this;
    }

    public function getCustomAttribute($key)
    {
        if (isset($this->customAttributes[$key]) && $this->customAttributes[$key]) {
            return $this->customAttributes[$key];
        }
        if (!$this->getAttributeRepository()->exists($key)) {
            return null;
        }
        return $this->loadAttribute($key);
    }

    public function clearCustomAttributes(): void
    {
        $this->customAttributes = [];
    }

    public function getCustomAttributes(): array
    {
        return $this->customAttributes;
    }

    public function getAttributeTextValue($attribute)
    {
        $attribute = $this->fetchAttribute($attribute);
        if (!$attribute) {
            return null;
        }
        $value = $this->loadAttribute($attribute, false);
        if (!$attribute->isSelectable()) {
            return $value;
        }
        if (!$value || (is_array($value) && count($value) === 0)) {
            return null;
        }
        if ($attribute->isMultiselect()) {
            return AttributeOption::query()->where('attribute_id', $attribute->id)->whereIn('id', $value)->get()->pluck('value')->values()->toArray();
        }
        $option = AttributeOption::query()->where('attribute_id', $attribute->id)->where('id', $value)->first();
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
                $this->saveSelectableAttributeValue($attribute, $value, true);

            } else {
                $this->saveAttributeValue($attribute, $value);
            }
        }

        return $this->afterSave();
    }

    protected function saveSelectableAttributeValue(Attribute $attribute, $values, $isMultiValue = false)
    {
        if (!is_array($values)) {
            $values = [$values];
        }
        $valueModel = $attribute->getValueModel();
        $parameters = [
            'entity_id' => $this->id,
            'attribute_id' => $attribute->id,
        ];
        $valueModel::query()
            ->where($parameters)
            ->delete();
        if (!count($values)) {
            return;
        }
        foreach ($values as $value) {
            if (is_string($value)) {
                $value = AttributeOption::query()->where('attribute_id', $attribute->id)->where('value', $value)->first()?->id;
            }
            $attributeValue = new $valueModel($parameters);
            $attributeValue->attribute_id = $attribute->id;
            $attributeValue->entity_id = $this->id;
            $attributeValue->value = $value;
            $attributeValue->store_id = 1;
            $attributeValue->save();
        }
    }

    protected function saveAttributeValue(Attribute $attribute, $value, $isMultiValue = false)
    {
        $valueModel = $attribute->getValueModel();
        $parameters = [
            'entity_id' => $this->id,
            'attribute_id' => $attribute->id,
        ];
        if (!$value) {
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
        $isLikeSearch = Str::startsWith($value, '%') || Str::endsWith($value, '%');
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
            ->when($isLikeSearch, function (Builder $query) use ($valueTable, $value) {
                $query->whereLike("{$valueTable}.value", $value);
            })
            ->when(!$isLikeSearch, function (Builder $query) use ($valueTable, $value) {
                $query->where("{$valueTable}.value", $value);
            });

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
        $stringAttributes = Collection::make($attributes)->filter(fn ($attribute) => is_string($attribute))->count() === count($attributes);
        if ($stringAttributes) {
            $attributes = $this->getAttributeRepository()->getAllByCode($attributes);
        }
        foreach ($attributes as $attribute) {
            $this->loadAttribute($attribute);
        }
        return $this;
    }

    public function getCustomAttributeValue(Attribute|string $attribute, $textValue = false)
    {
        $attribute = $this->fetchAttribute($attribute);
        $valueModel = $attribute->getValueModel();
        $storeId = app(StoreManager::class)->getCurrentStore()->id;
        if ($attribute->frontend_type === 'multiselect') {
            $attributeValue = $valueModel::query()
                ->where('entity_id', $this->id)
                ->where('attribute_id', $attribute->id)
                ->where('store_id', $storeId)
                ->pluck('value');
            if ($textValue) {
                $attributeValue = AttributeOption::query()
                    ->whereIn('id', $attributeValue)
                    ->where('store_id', $storeId)
                    ->pluck('value');
            }
        } else {
            $attributeValue = $valueModel::query()
                ->where('entity_id', $this->id)
                ->where('attribute_id', $attribute->id)
                ->where('store_id', $storeId)
                ->first();
            if ($textValue && $attributeValue && $attribute->isSelectable()) {
                $attributeValue = AttributeOption::query()
                    ->where('id', $attributeValue->value)
                    ->where('store_id', $storeId)
                    ->first();
            }
            $attributeValue = $attributeValue->value ?? null;
        }
        if ($attributeValue) {
            return $attributeValue;
        }
        if ($this->getEntityType() === Product::ENTITY_TYPE && $this->parent_id && $this->parent) {
            return $this->parent->loadAttribute($attribute, $textValue);
        }
        return null;
    }

    public function getCustomAttributeColor(Attribute|string $attribute)
    {
        $attribute = $this->fetchAttribute($attribute);
        if (!$attribute->isSelectable()) {
            return null;
        }
        $valueModel = $attribute->getValueModel();
        $attributeValue = $valueModel::query()
            ->where('entity_id', $this->id)
            ->where('attribute_id', $attribute->id)
            ->pluck('value');
        $attributeValue = AttributeOption::query()
            ->whereIn('id', $attributeValue)
            ->pluck('color');
        if ($attribute->frontend_type === 'select') {
            $attributeValue = count($attributeValue) > 0 ? $attributeValue[0] : null;
        }
        if ($attributeValue) {
            return $attributeValue;
        }
        if ($this->getEntityType() === Product::ENTITY_TYPE && $this->parent_id && $this->parent) {
            return $this->parent->getCustomAttributeColor($attribute);
        }
        return null;
    }

    public function loadAttribute(Attribute|string $attribute, $textValue = true)
    {
        $attribute = $this->fetchAttribute($attribute);
        $attributeValue = $this->getCustomAttributeValue($attribute, $textValue);
        if ($attributeValue) {
            $this->customAttributes[$attribute->code] = $attributeValue;
        }
        return $attributeValue;
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