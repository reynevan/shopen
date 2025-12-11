<?php

namespace Shopen\Http\Controllers\Admin\Banner;

use Illuminate\Support\Arr;
use Inertia\Inertia;
use Inertia\Response;
use Shopen\Enums\Banner\Placement;
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
        return Inertia::render('Admin/Banner/Index', [
            'banners' => fn() => BannerResource::collection($this->bannerRepository->getPaginated(request('sort', 'id'), request('dir', 'asc'), request('q'), request('placement'))),
            'placements' => fn () => Placement::toArray(),
            'placement' => fn () => request('placement'),
            'sort' => fn () => request('sort', 'id'),
            'dir' => fn () => request('dir', 'desc'),
            'q' => fn () => request('q')
        ]);
    }

}