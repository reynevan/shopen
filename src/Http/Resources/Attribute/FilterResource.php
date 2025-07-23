<?php

namespace Shopen\Http\Resources\Attribute;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class   FilterResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = [
            'name' => $this->name,
            'code' => $this->code,
            'slug' => $this->slug,
            'options' => AttributeOptionResource::collection($this->resource->options),
            'units' => $this->units,
        ];
        return $data;
    }
}
