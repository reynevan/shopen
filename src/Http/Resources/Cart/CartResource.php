<?php

namespace Shopen\Http\Resources\Cart;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Number;

class CartResource extends JsonResource
{

    public function toArray(Request $request): array
    {

        $subtotal = $this->totalPrice();
        $shipping = $this->getShippingPrice();

        return [
            'id' => $this->id,
            'subtotal' => Number::currency($subtotal),
            'shipping' => Number::currency($shipping),
            'total' => Number::currency($subtotal + $shipping),
            'items' => CartItemResource::collection($this->items)
        ];
    }

}
