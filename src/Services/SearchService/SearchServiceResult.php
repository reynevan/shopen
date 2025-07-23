<?php

namespace Shopen\Services\SearchService;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Shopen\Http\Resources\Attribute\FilterResource;
use Shopen\Models\Product\Product;
use Shopen\Repositories\Product\ProductAttributeRepository;

class SearchServiceResult
{
    public function __construct(protected array $searchResult)
    {}

    protected function parseProducts(): Collection
    {
        $ids = Arr::pluck($this->searchResult['hits']['hits'], '_source.id');
        $sources = Arr::pluck($this->searchResult['hits']['hits'], '_source', '_source.id');
        $sortIds = implode(',', $ids);
        $products = Product::query()
            ->with('price')
            ->whereIn('id', $ids)
            ->orderByRaw("FIELD(id, $sortIds)")
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
            $product->setCustomAttribute('images', $images);
        }

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
            request()->page,
            [
                'path' => request()->url()
            ]
        );
    }

    public function products(): Collection
    {
        return $this->parseProducts();
    }

    public function getAttributesFilters(): AnonymousResourceCollection
    {
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
        return FilterResource::collection($attributes);
    }
}