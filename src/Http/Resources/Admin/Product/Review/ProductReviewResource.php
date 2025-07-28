<?php

namespace Shopen\Http\Resources\Admin\Product\Review;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Shopen\Http\Resources\Admin\Product\ProductResource;
use Shopen\Http\Resources\User\UserResource;

class ProductReviewResource extends JsonResource
{

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'helpful_votes_count' => $this->helpful_votes_count,
            'unhelpful_votes_count' => $this->unhelpful_votes_count,
            'is_verified_purchase' => $this->is_verified_purchase,
            'status' => $this->status,
            'status_label' => $this->status->label(),
            'rating' => $this->rating,
            'rating_to_verify' => $this->rating_to_verify,
            'comment' => $this->comment,
            'comment_to_verify' => $this->comment_to_verify,
            'created_at' => $this->created_at->toLocalDateTime(),
            'user' => UserResource::make($this->user),
            'product' => [
                'id' => $this->product_id,
                'image' => $this->product->getThumbnailUrl(),
                'url' => $this->product->getUrl(),
                'name' => $this->product->name
            ]
        ];
    }
}
