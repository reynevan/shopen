<?php

namespace Shopen\Core\Support\Product\Filtering;


use Elastic\ScoutDriverPlus\Builders\BoolQueryBuilder;
use Elastic\ScoutDriverPlus\Support\Query;

class AttributeFilter implements FilterInterface
{
    protected string $attributeCode;
    protected $value;

    public function __construct(string $attributeCode, $value)
    {
        $this->attributeCode = $attributeCode;
        if (!is_array($value)) {
            $value = [$value];
        }
        $this->value = $value;
    }

    public function apply(BoolQueryBuilder $query): BoolQueryBuilder
    {
        $term = Query::terms()->field($this->attributeCode)->values($this->value);
        return $query->filter($term);
    }
}