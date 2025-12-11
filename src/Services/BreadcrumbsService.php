<?php

namespace Shopen\Services;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\URL;
use Shopen\Models\Category\Category;
use Shopen\Models\Product\Product;
use Shopen\Models\UrlRewrite;

class BreadcrumbsService
{
    const CACHE_TTL = 60 * 60;

    protected array $breadcrumbs = [];

    protected array $registeredRoutes = [];

    public function __construct(protected Request $request)
    {}

    public function register(string $routeName, Closure $callback): void
    {
        $this->registeredRoutes[$routeName] = $callback;
    }

    public function generate(): array
    {
        if (!config('app.debug') && Cache::has($this->getPageCacheKey())) {
            return Cache::get($this->getPageCacheKey());
        }
        $this->breadcrumbs = [];
        $this->add('Strona główna', route('home'));

        $currentRoute = $this->request->route();

        if ($currentRoute) {
            $routeName = $currentRoute->getName();
            if ($routeName && isset($this->registeredRoutes[$routeName])) {
                $callback = $this->registeredRoutes[$routeName];

                call_user_func_array($callback, array_merge([$this], $currentRoute->parameters()));

                return $this->finalize();
            }
        }

        $path = $this->request->path() === '/' ? null : $this->request->path();
        if (!$path) {
            return $this->breadcrumbs;
        }

        $urlRewrite = app(UrlService::class)->getRewrite($path);

        if (!$urlRewrite || !$urlRewrite->entity) {
            return $this->breadcrumbs;
        }

        $saveInCache = false;
        switch ($urlRewrite->entity_type) {
            case 'category':
                $this->generateForCategory($urlRewrite->entity);
                $saveInCache = true;
                break;
            case 'product':
                $this->generateForProduct($urlRewrite->entity);
                break;
        }

        $breadcrumbs = $this->finalize();
        if ($saveInCache) {
            Cache::put($this->getPageCacheKey(), $breadcrumbs, self::CACHE_TTL);
        }
        return $breadcrumbs;
    }

    public function remove()
    {
        $this->breadcrumbs = [];
    }

    public function add(string $name, ?string $url = null): void
    {
        $this->breadcrumbs[] = ['name' => $name, 'url' => $url];
    }

    protected function getPageCacheKey()
    {
        return 'breadcrumbs.' . $this->request->path();
    }

    protected function finalize(): array
    {
        if (count($this->breadcrumbs) > 1) {
            $this->breadcrumbs[count($this->breadcrumbs) - 1]['url'] = null;
        }
        return $this->breadcrumbs;
    }

    protected function generateForCategory(Category $category): void
    {
        $cacheKey = 'breadcrumbs.category.' . $category->id;
        $categoryPath = Cache::remember($cacheKey, config('app.debug') ? 0 : self::CACHE_TTL, function () use ($category) {
            $path = [];
            $current = $category;
            while ($current) {
                array_unshift($path, [
                    'name' => $current->getCustomAttribute('name'),
                    'url' => $this->getCategoryUrl($current),
                ]);
                $current = $current->parent;
            }
            return $path;
        });

        $this->breadcrumbs = array_merge($this->breadcrumbs, $categoryPath);
    }

    protected function generateForProduct(Product $product): void
    {
        $lastUrl = session('last_category_page');
        $refererRewrite = null;
        $categoryGenerated = false;

        if ($lastUrl) {
            $refererPath = ltrim(parse_url($lastUrl, PHP_URL_PATH), '/');
            $refererRewrite = UrlRewrite::query()
                ->with('entity')
                ->where('request_path', $refererPath)
                ->where('entity_type', 'category')
                ->first();
        }
        if ($refererRewrite && $refererRewrite->entity) {
            $isProductCategory = $product->categories()->where('categories.id', $refererRewrite->entity_id)->exists();
            if ($isProductCategory) {
                $this->generateForCategory($refererRewrite->entity);
                $categoryGenerated = true;
            }
        }
        if (!$categoryGenerated) {
            $category = $product->categories()
                ->where('is_canonical', true)
                ->orderBy('level', 'desc')
                ->first();
            if ($category) {
                $this->generateForCategory($category);
            }
        }
        $this->add($product->getCustomAttribute('name'), $this->getProductUrl($product));
    }

    protected function getCategoryUrl(Category $category): string
    {
        return URL::to($category->urlRewrite->request_path ?? '#');
    }

    protected function getProductUrl(Product $product): string
    {
        return URL::to($product->urlRewrite->request_path ?? '#');
    }
}