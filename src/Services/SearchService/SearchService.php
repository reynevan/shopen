<?php

namespace Shopen\Services\SearchService;

use App\Support\ProductSorting\ProductSortRegistry;
use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\ClientBuilder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Shopen\Models\Product\Product;
use Shopen\Repositories\Product\ProductAttributeRepository;
use stdClass;

class SearchService
{
    protected Client $client;

    protected ?int $categoryId = null;
    protected ?int $brandId = null;
    protected array $ids = [];
    protected array $filters = [];
    protected ?string $searchQuery = null;
    protected ?string $sort = null;
    protected ?int $page = null;
    protected ?int $perPage = null;
    protected ?int $limit = null;

    public function __construct(
        protected ProductSortRegistry        $productSortRegistry,
        protected ProductAttributeRepository $productAttributeRepository,
    )
    {
        $this->client = ClientBuilder::create()
            ->setHosts(['localhost:9200'])
            ->build();
    }

    public function setCategoryId($categoryId): static
    {
        $this->categoryId = $categoryId;
        return $this;
    }

    public function setBrandId($brandId): static
    {
        $this->brandId = $brandId;
        return $this;
    }

    public function setIds(array $ids): static
    {
        $this->ids = $ids;
        return $this;
    }

    public function setFilters(array $filters): static
    {
        $this->filters = $filters;
        return $this;
    }

    public function setSearchQuery(?string $searchQuery): static
    {
        $this->searchQuery = $searchQuery;
        return $this;
    }

    public function setSort(?string $sort): static
    {
        $this->sort = $sort;
        return $this;
    }

    public function setPage(?int $page): static
    {
        $this->page = $page;
        return $this;
    }

    public function setPerPage(?int $perPage): static
    {
        $this->perPage = $perPage;
        return $this;
    }

    public function setLimit(?int $limit): static
    {
        $this->limit = $limit;
        return $this;
    }

    public function getProducts()
    {
        $filters = [];

        if ($this->categoryId) {
            $filters[] = ['term' => [ 'category_id' => $this->categoryId ]];
            $filters[] = ['terms' => [ 'visibility' => [Product::VISIBILITY_ALL, Product::VISIBILITY_CATEGORY] ]];
        }

        if ($this->brandId) {
            $filters[] = ['term' => ['brand_id' => $this->brandId]];
        }

        if ($this->ids) {
            $filters[] = ['terms' => ['id' => $this->ids]];
        }

        $query = count($filters)
            ? ['bool' => ['filter' => $filters]]
            : ['match_all' => new \stdClass()];

        $params = [
            'index' => 'shopen_products',
            'body' => ['query' => $query],
        ];

        if ($this->limit) {
            $params['body']['size'] = $this->limit;
        }
        $result = $this->client->search($params)->asArray();

        return new SearchServiceResult($result);
    }

    public function searchProducts(): SearchServiceResult
    {
        $params = [
            'index' => 'shopen_products',
            'body' => [
                'query' => [
                    'bool' => [
                        'filter' => $this->buildFiltersArray($this->filters),
                    ],
                ],
                'aggs' => $this->buildAggregationsArray(),
            ],
        ];

        if ($this->page) {
            $perPage = $this->perPage ?? config('shopen.product.per_page');
            $from = (max(intval($this->page), 1) - 1) * $perPage;
            $params['body']['from'] = $from;
            $params['body']['size'] = $perPage;
        } elseif ($this->limit) {
            $params['body']['size'] = $this->limit;
        }

        if (config('shopen.product.show_out_of_stock')) {
            $params['body']['sort'][] = ['in_stock' => 'desc'];
        }

        $sorter = $this->productSortRegistry->findByKey($this->sort);
        if ($sorter) {
            $params['body']['sort'][] = $sorter->build();
        }

        $this->addSearchFilter($params);

        $result = $this->client->search($params)->asArray();

        return new SearchServiceResult($result);
    }

    public function searchCategories(): SearchServiceResult
    {
        $params = [
            'index' => 'shopen_categories',
            'body' => [
                'query' => [
                    'bool' => [
                        'must' => $this->getSearchQueryFilter()
                    ]
                ]
            ],
        ];

        if ($this->limit) {
            $params['body']['size'] = $this->limit;
        }

        $result = $this->client->search($params)->asArray();

        return new SearchServiceResult($result);
    }

