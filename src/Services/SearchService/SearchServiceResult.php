<?php

namespace Shopen\Services\SearchService;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Shopen\Http\Resources\Attribute\FilterResource;
use Shopen\Models\Product\Product;
use Shopen\Repositories\Product\ProductAttributeRepository;

class SearchServiceResult
{
    public function __construct(protected array $searchResult)
    {}

    protected function parseProducts($sortIds = null): Collection
    {
        $time = microtime(true);
        $resultIds = Arr::pluck($this->searchResult['hits']['hits'], '_source.id');
        $sources = Arr::pluck($this->searchResult['hits']['hits'], '_source', '_source.id');
        $ids =  implode(',', $sortIds ?? $resultIds);
        $products = Product::query()
            ->with('price')
            ->whereIn('id', $resultIds)
            ->orderByRaw("FIELD(id, $ids)")
            ->get();

        foreach ($products as $product) {
            $source = $sources[$product->id] ?? [];
            foreach ($source['list_attributes'] ?? [] as $code => $value) {
                $product->setCustomAttribute($code, $value);
            }
            $imagesCount = max(count($source['thumbnail_url']), count($source['mobile_thumbnail_url']));
            $images = [];
            for ($i = 0; $i < $imagesCount; $i++) {
                $images[] = [
                    'thumbnail' => $source['thumbnail_url'][$i] ?? null,
                    'mobile_thumbnail' => $source['mobile_thumbnail_url'][$i] ?? null,
                ];
            }
            $product->rating = round((float)$source['rating'] ?? 0, 2);
            $product->reviews_count = $source['reviews_count'] ?? 0;
            $product->setCustomAttribute('images', $images);
        }

        config('app.debug') && Log::debug('[TIME] searchProducts: ' . (microtime(true) - $time));
        return $products;
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

    public function paginatedProducts(): LengthAwarePaginator
    {
        return new LengthAwarePaginator(
            $this->parseProducts(),
            $this->searchResult['hits']['total']['value'],
            32,
            request()->strona,
            [
                'path' => request()->url(),
                'pageName' => 'strona'
            ]
        );
    }

    public function products(): Collection
    {
        return $this->parseProducts();
    }

    public function sortedProducts($sortIds): Collection
    {
        return $this->parseProducts($sortIds);
    }

    public function getAttributesFilters(): AnonymousResourceCollection
    {
        $time = microtime(true);
       $attributeOptionsCount = [];
        foreach ($this->searchResult['aggregations'] as $aggregation) {
            foreach ($aggregation['count']['buckets'] ?? $aggregation['filters']['count']['buckets'] ?? [] as $bucket) {
                $attributeOptionsCount[$bucket['key']] = $bucket['doc_count'];
            }
        }

        $attributes = app(ProductAttributeRepository::class)->getFilterable();
        foreach ($attributes as $attribute) {
            foreach ($attribute->options as $option) {
                $option->count = $attributeOptionsCount[$option->id] ?? 0;
            }

            $attribute->options = $attribute
                ->options
                ->sortBy('value')
                ->filter(fn($option) => $option->count > 0);
        }
        $attributes = FilterResource::collection($attributes);
        config('app.debug') && Log::debug('[TIME] getAttributesFilters: ' . (microtime(true) - $time));
        return $attributes;
    }
}