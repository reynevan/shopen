<?php

namespace Shopen\Http\Controllers\Admin\Brand;

use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Shopen\Http\Requests\Admin\Brand\StoreBrandRequest;
use Shopen\Models\Brand\Brand;
use Shopen\Repositories\Brand\BrandRepository;

class BrandCreateController
{

    public function __construct(
        protected readonly BrandRepository $brandRepository
    )
    {}

    public function create(): Response
    {

        return Inertia::render('Admin/Brand/Create');
    }

    public function store(StoreBrandRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $brand = Brand::create($data);
        $brand->createOrUpdateSeoForWebsite(1, $data);

        if ($request->hasFile('logo')) {
            $brand->addMedia($request->file('logo'))->toMediaCollection();
        }

        return redirect()->to(route('admin.brands.index'))->with('success', 'Marka została utworzona.');
    }
}