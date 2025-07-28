<?php

namespace Shopen\Http\Controllers\Frontend\Api;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Shopen\Http\Resources\Admin\Product\Review\ProductReviewResource;
use Shopen\Models\Product\Product;
use Shopen\Models\Product\Review\ProductReview;
use Shopen\Models\Product\Review\ProductReviewVote;
use Shopen\Repositories\Product\Review\ProductReviewRepository;
use Throwable;

class ProductReviewsController
{
    public function __construct(
        protected ProductReviewRepository $productReviewRepository
    )
    {}

    public function index(Product $product)
    {
        $reviews = $this->productReviewRepository->getForProduct($product, request('opinie'));

        return ProductReviewResource::collection($reviews);
    }

    public function vote(ProductReview $review)
    {
        $user = Auth::user();

        if ($review->user_id === $user->id) {
            return response()->json(['error' => 'Nie możesz głosować na własną recenzję.'], 400);
        }

        DB::beginTransaction();
        try {
            $voteValue = (int) request('vote') >= 0 ? 1 : -1;

            $vote = ProductReviewVote::where('user_id', $user->id)
                ->where('product_review_id', $review->id)
                ->first();

            if ($vote) {
                $this->changeVote($vote, $review, $voteValue);
            } else {
                $this->createVote($review, $voteValue);
            }
            DB::commit();
        } catch (Throwable $exception) {
            DB::rollBack();
            Log::error('Błąd podczas głosowania na recenzję: ' . $exception->getMessage(), [
                'exception' => $exception
            ]);

            return response()->json(['error' => 'Wystąpił błąd. Spróbuj ponownie.'], 500);
        }

        return ProductReviewResource::make($review->fresh());
    }

    protected function changeVote(ProductReviewVote $vote, ProductReview $review, $voteValue)
    {
        if ($vote->vote !== $voteValue) {
            $vote->vote > 0
                ? $review->decrement('helpful_votes_count')
                : $review->decrement('unhelpful_votes_count');

            $vote->vote = $voteValue;
            $vote->save();

            $voteValue > 0
                ? $review->increment('helpful_votes_count')
                : $review->increment('unhelpful_votes_count');

        } else {
            $vote->vote > 0
                ? $review->decrement('helpful_votes_count')
                : $review->decrement('unhelpful_votes_count');

            $vote->delete();
        }
    }

    protected function createVote(ProductReview $review, $voteValue)
    {
        ProductReviewVote::create([
            'user_id'           => Auth::user()->id,
            'product_review_id' => $review->id,
            'vote'              => $voteValue,
        ]);

        $voteValue > 0
            ? $review->increment('helpful_votes_count')
            : $review->increment('unhelpful_votes_count');
    }
}