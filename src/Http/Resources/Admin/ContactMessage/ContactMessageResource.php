<?php

namespace Shopen\Http\Resources\Admin\ContactMessage;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;
use Illuminate\Support\Number;
use Shopen\Http\Resources\Admin\Order\ProductResource;
use Shopen\Http\Resources\Admin\PromoCode\PromoCodeCouponResource;
use Shopen\Http\Resources\Admin\User\CustomerResource;

class ContactMessageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'subject' => $this->subject,
            'message' => strip_tags($this->message),
            'status' => $this->status,
            'status_label' => $this->status->label(),
            'user' => CustomerResource::make($this->whenLoaded('user')),
            'created_at_diff' => Carbon::make($this->created_at)->diffForHumans(),
            'created_at' => $this->created_at->format('d-m-Y H:i'),
            'responses' => ContactMessageResponseResource::collection($this->whenLoaded('responses')),
        ];
    }
}
