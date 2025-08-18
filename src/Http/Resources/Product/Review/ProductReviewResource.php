<?php

namespace Shopen\Http\Resources\Product\Review;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class ProductReviewResource extends JsonResource
{

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'helpful_votes_count' => $this->helpful_votes_count,
            'unhelpful_votes_count' => $this->unhelpful_votes_count,
            'is_verified_purchase' => $this->is_verified_purchase,
            'rating' => $this->rating,
            'comment' => $this->comment,
            'created_at' => Carbon::make($this->created_at)->diffForHumans(),
            'user' => [
                'id' => $this->user->id,
                'first_name' => $this->user->first_name
            ]
        ];
    }
}
