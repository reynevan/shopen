<?php

namespace Shopen\Http\Controllers\Admin\Product\Review;

use Inertia\Inertia;
use Shopen\Repositories\Product\Review\ProductReviewRepository;
use Shopen\Http\Resources\Admin\Product\Review\ProductReviewResource;

class ProductReviewsIndexController
{
    public function __construct(
        protected ProductReviewRepository $productReviewRepository
    )
    {
        
    }
    public function index()
    {
        $reviews = $this->productReviewRepository->getPaginated(request('sort', 'id'), request('dir', 'asc'), request('status'), request('q'));

        return Inertia::render('Admin/Product/Review/Index', [
            'reviews' => ProductReviewResource::collection($reviews),
            'status' => request('status'),
            'sort' => request('sort', 'id'),
            'dir' => request('dir', 'desc'),
            'q' => request('q')
        ]);
    }
}