<?php

namespace Shopen\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Shopen\Enums\Banner\Placement;
use Shopen\Enums\Banner\PlacementType;
use Shopen\Models\Banner\Banner;
use Shopen\Models\Category\Category;
use Shopen\Models\Product\Product;

class BannerService
{
    private const CACHE_TTL = 60 * 60;

    public function getForPlacement(string $placementKey): Collection
    {
        $cacheKey = "banners.predefined.{$placementKey}";
        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($placementKey) {
            return $this->buildBaseQuery()
                ->where('placement_type', PlacementType::PREDEFINED)
                ->where('placement_key', $placementKey)
                ->get();
        })->map(fn($banner) => $this->transformBanner($banner));
    }

    public function getForCategory(Category $category): array
    {
        $placements = [
            Placement::CATEGORY_PAGE_TOP,
            Placement::CATEGORY_PAGE_BOTTOM,
            Placement::CATEGORY_PAGE_PRODUCTS_TOP,
            Placement::CATEGORY_PAGE_PRODUCTS_BOTTOM,
            Placement::CATEGORY_PAGE_FILTERS_TOP,
            Placement::CATEGORY_PAGE_FILTERS_BOTTOM,
        ];
        $banners = [];
        foreach ($placements as $placement) {
            $cacheKey = "banners.category.{$placement->value}.{$category->id}";
            $banners[$placement->value] = Cache::remember($cacheKey, self::CACHE_TTL, function () use ($placement, $category) {
                return $this->buildBaseQuery()
                    ->where('placement_type', PlacementType::PREDEFINED)
                    ->where('placement_key', $placement->value)
                    ->where(function ($query) use ($category) {
                        $query
                            ->whereHas('categories', fn($q) => $q->where('category_id', $category->id))
                            ->orWhereDoesntHave('categories');
                    })
                    ->get();
            })->map(fn($banner) => $this->transformBanner($banner));
        }
        return $banners;
    }

    public function getForProduct(Product $product)
    {
        $placements = [
            Placement::PRODUCT_PAGE_TOP,
            Placement::PRODUCT_PAGE_BOTTOM,
        ];
        $banners = [];
        foreach ($placements as $placement) {
            $cacheKey = "banners.product.{$placement->value}.{$product->id}";
            $banners[$placement->value] = Cache::remember($cacheKey, self::CACHE_TTL, function () use ($placement, $product) {
                return $this->buildBaseQuery()
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
                    ->get();
            })->map(fn($banner) => $this->transformBanner($banner));
        }
        return $banners;
    }

    public function getByCode(string $code): ?array
    {
        $cacheKey = "banners.code.{$code}";
        $banner = Cache::remember($cacheKey, self::CACHE_TTL, function () use ($code) {
            return $this->buildBaseQuery()
                ->where('placement_type', PlacementType::DYNAMIC)
                ->where('placement_key', $code)
                ->first();
        });

        return $banner ? $this->transformBanner($banner) : null;
    }

    private function buildBaseQuery()
    {
        $now = Carbon::now();
        return Banner::query()
            ->where('is_active', true)
            ->where(fn($q) => $q->where('start_date', '<=', $now)->orWhereNull('start_date'))
            ->where(fn($q) => $q->where('end_date', '>=', $now)->orWhereNull('end_date'))
            ->orderBy('sort_order', 'asc');
    }

    private function transformBanner(Banner $banner): array
    {
        return [
            'id' => $banner->id,
            'title' => $banner->title,
            'alt_text' => $banner->alt_text,
            'link_url' => $banner->link_url,
            'opens_in_new_tab' => $banner->opens_in_new_tab,
            'image_url_desktop' => asset('storage/' . $banner->image_path_desktop),
            'image_url_mobile' => $banner->image_path_mobile ? asset('storage/' . $banner->image_path_mobile) : null,
        ];
    }
}