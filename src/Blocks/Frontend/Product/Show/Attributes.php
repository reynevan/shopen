<?php

namespace Shopen\Blocks\Frontend\Product\Show;

use Shopen\Blocks\Block;
use Shopen\Core\Context;
use Shopen\Models\Product\Attribute\ProductAttribute;

class Attributes extends Block
{
    public function __construct(private Context $context)
    {}

    public function getAttributesList()
    {
        return ProductAttribute::query()->where('is_visible_in_details', true)->get();
    }
}