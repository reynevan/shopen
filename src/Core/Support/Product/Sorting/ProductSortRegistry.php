<?php

namespace Shopen\Core\Support\Product\Sorting;


use Illuminate\Container\Container;
use Illuminate\Support\Facades\Cookie;

class ProductSortRegistry
{
    const SORT_COOKIE_NAME = 'product_sort_preference';

    /** @var ProductSorter[] */
    protected array $sorters = [];

    public function __construct()
    {
        $container = Container::getInstance();

        foreach ($this->getSorters() as $sorter) {
            $this->sorters[] = $container->make($sorter);
        };
    }

    protected function getSorters(): array
    {
        return [];
    }

    public function defaultKey(): string
    {
        return Cookie::get(self::SORT_COOKIE_NAME, 'popularity');
    }

    public function getDefaultSorter(): ?ProductSorter
    {
        return $this->findByKey($this->defaultKey(), false);
    }

    public function allOptions(): array
    {
        $options = [];

        foreach ($this->sorters as $sorter) {
            foreach ($sorter->keys() as $key) {
                $options[] = [
                    'key' => $key,
                    'label' => $sorter->label($key)
                ];
            }
        }

        return $options;
    }

    public function findByKey(?string $key, $defaultOnNotFound = true): ?ProductSorter
    {
        if (!$key) {
            return $defaultOnNotFound ? $this->getDefaultSorter() : null;
        }
        foreach ($this->sorters as $sorter) {
            if (in_array($key, $sorter->keys(), true)) {
                $sorter->setKey($key);
                return $sorter;
            }
        }

        return $defaultOnNotFound ? $this->getDefaultSorter() : null;
    }

    public function setDefault($key)
    {
        foreach ($this->sorters as $sorter) {
            if (in_array($key, $sorter->keys(), true)) {
                Cookie::queue(Cookie::forever(self::SORT_COOKIE_NAME, $key));
            }
        }
    }
}
