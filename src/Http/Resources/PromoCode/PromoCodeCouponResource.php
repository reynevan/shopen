<?php

namespace Shopen\Http\Resources\PromoCode;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PromoCodeCouponResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'code' => $this->code,
            'promo_code' => PromoCodeResource::make($this->whenLoaded('promoCode')),
        ];
    }
}
