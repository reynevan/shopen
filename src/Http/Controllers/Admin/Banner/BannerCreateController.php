<?php

namespace Shopen\Http\Controllers\Admin\Banner;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;
use Shopen\Enums\Banner\Placement;
use Shopen\Enums\Banner\PlacementType;
use Shopen\Http\Requests\Admin\Banner\StoreBannerRequest;
use Shopen\Http\Resources\Admin\Category\CategoryResource;
use Shopen\Models\Banner\Banner;
use Shopen\Models\Category\Category;
use Shopen\Repositories\Category\CategoryRepository;

readonly class BannerCreateController
{
    public function __construct(
        protected CategoryRepository $categoryRepository,
    )
    {}

    public function create(): Response
    {
        return Inertia::render('Admin/Banner/Create', [
            'placementTypes' => fn () => PlacementType::options(),
            'placements' => fn () => Placement::options(),
            'categories' => fn () => $this->categoryRepository->getArray(),
        ]);
    }

    public function store(StoreBannerRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $validated['image_path_desktop'] = $request->file('image_desktop')->store('banners', 'public');
        if ($request->hasFile('image_mobile')) {
            $validated['image_path_mobile'] = $request->file('image_mobile')->store('banners', 'public');
        }

        $banner = Banner::create($validated);

        if (!empty($validated['category_ids'])) {
            $banner->categories()->sync($validated['category_ids']);
        }

        return Redirect::route('admin.banners.index')->with('success', 'Baner został utworzony.');
    }


}