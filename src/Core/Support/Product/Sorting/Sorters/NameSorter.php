<?php

namespace Shopen\Core\Support\Product\Sorting\Sorters;


use Shopen\Core\Support\Product\Sorting\ProductSorter;

class NameSorter extends AttributeSorter implements ProductSorter
{
   protected string $attributeCode = 'name.keyword';

    public function keys(): array
    {
        return ['name_asc', 'name_desc'];
    }

    public function label(string $key): ?string
    {
        return $key === 'name_asc' ? 'Nazwa A–Z' : 'Nazwa Z-A';
    }

    protected function getSortDirection(): string
    {
        return $this->key === 'name_asc' ? self::DIR_ASC : self::DIR_DESC;
    }
}