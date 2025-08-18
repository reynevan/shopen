<?php

namespace Shopen\Http\Controllers\Frontend;

use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Shopen\Http\Resources\Product\ProductResource;
use Shopen\Models\Attribute\AttributeOption;
use Shopen\Repositories\Product\ProductAttributeRepository;
use Shopen\Repositories\Product\ProductRepository;
use Shopen\Services\SearchService\SearchService;

class SearchController
{
    protected array $filters;
    public function __construct(
        protected ProductRepository $productRepository,
        protected SearchService  $searchService,
        protected ProductAttributeRepository $productAttributeRepository,
    )
    {
        $this->filters = $this->getActiveFilters();
    }

    public function index()
    {
        if (!request()->query('q')) {
            return redirect('/');
        }
        $searchResult = $this
            ->searchService
            ->setSearchQuery(request()->query('q'))
            ->setFilters($this->filters)
            ->setSort(request()->query('sort'))
            ->setPage(request()->query('strona', 1))
            ->searchProducts();
        $products = $searchResult->paginatedProducts();

        return Inertia::render('Frontend/Search/Index', [
            'products' => ProductResource::collection($products),
            'banners' => [],
            'filters' => fn() => [
                'attributes' => $searchResult->getAttributesFilters(),
                'priceRange' => $searchResult->getPriceFilters()
            ],
            'activeFilters' => fn() => $this->getActiveFilters('slug', 'slug'),
            'searchQuery' => request()->input('q')
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