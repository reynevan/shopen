<?php

namespace Shopen\Http\Controllers\Frontend\Category;

use Inertia\Inertia;
use Inertia\Response;
use Shopen\Http\Controllers\Frontend\ProductsListController;
use Shopen\Http\Resources\Category\BaseCategoryResource;
use Shopen\Http\Resources\Category\CategoryResource;
use Shopen\Http\Resources\Product\ProductResource;
use Shopen\Models\Category\Category;

readonly class CategoryShowController extends ProductsListController
{
    public function index(Category $category): Response
    {
        if (request()->query('sort')) {
            $this->productSortRegistry->setDefault(request()->query('sort'));
        }
        $searchResult = $this
            ->searchService
            ->setCategoryId($category->id)
            ->setFilters($this->filters)
            ->setSort(request()->query('sort'))
            ->setPage(request()->query('strona', 1))
            ->searchProducts();
        $products = $searchResult->paginatedProducts();

        $category->loadAttributes(['description', 'name']);

        return Inertia::render('Frontend/Category/Show', [
            'products' => fn() => ProductResource::collection($products),
            'filters' => fn() => [
                'attributes' => $searchResult->getAttributesFilters(),
                'priceRange' => $searchResult->getPriceFilters()
            ],
            'activeFilters' => fn() => $this->getActiveFilters('slug', 'slug'),
            'banners' => fn() => $this->bannerService->getForCategory($category),
            'category' => fn () => CategoryResource::make($category),
            'subcategories' => fn() => $this->getSubcategories($category),
            'activeSort' => fn () => request()->query('sort') ?? $this->productSortRegistry->defaultKey(),
            'sortOptions' => fn () => $this->productSortRegistry->allOptions(),
            'title' => fn () => $this->getTitle($category),
        ]);
    }

    protected function getSubcategories(Category $category)
    {
        $searchResult = $this
            ->searchService
            ->setCategoryId($category->id)
            ->setSort(request()->query('sort'))
            ->getCategories();
        return BaseCategoryResource::collection($searchResult->categories());
    }

    protected function getTitle(Category $category = null): string
    {
        $title = [$category->name];
        if (request()->query('strona') > 1) {
            $title[] = 'Strona ' . request()->query('strona');
        }
        return implode(' - ', $title);
    }




}