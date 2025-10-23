<?php

namespace Shopen\Services;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Number;
use Shopen\Models\Attribute\AttributeOption;
use Shopen\Models\Brand\Brand;
use Shopen\Models\Category\Category;
use Shopen\Repositories\Category\CategoryAttributeRepository;
use Shopen\Repositories\Product\ProductAttributeRepository;

class FiltersService
{
    protected array $filters = [];

    protected function initFilters(): void
    {
        if (count($this->filters) > 0) {
            return;
        }
        $query = request()->query();
        if (request()->query('cena-od')) {
            $this->addPriceFromFilter();
            unset($query['cena-od']);
        }
        if (request()->query('cena-do')) {
            $this->addPriceToFilter();
            unset($query['cena-do']);
        }
        if (request()->query('brand')) {
            $this->addBrandFilters();
            unset($query['brand']);
        }
        if (request()->query('kategoria')) {
            $this->addCategoryFilters();
            unset($query['brand']);
        }
        $this->addAttributeFilters($query);
    }

    public function getFullActiveFilters(): array
    {
        $this->initFilters();
        return $this->filters;
    }

    public function getSimpleFilters(): array
    {
        $this->initFilters();
        $filters = [];
        foreach ($this->filters as $key => $filter) {
            if ($key === 'price') {
                foreach ($filter['options'] as $option) {
                    $filters[$option['key']] = $option['value'];
                }
                continue;
            }
            $filters[$filter['key']] = Arr::map($filter['options'], fn($option) => $option['id']);
        }

        return $filters;
    }

    protected function getIdFromSlug($slug): ?string
    {
        $slugParts = explode('-', $slug);
        if (count($slugParts) < 2) {
            return null;
        }
        return $slugParts[0];
    }

    protected function addPriceFromFilter(): void
    {
        $this->filters['price']['options'][] = [
            'key' => 'price_min',
            'slug' => 'cena-od',
            'label' => 'Od ' .  Number::currency(intval(request()->query('cena-od')), '', null, 0),
            'value' => intval(request()->query('cena-od'))
        ];
        $this->filters['price']['name'] = 'Cena';
    }

    protected function addPriceToFilter(): void
    {
        $this->filters['price']['options'][] = [
            'key' => 'price_max',
            'slug' => 'cena-do',
            'label' => 'Od ' .  Number::currency(intval(request()->query('cena-do')), '', null, 0),
            'value' => intval(request()->query('cena-do'))
        ];
        $this->filters['price']['name'] = 'Cena';
    }

    protected function addBrandFilters(): void
    {
        $brandQuery = request()->query('brand', '');
        if (!is_string($brandQuery)) {
            return;
        }
        $brandSlugs = explode(',', $brandQuery);
        $brands = Brand::query()->whereIn('slug', $brandSlugs)->get();
        $this->filters['brand'] = [
            'name' => 'Marka',
            'slug' => 'brand',
            'key' => 'brand',
        ];
        foreach ($brands as $brand) {
            $this->filters['brand']['options'][] = [
                'key' => $brand->slug,
                'name' => 'Marka',
                'label' => $brand->name,
                'id' => $brand->id,
                'slug' => $brand->slug,
            ];
        }
    }

    protected function addCategoryFilters(): void
    {
        $categoryQuery = request()->query('kategoria', '');
        if (!is_string($categoryQuery)) {
            return;
        }
        $slugs = explode(',', $categoryQuery);
        $ids = [];
        foreach ($slugs as $slug) {
            $ids[] = $this->getIdFromSlug($slug);
        }
        $categories = Category::query()->whereIn('id', $ids)->get();
        $names = app(CategoryAttributeRepository::class)->getValues('name', $ids);
        if ($categories->count() > 0) {
            $this->filters['kategoria'] = [
                'key' => 'category',
                'name' => 'Kategoria',
                'slug' => 'kategoria'
            ];
            foreach ($categories as $category) {
                $slug = $category->getFilterSlug($names[$category->id] ?? null);
                $this->filters['kategoria']['options'][] = [
                    'key' => $slug,
                    'name' => 'Kategoria',
                    'label' => $names[$category->id],
                    'id' => $category->id,
                    'slug' => $slug,
                ];
            }
        }
    }

    protected function addAttributeFilters($query): void
    {
        $attributes = app(ProductAttributeRepository::class)->getFilterable();

        foreach ($attributes as $attribute) {
            $slug = $attribute->slug;
            if (!isset($query[$slug]) || !is_string($query[$slug])) {
                continue;
            }
            $values = explode(',', $query[$slug]);
            $options = new Collection($values);
            $this->filters[$attribute->slug] = [
                'key' => $attribute->code,
                'name' => $attribute->name,
                'slug' => $attribute->slug,
                'options' =>  $options
                    ->map(function ($value) {
                        $id = $this->getIdFromSlug($value);
                        if (!$id) {
                            return false;
                        }
                        $option = AttributeOption::query()->find($id);
                        if (!$option) {
                            return false;
                        }
                        return [
                            'label' => $option->value,
                            'slug' => $option->slug,
                            'id' => $option->id,
                        ];
                    })
                    ->filter()
                    ->toArray()
            ];
        }
    }
}