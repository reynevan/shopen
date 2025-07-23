<?php

namespace Shopen\Http\Controllers\Admin\Product;

use Inertia\Inertia;
use Shopen\Http\Resources\Admin\Product\ProductResource;
use Shopen\Repositories\Product\ProductRepository;

readonly class ProductsIndexController
{
    public function __construct(
        private ProductRepository $productRepository
    )
    {

    }

    public function index()
    {
        $products = $this->productRepository->getPaginated(request('sort', 'id'), request('dir', 'asc'), request('q'));

        return Inertia::render('Admin/Product/Index', [
            'products' => ProductResource::collection($products),
            'sort' => request('sort', 'id'),
            'dir' => request('dir', 'desc'),
            'q' => request('q')
        ]);
    }
}