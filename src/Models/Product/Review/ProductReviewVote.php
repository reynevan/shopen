<?php

namespace Shopen\Models\Product\Review;

use Illuminate\Database\Eloquent\Model;

class ProductReviewVote extends Model
{
    protected $fillable = [
        'user_id',
        'product_review_id',
        'vote'
    ];

    protected $casts = [
        'helpful_votes_count' => 'int',
        'unhelpful_votes_count' => 'int',
    ];
}
