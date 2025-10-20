<?php

namespace Shopen\Models\Category;


use Shopen\Models\Product\Product;

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
            'name' => $this->getCustomAttribute('name'),
            'parent_id' => $this->parent_id,
            'url_key' => $this->getUrl(false),
            'products_count' => $this
                ->products()
                ->where('visible_individually', true)
                ->filterByAttribute('is_active', true)
                ->count(),
        ];
    }
}