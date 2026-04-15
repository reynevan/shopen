<?php

namespace Shopen\Services\Import\Magento;

use Illuminate\Support\Facades\DB;
use Shopen\Models\Category\Category;

trait CategoriesImporter
{

    public function importCategories()
    {
        $this->loadCategoryEntityTypeId();
        $this->loadCategoryAttributeIds();

        $categories = DB::connection($this->connectionName)
            ->table('catalog_category_entity')
            ->select([
                'entity_id',
                'parent_id',
                'path',
                'level',
                'children_count',
                'created_at',
                'updated_at',
            ])
            ->orderBy('level')
            ->orderBy('position')
            ->get()
            ->toArray();

        $categoriesArray = array_map(fn($item) => (array) $item, $categories);

        // Pobierz atrybuty dla każdej kategorii
        foreach ($categoriesArray as &$category) {
            $category['name'] = $this->getCategoryAttributeValue($category['entity_id'], 'name');
            $category['description'] = $this->getCategoryAttributeValue($category['entity_id'], 'description');
            $category['url_key'] = $this->getCategoryAttributeValue($category['entity_id'], 'url_key');
            $category['meta_description'] = $this->getCategoryAttributeValue($category['entity_id'], 'meta_description');
            $category['include_in_menu'] = (bool) $this->getCategoryAttributeValue($category['entity_id'], 'include_in_menu');
            $category['is_active'] = (bool) $this->getCategoryAttributeValue($category['entity_id'], 'is_active');
            $category['children'] = [];
        }
        $categoriesData = $this->buildCategoryTree($categoriesArray);

        foreach ($categoriesData as $categoryRow) {
           $this->importCategory($categoryRow, null);
        }
    }

    protected function importCategory($data, $parentId)
    {
        $params = ['parent_id' => $parentId];
        $category = Category::query()
            ->where($params)
            ->filterByAttribute('name', $data['name'])
            ->firstOrNew($params);
        if (!in_array($data['name'], ['Root Catalog', 'Default Category'])) {
            $category->name = $data['name'];
            $category->setCustomAttribute('name', $data['name']);
            $category->setCustomAttribute('description', $data['description']);
            $category->setCustomAttribute('display_in_menu', $data['include_in_menu']);
            $category->level = max($data['level'] - 2, 0);
            $category->is_active = $data['is_active'];
            $category->parent_id = $parentId;
            $category->save();
            $category->generateUrlRewrite($data['url_key']);
            $category->createOrUpdateSeoForStore(1, [
                'seo_description' => $data['meta_description'],
            ]);
            $category->updatePath();
        }
        foreach ($data['children'] as $childRow) {
            $this->importCategory($childRow, $category->id);
        }
    }

    private function loadCategoryEntityTypeId(): void
    {
        $this->categoryEntityTypeId = DB::connection($this->connectionName)
            ->table('eav_entity_type')
            ->where('entity_type_code', 'catalog_category')
            ->value('entity_type_id');
    }

    private function loadCategoryAttributeIds(): void
    {
        $attributes = ['name', 'description', 'url_key', 'meta_description', 'include_in_menu', 'is_active'];

        $results = DB::connection($this->connectionName)
            ->table('eav_attribute')
            ->where('entity_type_id', $this->categoryEntityTypeId)
            ->whereIn('attribute_code', $attributes)
            ->select('attribute_id', 'attribute_code', 'backend_type')
            ->get();

        foreach ($results as $result) {
            $this->attributeIds[$result->attribute_code] = [
                'id' => $result->attribute_id,
                'type' => $result->backend_type,
            ];
        }
    }

    private function getCategoryAttributeValue(int $entityId, string $attributeCode): mixed
    {
        if (!isset($this->attributeIds[$attributeCode])) {
            return null;
        }

        $attributeInfo = $this->attributeIds[$attributeCode];
        $backendType = $attributeInfo['type'];
        $attributeId = $attributeInfo['id'];

        $tableName = "catalog_category_entity_{$backendType}";

        $value = DB::connection($this->connectionName)
            ->table($tableName)
            ->where('attribute_id', $attributeId)
            ->where('entity_id', $entityId)
            ->where('store_id', 0)
            ->value('value');

        return $value;
    }

    private function buildCategoryTree(array $categories): array
    {
        $categoryMap = [];
        $rootCategories = [];

        // Indeksuj kategorie po ID
        foreach ($categories as $category) {
            $categoryMap[$category['entity_id']] = $category;
        }

        // Buduj drzewo
        foreach ($categoryMap as $entityId => &$category) {
            $parentId = $category['parent_id'];

            if ($parentId && isset($categoryMap[$parentId])) {
                $categoryMap[$parentId]['children'][] = &$category;
            } else {
                $rootCategories[] = &$category;
            }
        }

        return $rootCategories;
    }
}