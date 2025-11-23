<?php

namespace Shopen\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Shopen\Enums\Banner\Placement;
use Shopen\Enums\Banner\PlacementType;
use Shopen\Http\Resources\Banner\BannerResource;
use Shopen\Models\Banner\Banner;
use Shopen\Models\Category\Category;
use Shopen\Models\Product\Product;

class BannerService
{
    private const CACHE_TTL = 60 * 60;

    public function getForCategory(Category $category): array
    {
        $cacheKey = 'banners.category.' . $category->id;
        return Cache::remember($cacheKey, config('app.debug') ? 0 : self::CACHE_TTL, function () use ($category) {
            $placements = [
                Placement::CATEGORY_PAGE_TOP->value,
                Placement::CATEGORY_PAGE_BOTTOM->value,
                Placement::CATEGORY_PAGE_PRODUCTS_TOP->value,
                Placement::CATEGORY_PAGE_PRODUCTS_BOTTOM->value,
            ];
            $data = [];
            $banners = $this->buildBaseQuery()
                ->where(function ($query) use ($category) {
                    $query
                        ->whereHas('categories', fn($q) => $q->where('category_id', $category->id))
                        ->orWhereDoesntHave('categories');
                })
                ->whereIn('placement_key', $placements)
                ->get();
            foreach ($banners as $banner) {
                $data[$banner->getPagePlacementKey()][] = BannerResource::make($banner);
            }
            return $data;
        });
    }

    public function getForProduct(Product $product): array
    {
        $cacheKey = 'banners.product.' . $product->id;
        return Cache::remember($cacheKey, config('app.debug') ? 0 : self::CACHE_TTL, function () use ($product) {
            $placements = [
                Placement::PRODUCT_PAGE_TOP,
                Placement::PRODUCT_PAGE_BOTTOM,
            ];
            $banners = [];
            foreach ($placements as $placement) {
                $banners[$placement->value] = $this->buildBaseQuery()
                    ->where('placement_type', PlacementType::PREDEFINED)
                    ->where('placement_key', $placement->value)
                    ->where(function ($query) use ($product) {
                        $query
                            ->whereHas('categories', function (Builder $query) use ($product) {
                                $query->whereHas('products', function (Builder $query) use ($product) {
                                    $query->where('products.id', $product->id);
                                });
                            })
                            ->orWhereDoesntHave('categories');
                    })
                    ->get()
                    ->map(fn($banner) => BannerResource::make($banner));
            }
            return $banners;
        });
    }

    public function getForHomePage(): array
    {
        $placements = [
            Placement::HOME_PAGE_TOP,
            Placement::HOME_PAGE_BOTTOM,
        ];
        $banners = [];
        foreach ($placements as $placement) {
            $placementBanners = $this->buildBaseQuery()
                ->where('placement_type', PlacementType::PREDEFINED)
                ->where('placement_key', $placement->value)
                ->get();
            $banners[$placement->value] = BannerResource::collection($placementBanners)->resolve();
        }
        return $banners;
    }

    private function buildBaseQuery()
    {
        $now = Carbon::now();
        return Banner::query()
            ->where('is_active', true)
            ->with(['media'])
            ->where(fn($q) => $q->where('start_date', '<=', $now)->orWhereNull('start_date'))
            ->where(fn($q) => $q->where('end_date', '>=', $now)->orWhereNull('end_date'))
            ->orderBy('sort_order', 'asc');
    }
}