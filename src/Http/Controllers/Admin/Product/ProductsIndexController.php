<?php

namespace Shopen\Http\Controllers\Admin\Product;

use Inertia\Inertia;
use Inertia\Response;
use Shopen\Repositories\Product\ProductRepository;
use Shopen\Http\Resources\Admin\Product\BaseProductResource;

readonly class ProductsIndexController
    {
        public function __construct(
            private ProductRepository $productRepository
        )
        {}

    public function index(): Response
    {
        $products = $this->productRepository->getPaginated(
            request('sort', 'id'),
            request('dir', 'asc'),
            request('q'),
            ['name', 'is_active']);


        return Inertia::render('Admin/Product/Index', [
            'products' => BaseProductResource::collection($products),
            'sort' => request('sort', 'id'),
            'dir' => request('dir', 'asc'),
            'q' => request('q')
        ]);
    }
}