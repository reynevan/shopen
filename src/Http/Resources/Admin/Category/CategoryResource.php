<?php

namespace Shopen\Http\Resources\Admin\Category;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;
use Shopen\Services\CustomAttributesService;
use Shopen\Http\Resources\Admin\Seo\SeoDetailResource;

class CategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        app(CustomAttributesService::class)->setPreloadedAttributes($this->resource);

        return [
            'id' => $this->id,
            'name' => $this->name,
            'parent_id' => $this->parent_id,
            'children' => CategoryResource::collection($this->whenLoaded('children')),
            'is_active' => $this->is_active,
            'display_in_menu' => $this->display_in_menu,
            'sort_index' => $this->sort_index,
            'attributes' => $this->resource->getCustomAttributes(),
            'menu_image_url' => $this->resource->getMenuImageUrl(),
            'seo' => SeoDetailResource::make($this->resource->getSeoForWebsite(1))
        ];
    }

}
