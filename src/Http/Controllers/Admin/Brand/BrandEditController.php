<?php

namespace Shopen\Http\Controllers\Admin\Brand;

use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Shopen\Http\Requests\Admin\Brand\StoreBrandRequest;
use Shopen\Http\Requests\Admin\Brand\UpdateBrandRequest;
use Shopen\Http\Resources\Admin\Brand\BrandResource;
use Shopen\Models\Brand\Brand;
use Shopen\Repositories\Brand\BrandRepository;

class BrandEditController
{

    public function __construct(
        protected readonly BrandRepository $brandRepository
    )
    {}

    public function edit(Brand $brand): Response
    {
        return Inertia::render('Admin/Brand/Edit', [
            'brand' => BrandResource::make($brand)
        ]);
    }

    public function update(UpdateBrandRequest $request, Brand $brand): RedirectResponse
    {
        $data = $request->validated();
        $brand->update($data);

        $brand->createOrUpdateSeoForStore(1, $data);

        if ($request->hasFile('logo')) {
            $brand->media()->delete();
            $brand->addMedia($request->file('logo'))->toMediaCollection();
        }

        return redirect()->to(route('admin.brands.index'))->with('success', 'Marka została zaktualizowana.');
    }

    public function destroy(Brand $brand): RedirectResponse
    {
        $brand->delete();

        return redirect()->to(route('admin.brands.index'))->with('success', 'Marka została usunięta');
    }
}