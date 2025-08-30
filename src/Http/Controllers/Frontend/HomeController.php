<?php

namespace Shopen\Http\Controllers\Frontend;

use Inertia\Inertia;
use Shopen\Http\Controller;
use Shopen\Http\Resources\Brand\BrandResource;
use Shopen\Http\Resources\Product\ProductResource;
use Shopen\Repositories\Brand\BrandRepository;
use Shopen\Services\SearchService\SearchService;

class HomeController extends Controller
{
    public function __construct(
        protected readonly SearchService $searchService,
        protected readonly BrandRepository $brandRepository,
    )
    {
    }

    public function index()
    {
        $bestsellers = $this->searchService->setCategoryId(1)->setLimit(20)->getProducts()->products();
        $brands = $this->brandRepository->getActive();

        return Inertia::render('Frontend/Home/Index', [
                'bestsellers' => ProductResource::collection($bestsellers),
                'brands' => BrandResource::collection($brands)
            ]
        );
    }
}