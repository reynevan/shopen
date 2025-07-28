<?php

namespace Shopen\Http\Controllers\Frontend\Product\Review;

use Illuminate\Support\Facades\Auth;
use Shopen\Enums\Product\Review\ReviewStatus;
use Shopen\Http\Requests\Frontend\Product\Review\StoreProductReviewRequest;
use Shopen\Models\Product\Product;
use Shopen\Services\ProductReviewService;

class ProductReviewStoreController
{
    public function __construct(
        protected ProductReviewService $productReviewService
    )
    {

    }

    public function store(StoreProductReviewRequest $request, Product $product)
    {
        $user = Auth::user();

        $isVerified = $this->productReviewService->hasUserPurchasedProduct($user, $product);

        $product->reviews()->create([
            'user_id' => $user->id,
            'rating' => $request->rating,
            'comment' => $request->comment ?? '',
            'is_verified_purchase' => $isVerified,
            'status' => $isVerified ? ReviewStatus::APPROVED : ReviewStatus::PENDING,
        ]);

        $product->searchable();

        $message = $isVerified ? 'Dziękujemy za opinię!' : 'Opinia została wysłana i oczekuje na moderację.';

        return back()->with('success', $message);
    }
}