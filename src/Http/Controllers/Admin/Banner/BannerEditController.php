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
use Shopen\Http\Resources\Admin\Banner\BannerResource;
use Shopen\Models\Banner\Banner;
use Shopen\Repositories\Category\CategoryRepository;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Throwable;

readonly class BannerEditController
{
    public function __construct(
        protected CategoryRepository $categoryRepository,
    )
    {}

    public function edit(Banner $banner): Response
    {
        return Inertia::render('Admin/Banner/Edit', [
            'banner' => fn () => BannerResource::make($banner),
            'placements' => fn () => Placement::options(),
            'placementTypes' => fn () => PlacementType::options(),
            'categories' => fn () => $this->categoryRepository->getArray(),
        ]);
    }

    public function update(StoreBannerRequest $request, Banner $banner): RedirectResponse
    {
        $validated = $request->validated();

        DB::beginTransaction();
        try {
            if ($request->hasFile('image_desktop')) {
                $banner->getMedia('desktop')->each(fn (Media $media) => $media->delete());
                $banner
                    ->addMedia($request->file('image_desktop'))
                    ->toMediaCollection('desktop');
            }

            if ($request->hasFile('image_mobile')) {
                $banner->getMedia('mobile')->each(fn (Media $media) => $media->delete());
                $banner
                    ->addMedia($request->file('image_mobile'))
                    ->toMediaCollection('mobile');
            }

            $banner->update($validated);

            $banner->categories()->sync($validated['category_ids'] ?? []);
            DB::commit();

        } catch (Throwable $e) {
            DB::rollBack();
            Log::error($e->getMessage());
        }
        return Redirect::route('admin.banners.index')->with('success', 'Baner został zaktualizowany.');
    }

    public function destroy(Banner $banner): RedirectResponse
    {
        $banner->delete();
        return back();
    }

}