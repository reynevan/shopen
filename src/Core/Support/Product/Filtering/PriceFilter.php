<?php

namespace Shopen\Core\Support\Product\Filtering;

use Elastic\ScoutDriverPlus\Builders\BoolQueryBuilder;
use Elastic\ScoutDriverPlus\Support\Query;

class PriceFilter implements FilterInterface
{
    protected ?float $min;
    protected ?float $max;

    public function __construct(?float $min, ?float $max)
    {
        $this->min = $min;
        $this->max = $max;
    }


    public function apply(BoolQueryBuilder $query): BoolQueryBuilder {
        if (!$this->min && !$this->max) {
            return $query;
        }
        $range = Query::range()->field('price');
        if ($this->min) {
            $range->gte($this->min);
        }
        if ($this->max) {
            $range->lte($this->max);
        }
        $query->filter($range);
        return $query;
    }

}