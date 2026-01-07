<?php

namespace Shopen\Http\Controllers\Frontend\Api;

use Shopen\Http\Resources\Category\BaseCategoryResource;
use Shopen\Http\Resources\Product\List\ProductResource;
use Shopen\Repositories\Product\ProductAttributeRepository;
use Shopen\Repositories\Product\ProductRepository;
use Shopen\Services\SearchService\SearchService;

class SearchController
{
    public function __construct(
        protected ProductRepository          $productRepository,
        protected SearchService              $searchService,
        protected ProductAttributeRepository $productAttributeRepository,
    )
    {}

    public function search(): array
    {
        $productsSearchResult = $this
            ->searchService
            ->setSearchQuery(request()->query('q'))
            ->setLimit(8)
            ->searchProducts();

        $categoriesSearchResult = $this
            ->searchService
            ->setSearchQuery(request()->query('q'))
            ->setPage(1)
            ->setLimit(5)
            ->searchCategories();
        $products = $productsSearchResult->products();
        $categories = $categoriesSearchResult->categories();

        return [
            'products' => ProductResource::collection($products),
            'categories' => BaseCategoryResource::collection($categories)
        ];
    }
}