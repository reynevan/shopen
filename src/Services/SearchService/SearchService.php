<?php

namespace Shopen\Services\SearchService;

use App\Support\ProductSorting\ProductSortRegistry;
use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\ClientBuilder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Shopen\Models\Attribute\Attribute;
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
    protected ?bool $new = null;

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

    public function setNew($value): static
    {
        $this->new = $value;
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
        }

        if ($this->brandId) {
            $filters[] = ['term' => ['brand_id' => $this->brandId]];
        }

        if ($this->ids) {
            $filters[] = ['terms' => ['id' => $this->ids]];
        }

        if ($this->new) {
            $filters[] = [
                'term' => [
                    'is_new' => true,
                ],
            ];
            $filters[] = [
                'bool' => [
                    'should' => [
                        ['range' => ['is_new_to' => ['gte' => 'now/d']]],
                        ['bool' => ['must_not' => ['exists' => ['field' => 'is_new_to']]]],
                    ],
                    'minimum_should_match' => 1,
                ],
            ];
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

    public function getCategories()
    {
        $filters = [];

        if ($this->categoryId) {
            $filters[] = ['term' => [ 'parent_id' => $this->categoryId ]];
            $filters[] = ['range' => [ 'products_count' => ['gt' => 0] ]];
        }

        if ($this->ids) {
            $filters[] = ['terms' => ['id' => $this->ids]];
        }

        $query = count($filters)
            ? ['bool' => ['filter' => $filters]]
            : ['match_all' => new \stdClass()];

        $params = [
            'index' => 'shopen_categories',
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

        $params['body']['sort'][] = ['_score' => 'desc'];

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
        if ($exclude !== 'brand') {
            $this->addBrandsFilter($queryFilters, $filters);
        }
        if ($exclude !== 'category') {
            $this->addCategoriesFilter($queryFilters, $filters);
        }
        $this->addAttributeFilters($queryFilters, $filters, $exclude);
        $this->addPriceRangeFilter($queryFilters, $filters);

        return $queryFilters;
    }

    private function addBrandsFilter(array &$queryFilters, $filters): void
    {
        if ($filters['brand'] ?? false) {
            $queryFilters[] = ['terms' => ['brand_id' => array_values($filters['brand'])]];
        }
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

    private function addCategoriesFilter(array &$queryFilters, $filters): void
    {
        if ($filters['category'] ?? false) {
            $queryFilters[] = ['terms' => ['category_id' => array_values($filters['category'])]];
        }
    }

    private function addAttributeFilters(array &$queryFilters, array $allFilters, ?string $excludeAttribute): void
    {
        $attributeFilters = Arr::except($allFilters, ['price_min', 'price_max', 'brand', 'category']);

        foreach ($attributeFilters as $attribute => $values) {
            if ($attribute === $excludeAttribute) {
                continue;
            }
            $queryFilters[] = ['terms' => [$attribute => $values]];
        }
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
            $aggregations[$attribute->code] = $this->getAttributeAggregation($attribute);
        }

        $aggregations['brand'] = $this->getBrandsAggregations();

        $aggregations['price_stats'] = $this->getPriceAggregations();

        $aggregations['category'] = $this->getCategoryAggregations();

        return $aggregations;
    }

    private function getCategoryAggregations(): array
    {
        $aggregation = [
            'global' => new stdClass(),
            'aggs' => [
                'filters' => [
                    'filter' => [
                        'bool' => [
                            'filter' => $this->buildFiltersArray(
                                Arr::except($this->filters, ['category'])
                            ),
                        ],
                    ],
                    'aggs' => [
                        'count' => ['terms' => ['field' => 'category_id']],
                    ],
                ],
            ],
        ];

        if ($this->searchQuery) {
            $aggregation['aggs']['filters']['filter']['bool']['must'] = $this->getSearchQueryFilter();
        }

        return $aggregation;
    }

    private function getAttributeAggregation(Attribute $attribute): array
    {
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

        return $aggregation;
    }

    private function getPriceAggregations(): array
    {
        $aggregation = [
            'global' => new stdClass(),
            'aggs' => [
                'filters' => [
                    'filter' => [
                        'bool' => [
                            'filter' => $this->buildFiltersArray(
                                Arr::except($this->filters, ['price_min', 'price_max'])
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
            $aggregation['aggs']['filters']['filter']['bool']['must'] = $this->getSearchQueryFilter();
        }

        return $aggregation;
    }

    private function getBrandsAggregations(): array
    {
        $aggregation = [
            'global' => new stdClass(),
            'aggs' => [
                'filters' => [
                    'filter' => [
                        'bool' => [
                            'filter' => $this->buildFiltersArray(
                                Arr::except($this->filters, ['brand'])
                            ),
                        ],
                    ],
                    'aggs' => [
                        'count' => ['terms' => ['field' => 'brand_id']],
                    ],
                ],
            ],
        ];

        if ($this->searchQuery) {
            $aggregation['aggs']['filters']['filter']['bool']['must'] = $this->getSearchQueryFilter();
        }

        return $aggregation;
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
