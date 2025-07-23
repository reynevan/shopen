<?php

namespace Shopen\Blocks\Frontend\Layout;

use Shopen\Blocks\Block;
use Shopen\Core\Context;

class Breadcrumbs extends Block
{
    public function __construct(private readonly Context $context)
    {}

    public function getCurrentProductJson()
    {
        $product = $this->context->getCurrentProduct();
        if (!$product) {
            return null;
        }
        return json_encode([
            'name' => $product->name,
            'url' => $product->getUrl()
        ]);
    }

    public function getCurrentCategoryJson()
    {
        $category = $this->context->getCurrentCategory();
        if (!$category) {
            return null;
        }
        return json_encode([
            'name' => $category->name,
            'url' => $category->getUrl()
        ]);
    }
}