<?php

namespace Shopen\Core\Support\Product\Filtering;


use Elastic\ScoutDriverPlus\Builders\BoolQueryBuilder;

interface FilterInterface
{
    public function apply(BoolQueryBuilder $query): BoolQueryBuilder;
}