    protected function buildFiltersArray(array $filters, ?string $exclude = null): array
    {
        $queryFilters = [];

        $this->addCategoryFilter($queryFilters);
        $this->addBrandFilter($queryFilters);
        $this->addAttributeFilters($queryFilters, $filters, $exclude);
        $this->addPriceRangeFilter($queryFilters, $filters);
        $this->addVisibilityFilter($queryFilters);

        return $queryFilters;
    }

    private function addBrandFilter(array &$queryFilters): void
    {
        if ($this->brandId) {
            $queryFilters[] = ['term' => ['brand_id' => $this->brandId]];
        }
    }

    private function addSearchFilter(array &$params): void
    {
        if (!$this->searchQuery) {
            return;
        }

        $params['body']['query']['bool']['must'] = $this->getSearchQueryFilter();
    }

    private function addCategoryFilter(array &$queryFilters): void
    {
        if ($this->categoryId) {
            $queryFilters[] = ['term' => ['category_id' => $this->categoryId]];
        }
    }

    private function addAttributeFilters(array &$queryFilters, array $allFilters, ?string $excludeAttribute): void
    {
        $attributeFilters = Arr::except($allFilters, ['price_min', 'price_max']);

        foreach ($attributeFilters as $attribute => $values) {
            if ($attribute === $excludeAttribute) {
                continue;
            }
            $queryFilters[] = ['terms' => [$attribute => $values]];
        }
    }

    private function addVisibilityFilter(array &$queryFilters): void
    {
        $queryFilters[] = ['terms' => ['visibility' => [
            Product::VISIBILITY_ALL,
            Product::VISIBILITY_SEARCH
        ]]];
    }

    private function addPriceRangeFilter(array &$queryFilters, array $allFilters): void
    {
        $range = [];

        if (isset($allFilters['price_min'])) {
            $range['gte'] = $allFilters['price_min'];
        }
        if (isset($allFilters['price_max'])) {
            $range['lte'] = $allFilters['price_max'];
        }

        if (!empty($range)) {
            $queryFilters[] = ['range' => ['price' => $range]];
        }
    }

    protected function buildAggregationsArray(): array
    {
        $aggregations = [];

        foreach ($this->productAttributeRepository->getFilterable() as $attribute) {
            $hasFilter = in_array($attribute->code, array_keys($this->filters));

            if ($hasFilter) {
                $aggregation = [
                    'global' => new stdClass(),
                    'aggs' => [
                        'filters' => [
                            'filter' => [
                                'bool' => [
                                    'filter' => $this->buildFiltersArray($this->filters, $attribute->code),
                                ],
                            ],
                            'aggs' => [
                                'count' => ['terms' => ['field' => $attribute->code]],
                            ],
                        ],
                    ],
                ];
                if ($this->searchQuery) {
                    $aggregation['aggs']['filters']['filter']['bool']['must'] = $this->getSearchQueryFilter();
                }
            } else {
                $aggregation = [
                    'filter' => [
                        'bool' => [
                            'filter' => [],
                        ],
                    ],
                    'aggs' => [
                        'count' => ['terms' => ['field' => $attribute->code]],
                    ],
                ];
                if ($this->searchQuery) {
                    $aggregation['filter']['bool']['must'] = $this->getSearchQueryFilter();
                }
            }

            $aggregations[$attribute->code] = $aggregation;
        }

        $aggregations['price_stats'] = [
            'global' => new stdClass(),
            'aggs' => [
                'filters' => [
                    'filter' => [
                        'bool' => [
                            'filter' => $this->buildFiltersArray(
                                Arr::except($this->filters, ['price_min', 'price_max', 'sort'])
                            ),
                        ],
                    ],
                    'aggs' => [
                        'min_price' => ['min' => ['field' => 'price']],
                        'max_price' => ['max' => ['field' => 'price']],
                    ],
                ],
            ],
        ];

        if ($this->searchQuery) {
            $aggregations['price_stats']['aggs']['filters']['filter']['bool']['must'] = $this->getSearchQueryFilter();
        }

        return $aggregations;
    }

    private function getSearchQueryFilter(): array
    {
        return [
            'match' => [
                'name.autocomplete' => [
                    'query' => $this->searchQuery,
                    'fuzziness' => 'AUTO',
                ],
            ],
        ];
    }
}
