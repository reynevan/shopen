<?php

namespace Shopen\Models\Product\Review;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Shopen\Enums\Product\Review\ReviewStatus;
use Shopen\Models\Product\Product;
use Shopen\Models\User;
use Shopen\Database\Factories\ReviewFactory;

class ProductReview extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'rating',
        'comment',
        'is_verified_purchase',
        'status',
        'rating_to_verify',
        'comment_to_verify',
    ];

    protected $casts = [
        'is_verified_purchase' => 'bool',
        'status' => ReviewStatus::class,
    ];

    protected static function newFactory()
    {
        return ReviewFactory::new();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function reply()
    {
        return $this->hasOne(ProductReviewReply::class);
    }

    public function votes()
    {
        return $this->hasMany(ProductReviewVote::class);
    }

    public function scopeApproved($query)
    {
        return $query->whereIn('status', [ReviewStatus::APPROVED, ReviewStatus::PENDING_EDIT]);
    }

    public function scopePending($query)
    {
        return $query->whereIn('status', [ReviewStatus::PENDING, ReviewStatus::PENDING_EDIT]);
    }

    public function getHelpfulnessScoreAttribute()
    {
        return $this->helpful_votes_count - $this->unhelpful_votes_count;
    }

    public function setCommentAttribute($value)
    {
        $this->attributes['comment'] = preg_replace('/\n{3,}/', "\n\n", $value);
    }

    public function setCommentToVerifyAttribute($value)
    {
        $this->attributes['comment_to_verify'] = preg_replace('/\n{3,}/', "\n\n", $value);
    }
}
