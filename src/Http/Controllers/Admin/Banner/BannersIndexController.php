<?php

namespace Shopen\Http\Controllers\Admin\Banner;

use Inertia\Inertia;
use Inertia\Response;
use Shopen\Http\Resources\Admin\Banner\BannerResource;
use Shopen\Repositories\Banner\BannerRepository;

readonly class BannersIndexController
{
    public function __construct(
        protected BannerRepository $bannerRepository
    )
    {}

    public function index(): Response
    {
        $banners = $this->bannerRepository->getPaginated(request('sort', 'id'), request('dir', 'asc'), request('q'));

        return Inertia::render('Admin/Banner/Index', [
            'banners' => BannerResource::collection($banners),
            'sort' => request('sort', 'id'),
            'dir' => request('dir', 'desc'),
            'q' => request('q')
        ]);
    }

}