<?php

namespace Shopen\Http\Resources\Admin\PromoCode;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PromoCodeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'name' => $this->name,
            'description' => $this->description,
            'is_active' => $this->is_active,
            'discount_type' => $this->discount_type->value ?? null,
            'discount_type_label' => $this->discount_type ? $this->discount_type->label() : null,
            'discount_value' => $this->discount_value,
            'max_discount_amount' => $this->max_discount_amount,
            'minimum_order_value' => $this->minimum_order_value,
            'applies_to' => $this->applies_to->value ?? null,
            'applies_to_label' => $this->applies_to ? $this->applies_to->label() : null,
            'applies_to_discounted' => $this->applies_to_discounted,
            'for_logged_users_only' => $this->for_logged_users_only,
            'usage_limit' => $this->usage_limit,
            'current_usage_count' => $this->current_usage_count,
            'valid_from' => $this->valid_from,
            'valid_to' => $this->valid_to,
            'valid_from_formatted' => ucfirst($this->valid_from?->translatedFormat('M d, Y')),
            'valid_to_formatted' => ucfirst($this->valid_to?->translatedFormat('M d, Y')),
            'attributes' => $this->resource->conditions['attributes'] ?? [],
            'categories' => $this->resource->conditions['categories'] ?? [],
        ];
    }
}
