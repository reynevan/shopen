<?php

namespace Shopen\Services;

use Illuminate\Support\Facades\Cache;
use Shopen\Core\Context;
use Shopen\Models\Category\Attribute\Value\CategoryAttributeBool;
use Shopen\Models\Category\Attribute\Value\CategoryAttributeString;
use Shopen\Models\Category\Category;
use Shopen\Models\UrlRewrite;
use Shopen\Repositories\Category\CategoryAttributeRepository;
use Shopen\Repositories\UrlRewriteRepository;

class MenuService
{
    public function __construct(
        protected readonly UrlRewriteRepository $urlRewriteRepository,
        protected readonly CategoryAttributeRepository $categoryAttributeRepository
    )
    {}

    public function getCategories()
    {
        return Cache::rememberForever('menu.categories', function () {
            $urls = $this->getUrls();

            $categories = Category::query()
                ->with(['media'])
                ->filterByAttribute('is_active', true)
                ->orderBy('level', 'desc')
                ->orderBy('sort_index')
                ->get();

            $names = $this->categoryAttributeRepository->getValues('name');

            $menuDisplayValues = $this->categoryAttributeRepository->getValues('display_in_menu');

            $map = [];

            foreach ($categories as $category) {
                if ($category->level === 0 && !($menuDisplayValues[$category->id] ?? false)) {
                    continue;
                }
                $map[$category->id] = [
                    'id' => $category->id,
                    'subcategories' => [],
                    'image' => $category->getMenuMedia(),
                    'url' => $urls[$category->id] ?? '',
                    'name' => $names[$category->id] ?? '',
                ];
            }

            $tree = [];

            foreach ($categories as $category) {
                if (!isset($map[$category->id])) { continue; }
                if ($category->parent_id && isset($map[$category->parent_id])) {
                    if (isset($map[$category->parent_id]['subcategories']) && count($map[$category->parent_id]['subcategories']) >= config('shopen.menu.categories.level_1_max')) {
                        continue;
                    }
                    $map[$category->parent_id]['subcategories'] = array_merge($map[$category->parent_id]['subcategories'] ?? [], [$map[$category->id]]);
                } elseif (intval($category->level) === 0) {
                    $tree[] = $map[$category->id];
                }
            }

            return $tree;
        });
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

    public function getMenu()
    {
        return ['categories' => $this->getCategories()];
    }
}