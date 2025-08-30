<?php

namespace Shopen\Http\Controllers\Admin\Brand;

use Inertia\Inertia;
use Inertia\Response;
use Shopen\Http\Resources\Admin\Attribute\AttributeResource;
use Shopen\Http\Resources\Admin\Brand\BrandResource;
use Shopen\Repositories\Attribute\AttributeRepository;
use Shopen\Repositories\Brand\BrandRepository;

class BrandIndexController
{

    public function __construct(
        protected readonly BrandRepository $brandRepository
    )
    {}

    public function index(): Response
    {
        $brands = $this->brandRepository->getPaginated(request('sort', 'id'), request('dir', 'asc'), request('q'));

        return Inertia::render('Admin/Brand/Index', [
            'brands' => BrandResource::collection($brands),
            'sort' => request('sort', 'id'),
            'dir' => request('dir', 'desc'),
            'q' => request('q')
        ]);
    }
}