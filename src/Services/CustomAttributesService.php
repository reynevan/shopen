<?php

namespace Shopen\Services;

use Illuminate\Database\Eloquent\Collection;
use Shopen\Models\Category\Category;
use Shopen\Models\Interfaces\HasCustomAttributesInterface;
use Shopen\Models\Product\Product;
use Shopen\Repositories\Attribute\AttributeRepository;
use Shopen\Repositories\Category\CategoryAttributeRepository;
use Shopen\Repositories\Product\ProductAttributeRepository;

class CustomAttributesService
{
    protected array $preloadedCategoryAttributes = [];

    public function __construct(
        private readonly ProductAttributeRepository $productAttributeRepository,
        private readonly CategoryAttributeRepository $categoryAttributeRepository,
    )
    {}

    public function loadAllAttributes(HasCustomAttributesInterface $entity): void
    {
        $attributeRepository = $this->getAttributeRepository($entity);
        if (!$attributeRepository) {
            return;
        }
        $attributes = $attributeRepository->getAll();
        $entities = new Collection();
        $entities->push($entity);

        $this->loadAttributes($entities, $attributes);
    }

    public function loadAllAttributesToCollection($entities): void
    {
        $attributeRepository = $this->getAttributeRepository($entities->first());
        if (!$attributeRepository) {
            return;
        }
        $attributes = $attributeRepository->getAll();

        $this->loadAttributes($entities, $attributes);
    }

    public function loadUsedInListAttributesToCollection($entities): void
    {
        $attributeRepository = $this->getAttributeRepository($entities->first());
        if (!$attributeRepository) {
            return;
        }
        $attributes = $attributeRepository->getUsedInList();

        $this->loadAttributes($entities, $attributes);
    }

    public function preloadCategoryAttributes(): void
    {
        $attributes = $this->categoryAttributeRepository->getAll();

        foreach ($attributes as $attribute) {
            if ($attribute->frontend_type === 'multiselect') {
                $values = $attribute->getValueModel()::query()
                    ->where('attribute_id', $attribute->id)
                    ->get()
                    ->groupBy('entity_id')
                    ->map(function ($items) {
                        return $items->pluck('value')->all();
                    })
                    ->toArray();
            } else {
                $values = $attribute->getValueModel()::query()
                    ->where('attribute_id', $attribute->id)
                    ->get()
                    ->pluck('value', 'entity_id')
                    ->toArray();
            }
            $this->preloadedCategoryAttributes[$attribute->code] = $values;
        }
    }

    public function setPreloadedAttributes(HasCustomAttributesInterface $entity): void
    {
        if (!$this->preloadedCategoryAttributes) {
            return;
        }
        foreach ($this->preloadedCategoryAttributes as $code => $values) {
            $entity->setCustomAttribute($code, $values[$entity->id] ?? null);
        }
    }

    protected function loadAttributes($entities, $attributes)
    {
        foreach ($attributes as $attribute) {
            if ($attribute->frontend_type === 'multiselect') {
                $values = $attribute->getValueModel()::query()
                    ->whereIn('entity_id', $entities->pluck('id'))
                    ->where('attribute_id', $attribute->id)
                    ->get()
                    ->groupBy('entity_id')
                    ->map(function ($items) {
                        return $items->pluck('value')->all();
                    })
                    ->toArray();
            } else {
                $values = $attribute->getValueModel()::query()
                    ->whereIn('entity_id', $entities->pluck('id'))
                    ->where('attribute_id', $attribute->id)
                    ->get()
                    ->pluck('value', 'entity_id')
                    ->toArray();
            }
            foreach ($entities as $entity) {
                $entity->setCustomAttribute($attribute->code, $values[$entity->id] ?? null);
            }
        }
    }

    protected function getAttributeRepository(HasCustomAttributesInterface $entity): AttributeRepository|null
    {
        if ($entity->getEntityType() === Category::ENTITY_TYPE) {
            return $this->categoryAttributeRepository;
        } elseif ($entity->getEntityType() === Product::ENTITY_TYPE) {
            return $this->productAttributeRepository;
        }
        return null;
    }
}