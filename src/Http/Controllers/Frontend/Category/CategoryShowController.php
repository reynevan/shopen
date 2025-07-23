<?php

namespace Shopen\Http\Controllers\Frontend\Category;

use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;
use App\Support\ProductSorting\ProductSortRegistry;
use Shopen\Http\Resources\CategoryResource;
use Shopen\Http\Resources\Product\ProductResource;
use Shopen\Models\Attribute\AttributeOption;
use Shopen\Models\Category\Category;
use Shopen\Repositories\Product\ProductAttributeRepository;
use Shopen\Repositories\Product\ProductRepository;
use Shopen\Services\BannerService;
use Shopen\Services\SearchService\SearchService;

readonly class CategoryShowController
{
    protected array $filters;

    public function __construct(
        protected ProductAttributeRepository $productAttributeRepository,
        protected ProductRepository          $productRepository,
        protected BannerService              $bannerService,
        protected ProductSortRegistry         $productSortRegistry,
        protected SearchService             $searchService,
    )
    {
        $this->filters = $this->getActiveFilters();
    }

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
            ->setPage(request()->query('page', 1))
            ->searchProducts();
        $products = $searchResult->paginatedProducts();

        $category->loadAttributes(['description', 'name'])->load('children');

        return Inertia::render('Frontend/Category/Show', [
            'products' => ProductResource::collection($products),
            'filters' => fn() => [
                'attributes' => $searchResult->getAttributesFilters(),
                'priceRange' => $searchResult->getPriceFilters()
            ],
            'activeFilters' => fn() => $this->getActiveFilters('slug', 'slug'),
            'banners' => fn() => $this->bannerService->getForCategory($category),
            'category' => fn () => CategoryResource::make($category),
            'subcategories' => fn() => CategoryResource::collection($category->children),
            'activeSort' => fn () => request()->query('sort') ?? $this->productSortRegistry->defaultKey(),
            'sortOptions' => fn () => $this->productSortRegistry->allOptions(),
        ]);
    }

    protected function getActiveFilters($attributesKey = 'code', $optionKey = 'id'): array
    {
        $time = microtime(true);
        $attributes = $this->productAttributeRepository->getFilterable();
        $activeFilters = [];
        $params = [];
        foreach (request()->query() as $key => $item) {
            if (is_array($item)) {
                continue;
            }
            $values = explode(',', $item);
            $slugParts = explode('-', $key);
            if (count($slugParts) < 2) {
                continue;
            }
            $params[$slugParts[0]] = array_map(function ($item) use($optionKey) {
                $optionId = explode('-', $item)[0] ?? null;
                if (!$optionId) {
                    return false;
                }
                $option = AttributeOption::query()->find($optionId);
                if (!$option) {
                    return false;
                }
                return $option->{$optionKey};
            }, $values);
            $params[$slugParts[0]]  = array_filter($params[$slugParts[0]]);
        }

        foreach ($attributes as $attribute) {
            if (isset($params[$attribute->id])) {
                $activeFilters[$attribute->{$attributesKey}] = is_array($params[$attribute->id]) ? $params[$attribute->id] : [$params[$attribute->id]];
            }
        }
        if (request()->query('cena_od')) {
            $activeFilters['price_min'] = request()->query('cena_od');
        }
        if (request()->query('cena_do')) {
            $activeFilters['price_max'] = request()->query('cena_do');
        }

        config('app.debug') && Log::debug('[TIME] getActiveFilters: ' . (microtime(true) - $time));
        return $activeFilters;
    }


}