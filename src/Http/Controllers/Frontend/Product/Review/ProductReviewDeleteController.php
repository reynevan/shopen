<?php

namespace Shopen\Http\Controllers\Frontend\Product\Review;

use Illuminate\Support\Facades\Auth;
use Shopen\Models\Product\Review\ProductReview;

class ProductReviewDeleteController
{

    public function delete(ProductReview $review)
    {
        if ($review->user_id !== Auth::id()) {
            return abort(403);
        }
        $product = $review->product;
        $review->delete();
        $product->searchable();
        return back()->with('success', 'Opinia została usunięta');
    }
}