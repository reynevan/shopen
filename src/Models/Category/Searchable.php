<?php

namespace Shopen\Models\Category;


trait Searchable
{
    use \Elastic\ScoutDriverPlus\Searchable;

    public function shouldBeSearchable(): bool
    {
        return !!$this->is_active;
    }

    public function toSearchableArray()
    {
        return [
            'id' => $this->id,
            'name' => $this->getCustomAttribute('name')
        ];
    }
}