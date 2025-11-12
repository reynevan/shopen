<?php

namespace Shopen\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Shopen\Http\Resources\Attribute\FilterResource;
use Shopen\Models\Category\Category;
use Shopen\Models\Product\Product;

class MediaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $width = $height = null;
        try {
            [$width, $height] = getimagesize($this->getPath());
        } catch (\Exception $e) {}
        return [
            'id' => $this->id,
            'url' => $this->original_url,
            'order' => $this->resource->order_column,
            'size' => $this->resource->human_readable_size,
            'width' => $width,
            'height' => $height,
            'gallery' => $this->resource->getCustomProperty('gallery') ?? false,
            'thumbnail' => $this->resource->getCustomProperty('thumbnail') ?? false,
            'meta' => $this->resource->getCustomProperty('meta') ?? false,
        ];
    }

}
