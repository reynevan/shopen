<?php

namespace Shopen\Http\Controllers\Frontend;

use Inertia\Inertia;
use Shopen\Http\Controller;
use Shopen\Http\Resources\Product\ProductResource;
use Shopen\Services\SearchService\SearchService;

class HomeController extends Controller
{
    public function __construct(
        protected SearchService $searchService,
    )
    {
    }

    public function index()
    {
        $bestsellers = $this->searchService->setCategoryId(1)->setLimit(20)->getProducts()->products();

        return Inertia::render('Frontend/Home/Index', [
                'bestsellers' => ProductResource::collection($bestsellers),
            ]
        );
    }
}