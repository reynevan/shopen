<?php

namespace Shopen\Http\Controllers\Frontend\Product\Review;

use Illuminate\Support\Facades\Auth;
use Shopen\Enums\Product\Review\ReviewStatus;
use Shopen\Http\Requests\Frontend\Product\Review\UpdateProductReviewRequest;
use Shopen\Models\Product\Review\ProductReview;
use Shopen\Services\ProductReviewService;

class ProductReviewUpdateController
{
    public function __construct(
        protected ProductReviewService $productReviewService
    )
    {}

    public function update(UpdateProductReviewRequest $request, ProductReview $review)
    {
        $isVerified = $this->productReviewService->hasUserPurchasedProduct(Auth::user(), $review->product);

        $review->rating = $request->rating;
        if ($isVerified) {
            $review->update([
                'rating' => $request->rating,
                'comment' => $request->comment,
                'is_verified_purchase' => true,
                'status' => ReviewStatus::APPROVED,
            ]);
        } else {
            $review->update([
                'rating_to_verify' => $request->rating,
                'comment_to_verify' => $request->comment,
                'is_verified_purchase' => false,
                'status' => $request->status === ReviewStatus::PENDING ? ReviewStatus::PENDING : ReviewStatus::PENDING_EDIT
            ]);
        }

        $message = $isVerified ? 'Dziękujemy za opinię!' : 'Opinia została wysłana i oczekuje na moderację.';
        $review->product->searchable();
        return back()->with('success', $message);
    }
}