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
            'children' => CategoryResource::collection($this->children),
            'is_active' => $this->is_active,
            'sort_index' => $this->sort_index,
            'attributes' => $this->resource->getCustomAttributes(),
            'image_path_desktop' => $this->image_path_desktop,
            'image_url_desktop' => $this->image_path_desktop ? Storage::url($this->image_path_desktop) : null,
            'image_path_mobile' => $this->image_path_mobile,
            'image_url_mobile' => $this->image_path_mobile ? Storage::url($this->image_path_mobile) : null,
            'seo' => SeoDetailResource::make($this->seo)
        ];
    }

}
