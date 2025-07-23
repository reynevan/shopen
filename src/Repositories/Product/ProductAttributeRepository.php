<?php

namespace Shopen\Repositories\Product;

use Illuminate\Database\Eloquent\Collection;
use Shopen\Models\Product\Attribute\ProductAttribute;
use Shopen\Repositories\Attribute\AttributeRepository;

class ProductAttributeRepository extends AttributeRepository
{
    const ATTRIBUTE_MODEL = ProductAttribute::class;
}