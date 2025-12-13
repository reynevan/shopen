<?php

namespace Shopen\Http\Controllers\Frontend\Category;

use App\Support\ProductSorting\ProductSortRegistry;
use Inertia\Inertia;
use Inertia\Response;
use Shopen\Http\Resources\Category\CategoryResource;
use Shopen\Http\Resources\Product\List\ProductResource;
use Shopen\Models\Category\Category;
use Shopen\Repositories\Product\ProductAttributeRepository;
use Shopen\Repositories\Product\ProductRepository;
use Shopen\Services\BannerService;
use Shopen\Services\FiltersService;
use Shopen\Services\SearchService\SearchService;
use Shopen\Services\ShoppingListService;

readonly class CategoryShowController
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

    public function index(Category $category): Response
    {

        if (request()->query('sort')) {
            $this->productSortRegistry->setDefault(request()->query('sort'));
        }
        $searchResult = $this
            ->searchService
            ->setCategoryId($category->id)
            ->setFilters($this->filtersService->getSimpleFilters())
            ->setSort(request()->query('sort'))
            ->setPage(request()->query('strona', 1))
            ->searchProducts();

        $products = $searchResult->paginatedProducts();

        $category->loadAttributes(['description', 'name', 'is_active']);

        if (!$category->getCustomAttribute('is_active')) {
            abort(404);
        }

        session(['last_category_page' => url()->current()]);

        return Inertia::render('Frontend/Category/Show', [
            'products' => fn() => ProductResource::collection($products),
            'filters' => fn() => [
                'attributes' => $searchResult->getFilters(),
                'priceRange' => $searchResult->getPriceFilters()
            ],
            'activeFilters' => fn() => $this->filtersService->getFullActiveFilters(),
            'banners' => fn() => $this->bannerService->getForCategory($category),
            'category' => fn() => CategoryResource::make($category),
            'activeSort' => fn() => request()->query('sort') ?? $this->productSortRegistry->defaultKey(),
            'sortOptions' => fn() => $this->productSortRegistry->allOptions(),
            'title' => fn() => $this->getTitle($category),
            'shoppingLists' => fn() => $this->shoppingListService
                ->getCurrentUserListsQuery()
                ->withCount('products')
                ->orderBy('name')
                ->get()
        ]);
    }

    protected function getTitle(?Category $category = null): string
    {
        $title = [$category->name];
        if (request()->query('strona') > 1) {
            $title[] = 'Strona ' . request()->query('strona');
        }
        return implode(' - ', $title);
    }


}