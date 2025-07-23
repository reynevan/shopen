<?php

namespace Shopen\Http\Resources\Admin\Seo;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SeoDetailResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'seo_title' => $this->seo_title,
            'seo_description' => $this->seo_description,
        ];
    }
}
