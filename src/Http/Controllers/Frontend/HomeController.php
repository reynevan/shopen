<?php

namespace Shopen\Http\Controllers\Frontend;

use Inertia\Inertia;
use Inertia\Response;
use Shopen\Http\Controller;
use Shopen\Http\Resources\Brand\BrandResource;
use Shopen\Http\Resources\Product\ProductResource;
use Shopen\Repositories\Brand\BrandRepository;
use Shopen\Services\BannerService;
use Shopen\Services\SearchService\SearchService;

class HomeController extends Controller
{
    public function __construct(
        protected readonly BrandRepository $brandRepository,
        protected readonly BannerService   $bannerService,
    )
    {
    }

    public function index(): Response
    {

        $bestsellers = app(SearchService::class)->setCategoryId(1)->setLimit(20)->getProducts()->products();

        $newProducts = app(SearchService::class)->setNew(true)->setLimit(20)->getProducts()->products();

        $brands = $this->brandRepository->getVisibleOnHomepage();

        return Inertia::render('Frontend/Home/Index', [
                'banners' => fn() => $this->bannerService->getForHomePage(),
                'bestsellers' => fn() => ProductResource::collection($bestsellers),
                'brands' => fn() => BrandResource::collection($brands),
                'newProducts' => fn() => ProductResource::collection($newProducts),
            ]
        );
    }
}