<?php

namespace Shopen\Http\Resources\Brand;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BaseBrandResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'logo' => $this->resource->getLogoUrl(),
            'url' => $this->resource->getUrl(),
        ];
    }
}
