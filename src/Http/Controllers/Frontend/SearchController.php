<?php

namespace Shopen\Http\Controllers\Frontend;

use Elastic\ScoutDriverPlus\Support\Query;
use Inertia\Inertia;
use Shopen\Http\Resources\Product\ProductResource;
use Shopen\Models\Product\Product;
use Shopen\Repositories\Product\ProductRepository;

class SearchController
{
    public function __construct(
        protected ProductRepository $productRepository,
    )
    {

    }

    public function index()
    {
        $term = request()->input('q');
        $query = Query::bool()
            ->should(Query::match()->field('name')->query($term)->analyzer('polish')->boost(3)->fuzziness('AUTO'))
            ->should(Query::match()->field('description')->query($term)->analyzer('polish')->boost(0.5)->fuzziness('AUTO'))
            ->should(Query::match()->field('searchable_attributes')->query($term)->analyzer('polish'))
            ->minimumShouldMatch(1);
        $products = Product::searchQuery($query)->paginate(32)->withQueryString();
        $products->setCollection($products->models());

        $this->productRepository->addAttributesUsedInList($products);
        $this->productRepository->addThumbnails($products);

        return Inertia::render('Frontend/Search/Index', [
            'products' => ProductResource::collection($products),
            'banners' => [],
            'filters' => [],
            'activeFilters' => []
        ]);
    }
}