<?php

namespace Shopen\Http\Resources\Attribute;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AttributeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = [
            'id' => $this->id,
            'name' => $this->name,
            'entity_type' => $this->entity_type,
            'backend_type' => $this->backend_type,
            'frontend_type' => $this->frontend_type,
            'code' => $this->code,
            'units' => $this->units,
            'is_filterable' => $this->is_filterable,
            'is_searchable' => $this->is_searchable,
            'is_system' => $this->is_system,
            'is_required' => $this->is_required,
            'is_visible_in_details' => $this->is_visible_in_details,
            'is_used_in_list' => $this->is_used_in_list,
            'options' => AttributeOptionResource::collection($this->sortedOptions)
        ];
        return $data;
    }
}
