<?php

namespace Shopen\Http\Controllers\Admin\Product\Review;

use Illuminate\Http\RedirectResponse;
use Shopen\Enums\Product\Review\ReviewStatus;
use Shopen\Models\Product\Product;
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
        $this->reindexProduct($review->product);

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
        $this->reindexProduct($review->product);
        return back();
    }

    public function destroy(ProductReview $review): RedirectResponse
    {
        $review->delete();
        $this->reindexProduct($review->product);
        return back();
    }

    protected function reindexProduct(Product $product)
    {
        $product->searchable();
        if ($product->isConfigurable()) {
            foreach ($product->variants as $variant) {
                $variant->searchable();
            }
        }
        if ($product->parent) {
            $product->parent->searchable();
            foreach ($product->parent->variants as $variant) {
                $variant->searchable();
            }
        }
    }
}