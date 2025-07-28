<?php

namespace Shopen\Http\Controllers\Admin\Product\Review;

use Illuminate\Http\RedirectResponse;
use Shopen\Enums\Product\Review\ReviewStatus;
use Shopen\Models\Product\Review\ProductReview;

class ProductReviewsEditController
{
    public function accept(ProductReview $review): RedirectResponse
    {
        if ($review->comment_to_verify) {
            $review->comment = $review->comment_to_verify;
            $review->comment_to_verify = null;
        }
        if ($review->rating_to_verify) {
            $review->rating = $review->rating_to_verify;
            $review->rating_to_verify = null;
        }
        $review->status = ReviewStatus::APPROVED;
        $review->save();
        $review->product->searchable();

        return back();
    }

    public function reject(ProductReview $review): RedirectResponse
    {

        if ($review->status === ReviewStatus::PENDING_EDIT) {
            $review->status = ReviewStatus::APPROVED;
        } else {
            $review->status = ReviewStatus::REJECTED;
        }
        $review->comment_to_verify = null;
        $review->rating_to_verify = null;
        $review->save();
        $review->product->searchable();
        return back();
    }

    public function delete(ProductReview $review): RedirectResponse
    {
        $review->delete();
        $review->product->searchable();
        return back();
    }
}