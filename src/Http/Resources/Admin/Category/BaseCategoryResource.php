<?php

namespace Shopen\Http\Resources\Admin\Category;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Shopen\Services\CustomAttributesService;

class BaseCategoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        app(CustomAttributesService::class)->setPreloadedAttributes($this->resource);

        return [
            'id' => $this->id,
            'name' => $this->name,
            'children' => BaseCategoryResource::collection($this->children),
            'is_active' => $this->is_active,
            'sort_index' => $this->sort_index
        ];
    }

}
