<?php

namespace Shopen\Http\Controllers\Frontend\Brand;

use Inertia\Inertia;
use Inertia\Response;
use Shopen\Http\Controllers\Frontend\ProductsListController;
use Shopen\Http\Resources\Brand\BrandResource;
use Shopen\Http\Resources\Product\ProductResource;
use Shopen\Models\Brand\Brand;

readonly class BrandShowController extends ProductsListController
{
    public function show(Brand $brand): Response
    {
        if (request()->query('sort')) {
            $this->productSortRegistry->setDefault(request()->query('sort'));
        }

        $searchResult = $this
            ->searchService
            ->setBrandId($brand->id)
            ->setFilters($this->filters)
            ->setSort(request()->query('sort'))
            ->setPage(request()->query('strona', 1))
            ->searchProducts();

        $products = $searchResult->paginatedProducts();


        return Inertia::render('Frontend/Brand/Show', [
            'brand' => fn () => BrandResource::make($brand),
            'products' => fn () => ProductResource::collection($products),
            'filters' => fn() => [
                'attributes' => $searchResult->getAttributesFilters(),
                'priceRange' => $searchResult->getPriceFilters()
            ],
            'activeFilters' => fn() => $this->getActiveFilters('slug', 'slug'),
            'banners' => fn () => [],
            'activeSort' => fn () => request()->query('sort') ?? $this->productSortRegistry->defaultKey(),
            'sortOptions' => fn () => $this->productSortRegistry->allOptions(),
            'title' => fn () => $this->getTitle($brand),
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