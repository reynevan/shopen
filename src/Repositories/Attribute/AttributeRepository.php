<?php

namespace Shopen\Repositories\Attribute;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Shopen\Models\Attribute\Attribute;

class AttributeRepository
{
    const ATTRIBUTE_MODEL = Attribute::class;

    protected array $filterable = [];

    public function getByCode($code)
    {
        return static::ATTRIBUTE_MODEL::query()->where('code', $code)->first();
    }

    public function getById($id)
    {
        return static::ATTRIBUTE_MODEL::query()->where('id', $id)->first();
    }

    public function getAll(): Collection
    {
        return static::ATTRIBUTE_MODEL::query()->get();
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

    public function getFilterable(): Collection
    {
        if (isset($this->filterable[static::ATTRIBUTE_MODEL])) {
            return $this->filterable[static::ATTRIBUTE_MODEL];
        }
        $this->filterable[static::ATTRIBUTE_MODEL] =  static::ATTRIBUTE_MODEL::query()
            ->where('is_filterable', true)
            ->whereIn('frontend_type', ['multiselect', 'select'])
            ->get();
        return $this->filterable[static::ATTRIBUTE_MODEL];
    }

    public function getSortable(): Collection
    {
        return static::ATTRIBUTE_MODEL::query()->where('is_sortable', true)->get();
    }
}