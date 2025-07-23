<?php

namespace Shopen\Http\Controllers\Admin\Banner;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
use Shopen\Enums\Banner\Placement;
use Shopen\Enums\Banner\PlacementType;
use Shopen\Http\Requests\Admin\Banner\StoreBannerRequest;
use Shopen\Http\Resources\Admin\Category\CategoryResource;
use Shopen\Http\Resources\Banner\BannerResource;
use Shopen\Jobs\RecalculateProductPrice;
use Shopen\Models\Banner\Banner;
use Shopen\Models\Category\Category;
use Shopen\Models\Product\Product;
use Shopen\Repositories\Category\CategoryRepository;

readonly class BannerEditController
{
    public function __construct(
        protected CategoryRepository $categoryRepository,
    )
    {}

    public function edit(Banner $banner): Response
    {
        return Inertia::render('Admin/Banner/Edit', [
            'banner' => BannerResource::make($banner),
            'placements' => Placement::options(),
            'categories' => fn () => CategoryResource::collection($this->categoryRepository->getAll(0)),
        ]);
    }

    public function update(StoreBannerRequest $request, Banner $banner): RedirectResponse
    {
        $validated = $request->validated();

        if ($request->hasFile('image_desktop')) {
            if ($banner->image_path_desktop) {
                Storage::disk('public')->delete($banner->image_path_desktop);
            }
            $validated['image_path_desktop'] = $request->file('image_desktop')->store('banners', 'public');
        }

        if ($request->hasFile('image_mobile')) {
            if ($banner->image_path_mobile) {
                Storage::disk('public')->delete($banner->image_path_mobile);
            }
            $validated['image_path_mobile'] = $request->file('image_mobile')->store('banners', 'public');
        }

        $banner->update($validated);

        $banner->categories()->sync($validated['category_ids'] ?? []);

        return Redirect::route('admin.banners.index')->with('success', 'Baner został zaktualizowany.');
    }

}