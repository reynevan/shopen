<?php

namespace Shopen\Repositories\Category;

use Shopen\Models\Category\Attribute\CategoryAttribute;
use Shopen\Repositories\Attribute\AttributeRepository;

class CategoryAttributeRepository extends AttributeRepository
{
    const ATTRIBUTE_MODEL = CategoryAttribute::class;

}