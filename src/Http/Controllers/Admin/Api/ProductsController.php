<?php

namespace Shopen\Http\Controllers\Admin\Api;

use Shopen\Http\Resources\Admin\Product\BaseProductResource;
use Shopen\Repositories\Product\ProductRepository;

class ProductsController
{
    public function __construct(
        private ProductRepository $productRepository
    )
    {}

    public function index()
    {
        $products = $this->productRepository->getPaginated(
            request('sort', 'id'),
            request('dir', 'asc'),
            request('q'),
            ['name', 'is_active']);

        return BaseProductResource::collection($products);
    }
}
