<?php

namespace Shopen\Services\SearchService;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Shopen\Http\Resources\Attribute\FilterResource;
use Shopen\Models\Brand\Brand;
use Shopen\Models\Category\Category;
use Shopen\Models\Product\Product;
use Shopen\Pagination\LengthAwarePaginator;
use Shopen\Repositories\Brand\BrandRepository;
use Shopen\Repositories\Category\CategoryAttributeRepository;
use Shopen\Repositories\Category\CategoryRepository;
use Shopen\Repositories\Product\ProductAttributeRepository;

class SearchServiceResult
{
    public function __construct(protected array $searchResult)
    {}

    protected function parseProducts($sortIds = null): Collection
    {
        $resultIds = Arr::pluck($this->searchResult['hits']['hits'], '_source.id');
        if (!$resultIds || !count($resultIds)) {
            return new Collection();
        }
        $sources = Arr::pluck($this->searchResult['hits']['hits'], '_source', '_source.id');
        $ids =  implode(',', $sortIds ?? $resultIds);
        $products = Product::query()
            ->with(['price', 'urlRewrite', 'brand'])
            ->whereIn('id', $resultIds)
            ->orderByRaw("FIELD(id, $ids)")
            ->get();

        $reviewsEnabled = config('shopen.product.reviews.enabled');
        foreach ($products as $product) {
            $source = $sources[$product->id] ?? [];
            foreach ($source['list_attributes'] ?? [] as $code => $value) {
                $product->setCustomAttribute($code, $value);
            }
            if ($reviewsEnabled) {
                $product->rating = round((float)$source['rating'] ?? 0, 2);
                $product->reviews_count = $source['reviews_count'] ?? 0;
            }
            $product->images = $source['thumbnail_url'];
        }
        return $products;
    }

    protected function parseCategories($sortIds = null): Collection
    {
        $resultIds = Arr::pluck($this->searchResult['hits']['hits'], '_source.id');
        if (!$resultIds || !count($resultIds)) {
            return new Collection();
        }
        $sources = Arr::pluck($this->searchResult['hits']['hits'], '_source', '_source.id');

        $categories = Category::query()
            ->whereIn('id', $resultIds)
            ->when($sortIds, function (Builder $query) use ($sortIds) {
                $query->orderByRaw("FIELD(id, $sortIds)");
            })
            ->get();

        foreach ($categories as $category) {
            $source = $sources[$category->id] ?? [];
            $category->url = config('app.url') . '/' .  $source['url_key'];
            $category->setCustomAttribute('name', $source['name']);
        }

        return $categories;
    }

    public function getPriceFilters(): array
    {
        $min = $this->searchResult['aggregations']['price_stats']['filters']['min_price']['value'] ?? 0;
        $max = $this->searchResult['aggregations']['price_stats']['filters']['max_price']['value'] ?? 0;
        return [
            'min' => $min ? floor($min / 10) * 10 : 0,
            'max' => $max ? ceil($max / 10) * 10 : 0
        ];
    }

    public function getFilters(): array
    {
        $filters = [];
        $categoriesFilters = $this->getCategoriesFilters();
        if (count($categoriesFilters)) {
            $filters[] = [
                'name' => 'Kategoria',
                'code' => 'category',
                'slug' => 'kategoria',
                'options' => $categoriesFilters
            ];
        }

        $brandFilters = $this->getBrandFilters();
        if (count($brandFilters)) {
            $filters[] = [
                'name' => 'Marka',
                'code' => 'brand',
                'slug' => 'brand',
                'options' => $brandFilters
            ];
        }

        foreach ($this->getAttributesFilters() as $attribute) {
            $filters[] = [
                'name' => $attribute->name,
                'code' => $attribute->code,
                'slug' => $attribute->slug,
                'options' => $attribute->options->map(fn($option) => [
                    'id' => $option->id,
                    'value' => $option->value,
                    'slug' => $option->slug,
                    'count' => $option->count ?? 0,
                    'color' => $option->color
                ])
                ->values()
                ->toArray()
            ];
        }

        return $filters;
    }

    public function paginatedProducts(): LengthAwarePaginator
    {
        return new LengthAwarePaginator(
            $this->parseProducts(),
            $this->searchResult['hits']['total']['value'],
            config('shopen.product.per_page'),
            request()->strona,
            [
                'path' => request()->url(),
                'pageName' => 'strona',
                'query' => request()->query(),
                'onEachSide' => 0
            ]
        );
    }

    public function products(): Collection
    {
        return $this->parseProducts();
    }

    public function categories(): Collection
    {
        return $this->parseCategories();
    }

    public function sortedProducts($sortIds): Collection
    {
        return $this->parseProducts($sortIds);
    }

    protected function getBrandFilters()
    {
        $brandsCount = [];
        foreach ($this->searchResult['aggregations']['brand']['filters']['count']['buckets'] ?? [] as $bucket) {
            $brandsCount[$bucket['key']] = $bucket['doc_count'];
        }

        $brands = app(BrandRepository::class)->getAllByIds(array_keys($brandsCount));

        return $brands->map(function (Brand $brand) use ($brandsCount) {
            return [
                'id' => $brand->id,
                'slug' => $brand->slug,
                'name' => $brand->name,
                'value' => $brand->name,
                'count' => $brandsCount[$brand->id] ?? 0
            ];
        })->toArray();
    }

    protected function getCategoriesFilters()
    {
        $categoriesCount = [];
        foreach ($this->searchResult['aggregations']['category']['filters']['count']['buckets'] ?? [] as $bucket) {
            $categoriesCount[$bucket['key']] = $bucket['doc_count'];
        }

        $ids = array_keys($categoriesCount);
        $categories = app(CategoryRepository::class)->getAllByIds($ids);
        $names = app(CategoryAttributeRepository::class)->getValues('name', $ids);

        return $categories
            ->map(function (Category $category) use ($categoriesCount, $names) {
                return [
                    'id' => $category->id,
                    'slug' => $category->getFilterSlug($names[$category->id] ?? null),
                    'name' => $names[$category->id] ?? null,
                    'value' => $category->name,
                    'count' => $categoriesCount[$category->id] ?? 0
                ];
            })
            ->filter(fn($category) => ($category['slug'] ?? null) && ($category['name'] ?? null))
            ->sort(fn($a, $b) => mb_strtolower($a['name']) > mb_strtolower($b['name']) ? 1 : -1)
            ->values()
            ->toArray();
    }

    protected function getAttributesFilters()
    {
        $attributeOptionsCount = [];
        foreach ($this->searchResult['aggregations'] as $attrCode => $aggregation) {
            foreach ($aggregation['count']['buckets'] ?? $aggregation['filters']['count']['buckets'] ?? [] as $bucket) {
                $attributeOptionsCount[$attrCode][$bucket['key']] = $bucket['doc_count'];
            }
        }

        $attributes = app(ProductAttributeRepository::class)->getFilterable();
        foreach ($attributes as $attribute) {
            foreach ($attribute->options as $option) {
                $option->count = $attributeOptionsCount[$attribute->code][$option->id] ?? 0;
            }

            $attribute->options = $attribute
                ->options
                ->sortBy('value')
                ->filter(fn($option) => $option->count > 0);
        }

        return $attributes->filter(fn($attr) => $attr->options->sum('count') > 0);
    }
}