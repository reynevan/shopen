<?php

namespace Shopen\Http\Controllers\Frontend;

use App\Support\ProductSorting\ProductSortRegistry;
use Shopen\Models\Attribute\AttributeOption;
use Shopen\Repositories\Product\ProductAttributeRepository;
use Shopen\Repositories\Product\ProductRepository;
use Shopen\Services\BannerService;
use Shopen\Services\SearchService\SearchService;

readonly class ProductsListController
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

    protected function getActiveFilters($attributesKey = 'code', $optionKey = 'id'): array
    {
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

        return $activeFilters;
    }

}