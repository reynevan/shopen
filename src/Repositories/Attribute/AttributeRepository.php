<?php

namespace Shopen\Repositories\Attribute;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Shopen\Models\Attribute\Attribute;

class AttributeRepository
{
    const ATTRIBUTE_MODEL = Attribute::class;

    protected array $attributeCodes = [];

    protected array $filterable = [];

    public function getByCode($code)
    {
        return static::ATTRIBUTE_MODEL::query()->where('code', $code)->first();
    }

    public function exists($code)
    {
        if (!isset($this->attributeCodes[self::ATTRIBUTE_MODEL]) || !count($this->attributeCodes[self::ATTRIBUTE_MODEL])) {
            $this->attributeCodes[self::ATTRIBUTE_MODEL] = static::ATTRIBUTE_MODEL::query()->select(['code'])->get()->pluck('code')->toArray();
        }
        return in_array($code, $this->attributeCodes[self::ATTRIBUTE_MODEL]);
    }

    public function getById($id)
    {
        return static::ATTRIBUTE_MODEL::query()->where('id', $id)->first();
    }

    public function getAll(): Collection
    {
        return static::ATTRIBUTE_MODEL::query()
            ->with(['options'])
            ->get();
    }

    public function getUsedOnProductPage(): Collection
    {
        return static::ATTRIBUTE_MODEL::query()
            ->with(['options'])
            ->where('is_used_on_product_page', true)
            ->orWhere('is_visible_in_details', true)
            ->get();
    }

    public function getAllByCode($codes)
    {
        return static::ATTRIBUTE_MODEL::query()
            ->whereIn('code', $codes)
            ->get();
    }

    public function getIndexable()
    {
        return static::ATTRIBUTE_MODEL::query()
            ->where('is_system', false)
            ->where(function (Builder $query) {
                return $query
                    ->where('is_searchable', true)
                    ->orWhere('is_sortable', true)
                    ->orWhere('is_filterable', true);
            })
            ->get();
    }

    public function getSearchable()
    {
        return static::ATTRIBUTE_MODEL::query()
            ->where('is_system', false)
            ->where('is_searchable', true)
            ->get();
    }

    public function getUsedInList(): Collection
    {
        return static::ATTRIBUTE_MODEL::query()->where('is_used_in_list', true)->get();
    }

    public function getVisibleInDetails(): Collection
    {
        return static::ATTRIBUTE_MODEL::query()
            ->with(['options'])
            ->where('is_visible_in_details', true)
            ->get();
    }

    public function getFilterable($withOptions = false): Collection
    {
        if (isset($this->filterable[static::ATTRIBUTE_MODEL][$withOptions])) {
            return $this->filterable[static::ATTRIBUTE_MODEL][$withOptions];
        }
        $this->filterable[static::ATTRIBUTE_MODEL][$withOptions] = static::ATTRIBUTE_MODEL::query()
            ->when($withOptions, function (Builder $query) {
                $query->with(['options']);
            })
            ->where('is_filterable', true)
            ->whereIn('frontend_type', ['multiselect', 'select'])
            ->get();
        return $this->filterable[static::ATTRIBUTE_MODEL][$withOptions];
    }

    public function getSortable(): Collection
    {
        return static::ATTRIBUTE_MODEL::query()->where('is_sortable', true)->get();
    }

    public function getPaginated($sortField, $sortDir, $searchQuery = null)
    {
        return Attribute::query()
            ->when($searchQuery, function (Builder $query) use ($searchQuery) {
                $query
                    ->whereLike('name', '%' . $searchQuery . '%')
                    ->orWhereLike('code', '%' . $searchQuery . '%');
            })
            ->orderBy($sortField, $sortDir)
            ->paginate(25)
            ->withQueryString();
    }

    public function getValues($attributeCode, $ids = null)
    {
        $attribute = $this->getByCode($attributeCode);
        if (!$attribute) {
            return [];
        }
        return $attribute->getValueModel()::query()
            ->where('attribute_id', $attribute->id)
            ->when($ids, function ($query) use ($ids) {
                $query->whereIn('entity_id', $ids);
            })
            ->select(['entity_id', 'value'])
            ->pluck('value', 'entity_id');
    }
}