<?php

namespace Shopen\Http\Resources\Admin\PromoCode;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PromoCodeCouponResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'usage_count' => $this->usage_count,
            'promo_code' => PromoCodeResource::make($this->whenLoaded('promoCode')),
        ];
    }
}
