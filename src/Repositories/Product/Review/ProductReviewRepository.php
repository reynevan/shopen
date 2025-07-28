<?php

namespace Shopen\Repositories\Product\Review;

use App\Support\ProductSorting\ProductSortRegistry;
use Illuminate\Database\Eloquent\Builder;
use Shopen\Enums\Product\Review\ReviewStatus;
use Shopen\Models\Product\Product;
use Shopen\Models\Product\Review\ProductReview;

class ProductReviewRepository
{
    public function __construct(
    )
    {}

    public function getForProduct(Product $product, $sortOption = null)
    {
        return $product
            ->approvedReviews()
            ->when($sortOption, function (Builder $query) use ($sortOption) {
                if ($sortOption === 'najnowsze') {
                    $query->latest();
                } elseif ($sortOption === 'najwyzsza-ocena') {
                    $query->orderBy('rating', 'desc');
                } elseif ($sortOption === 'najnizsza-ocena') {
                    $query->orderBy('rating', 'asc');
                }
            })
            ->orderByRaw('helpful_votes_count - unhelpful_votes_count desc')
            ->paginate(25);
    }

    public function getPaginated($sortField, $sortDir, $status = null, $searchQuery = null)
    {
        $products = ProductReview::query()
            ->when($searchQuery, function (Builder $query) use ($searchQuery) {
                $query
                    ->whereLike('comment', '%' . $searchQuery . '%')
                    ->orWhereLike('comment_to_verify', '%' . $searchQuery . '%');
            })
            ->when($status, function (Builder $query) use ($status) {
                if ($status === ReviewStatus::PENDING->value) {
                    $query->whereIn('status', [ReviewStatus::PENDING->value, ReviewStatus::PENDING_EDIT->value]);
                } elseif ($status === ReviewStatus::APPROVED->value) {
                    $query->whereIn('status', [ReviewStatus::APPROVED->value, ReviewStatus::PENDING_EDIT->value]);
                } else {
                    $query->where('status', $status);
                }
            })
            ->orderBy($sortField, $sortDir)
            ->paginate(15)
            ->withQueryString();


        return $products;
    }

}