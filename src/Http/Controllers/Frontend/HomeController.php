<?php

namespace Shopen\Http\Controllers\Frontend;

use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;
use Inertia\Response;
use Shopen\Http\Controller;
use Shopen\Http\Resources\Banner\BannerResource;
use Shopen\Http\Resources\Brand\BrandResource;
use Shopen\Http\Resources\Product\ProductResource;
use Shopen\Repositories\Brand\BrandRepository;
use Shopen\Services\BannerService;
use Shopen\Services\SearchService\SearchService;

class HomeController extends Controller
{
    private const CACHE_TTL = 60 * 60;

    public function __construct(
        protected readonly BrandRepository $brandRepository,
        protected readonly BannerService   $bannerService,
    )
    {
    }

    public function index(): Response
    {
        $newProducts = app(SearchService::class)->setNew(true)->setLimit(20)->getProducts()->products();

        $brands = fn() => Cache::remember('home.brands', config('app.debug') ? 0 : self::CACHE_TTL, function () {
            return BrandResource::collection($this->brandRepository->getVisibleOnHomepage())->resolve();
        });

        $banners = fn() => Cache::remember('home.banners', config('app.debug') ? 0 : self::CACHE_TTL, function () {
            return $this->bannerService->getForHomePage();
        });

        return Inertia::render('Frontend/Home/Index', [
                'banners' => $banners,
                'brands' => $brands,
                //'newProducts' => fn() => ProductResource::collection($newProducts),
            ]
        );
    }
}