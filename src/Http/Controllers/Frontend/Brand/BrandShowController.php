<?php

namespace Shopen\Http\Controllers\Frontend\Brand;

use App\Support\ProductSorting\ProductSortRegistry;
use Inertia\Inertia;
use Inertia\Response;
use Shopen\Http\Resources\Brand\BrandResource;
use Shopen\Http\Resources\Product\List\ProductResource;
use Shopen\Models\Brand\Brand;
use Shopen\Repositories\Product\ProductAttributeRepository;
use Shopen\Repositories\Product\ProductRepository;
use Shopen\Services\BannerService;
use Shopen\Services\FiltersService;
use Shopen\Services\SearchService\SearchService;
use Shopen\Services\ShoppingListService;

readonly class BrandShowController
{

    public function __construct(
        protected ProductAttributeRepository $productAttributeRepository,
        protected ProductRepository          $productRepository,
        protected BannerService              $bannerService,
        protected ProductSortRegistry        $productSortRegistry,
        protected SearchService              $searchService,
        protected FiltersService             $filtersService,
        protected ShoppingListService        $shoppingListService
    )
    {
    }

    public function show(Brand $brand): Response
    {
        if (request()->query('sort')) {
            $this->productSortRegistry->setDefault(request()->query('sort'));
        }

        $searchResult = $this
            ->searchService
            ->setBrandId($brand->id)
            ->setFilters($this->filtersService->getSimpleFilters())
            ->setSort(request()->query('sort'))
            ->setPage(request()->query('strona', 1))
            ->searchProducts();

        $products = $searchResult->paginatedProducts();

        return Inertia::render('Frontend/Brand/Show', [
            'brand' => fn() => BrandResource::make($brand),
            'products' => fn() => ProductResource::collection($products),
            'filters' => fn() => [
                'attributes' => $searchResult->getFilters(['brand']),
                'priceRange' => $searchResult->getPriceFilters()
            ],
            'activeFilters' => fn() => $this->filtersService->getFullActiveFilters(),
            'banners' => fn() => [],
            'activeSort' => fn() => request()->query('sort') ?? $this->productSortRegistry->defaultKey(),
            'sortOptions' => fn() => $this->productSortRegistry->allOptions(),
            'title' => fn() => $this->getTitle($brand),
        ]);
    }

    protected function getTitle(Brand $brand = null): string
    {
        $title = [$brand->name];
        if (request()->query('strona') > 1) {
            $title[] = 'Strona ' . request()->query('strona');
        }
        return implode(' - ', $title);
    }
}