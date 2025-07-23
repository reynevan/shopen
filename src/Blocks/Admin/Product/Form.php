<?php

namespace Shopen\Blocks\Admin\Product;

use Shopen\Blocks\Block;
use Shopen\Repositories\Product\ProductAttributeRepository;

class Form extends Block
{
    public function __construct(private ProductAttributeRepository $productAttributeRepository)
    {}

    public function getAttributes()
    {
        return $this->productAttributeRepository->getAll();
    }
}