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
        return [
            'id' => $this->id,
            'url' => $this->original_url,
            'order' => $this->resource->order_column
        ];
    }

}
