<?php

namespace Shopen\Http\Resources\Admin\TextSlide;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Shopen\Http\Resources\Admin\Product\Price\ProductPriceResource;

class TextSlideResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'color' => $this->color,
            'background_color' => $this->background_color,
            'content' => $this->content,
        ];
    }
}
