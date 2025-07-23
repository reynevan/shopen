<?php

namespace Shopen\Services;

use Illuminate\Support\Facades\Cache;
use Shopen\Models\Category\Category;
use Shopen\Models\UrlRewrite;
use Shopen\Repositories\UrlRewriteRepository;

class MenuService
{
    private const CACHE_TTL = 1 * 60;

    public function __construct(
        protected readonly UrlRewriteRepository $urlRewriteRepository,
    )
    {}

    public function getCategories()
    {
        return Cache::remember('menu.categories', self::CACHE_TTL, function () {
            $urls = $this->getUrls();
            $categories = Category::query()
                ->where('is_active', true)
                ->filterByAttribute('display_in_menu', true)
                ->orderBy('level', 'desc')
                ->get();

            $map = [];


            foreach ($categories as $category) {
                $category->subcategories = [];
                $category->is_current = false;//$category->id == $currentCategoryId;
                $category->has_current = false;
                $category->url = $urls[$category->id];
                $category->loadAttribute('name');
                $map[$category->id] = $category;
            }

            $tree = [];

            foreach ($categories as $category) {
                if ($category->parent_id && isset($map[$category->parent_id])) {
                    if ($category->is_current || $category->has_current) {
                        $map[$category->parent_id]->has_current = true;
                    }
                    $map[$category->parent_id]->subcategories = array_merge($map[$category->parent_id]->subcategories, [$map[$category->id]]);
                } elseif (intval($category->level) === 0) {
                    $tree[] = $map[$category->id];
                }
            }

            return $tree;
        });
    }

    public function getCssClasses(Category $category)
    {
        $classes = [];
        if ($category->is_current) {
            $classes[] = 'is-active';
        }

        if ($category->has_current) {
            $classes[] = 'has-active';
        }

        return implode(' ', $classes);
    }

    protected function getUrls(): array
    {
        return $this
            ->urlRewriteRepository
            ->getAllForCategories()
            ->map(function (UrlRewrite $urlRewrite) {
                $urlRewrite->url = config('app.url') . '/' . $urlRewrite->request_path;
                return $urlRewrite;
            })
            ->pluck('url', 'entity_id')
            ->toArray();
    }
}