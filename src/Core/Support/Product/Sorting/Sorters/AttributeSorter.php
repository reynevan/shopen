<?php

namespace Shopen\Core\Support\Product\Sorting\Sorters;

use Laravel\Scout\Builder;
use Shopen\Core\Support\Product\Sorting\ProductSorter;
use Shopen\Repositories\Product\ProductAttributeRepository;

abstract class AttributeSorter implements ProductSorter
{
    const DIR_ASC = 'ASC';
    const DIR_DESC = 'DESC';

    protected string $attributeCode;

    protected string $key;

    public function __construct(private readonly ProductAttributeRepository $attributeRepository)
    {}

    protected abstract function getSortDirection():string;

    public function setKey($key): static
    {
        $this->key = $key;
        return $this;
    }

    public function keys(): array
    {
        return ['name_asc'];
    }

    public function label(string $key): ?string
    {
        return $key === 'name_asc' ? 'Nazwa A–Z' : null;
    }

    public function build(): array
    {
        return [$this->attributeCode => $this->getSortDirection($this->key)];
    }
}