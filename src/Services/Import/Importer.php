<?php

namespace Shopen\Services\Import;

use Shopen\Models\Attribute\Attribute;
use Shopen\Models\Attribute\AttributeOption;

class Importer
{
    public function importAttribute($attributeData, $entityType): void
    {
        $attribute = Attribute::query()
            ->where('code', $attributeData['code'])
            ->where('entity_type', $entityType)
            ->first();
        if ($attribute) {
            return;
        }
        $data = [
            'name' => $attributeData['name'],
            'code' => $attributeData['code'],
            'entity_type' => $entityType,
            'backend_type' => $attributeData['backend_type'],
            'frontend_type' => $attributeData['frontend_type'],
            'is_filterable' => true,
            'is_searchable' => true,
            'is_system' => $attributeData['is_system'] ?? false,
            'is_required' => $attributeData['is_required'] ?? false,
            'is_used_in_list' => $attributeData['in_list'] ?? false,
            'is_visible_in_details' => $attributeData['in_details'] ?? true,
            'is_used_on_product_page' => $attributeData['is_used_on_product_page'] ?? false,
            'is_color' => $attributeData['is_color'] ?? false,
        ];
        $attribute = Attribute::forceCreate($data);
        if ($attributeData['options'] ?? false) {
            foreach ($attributeData['options'] as $option) {
                AttributeOption::forceCreate([
                    'attribute_id' => $attribute->id,
                    'value' => is_array($option) ? $option['value'] : $option,
                    'color' => is_array($option) ? ($option['color'] ?? null) : null,
                    'store_id' => 1
                ]);
            }
        }
    }
}