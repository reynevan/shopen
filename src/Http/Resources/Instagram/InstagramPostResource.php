<?php

namespace Shopen\Http\Resources\Instagram;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Shopen\Http\Resources\Attribute\FilterResource;
use Shopen\Models\Category\Category;
use Shopen\Models\Product\Product;

class InstagramPostResource extends JsonResource
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
            'post_url' => $this->post_url,
            'media_url' => $this->resource->getFirstMediaUrl('default', 'media'),
            'media_2x_url' => $this->resource->getFirstMediaUrl('default', 'media-2x'),
        ];
    }

}
