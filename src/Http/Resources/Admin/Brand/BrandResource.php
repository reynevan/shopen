<?php

namespace Shopen\Http\Resources\Admin\Brand;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BrandResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'is_active' => $this->is_active,
            'show_on_homepage' => $this->show_on_homepage,
            'logo_url' => $this->resource->getFirstMediaUrl('default', 'logo-80'),
            'seo' => $this->resource->getSeoForWebsite(1),
        ];
    }
}
