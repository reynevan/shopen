<?php

namespace Shopen\Http\Resources\Category;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class CategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'image_url_desktop' => $this->image_path_desktop ? Storage::url($this->image_path_desktop) : null,
            'image_url_mobile' => $this->image_path_mobile ? Storage::url($this->image_path_mobile) : null,
            'children' => CategoryResource::collection($this->whenLoaded('children')),
            'seo' => $this->getSeoData(1, true)
        ];
    }

}
