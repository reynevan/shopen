<?php

namespace Shopen\Blocks\Frontend\Product\Show;

use Shopen\Blocks\Block;
use Shopen\Core\Context;

class Gallery extends Block
{
    public function __construct(private readonly Context $context)
    {}

    public function getImages()
    {
        $product = $this->context->getCurrentProduct();
        return $product->getImagesUrls();
    }
}