<?php

namespace Shopen\Http\Resources\Admin\User;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Shopen\Http\Resources\Admin\Order\OrderResource;
use Shopen\Http\Resources\User\AddressResource;

class CustomerResource extends JsonResource
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
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'shipping_addresses' => AddressResource::collection($this->whenLoaded('shippingAddresses')),
            'billing_addresses' => AddressResource::collection($this->whenLoaded('billingAddresses')),
            'orders' => OrderResource::collection($this->whenLoaded('orders')),
        ];
    }

}