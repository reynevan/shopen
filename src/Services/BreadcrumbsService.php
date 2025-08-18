<?php

namespace Shopen\Services;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Shopen\Models\Category\Category;
use Shopen\Models\Product\Product;
use Shopen\Models\UrlRewrite;

class BreadcrumbsService
{
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

        $urlRewrite = UrlRewrite::with('entity')
            ->where('request_path', $path)
            ->first();

        if (!$urlRewrite || !$urlRewrite->entity) {
            return $this->breadcrumbs;
        }

        switch ($urlRewrite->entity_type) {
            case 'category':
                $this->generateForCategory($urlRewrite->entity);
                break;
            case 'product':
                $this->generateForProduct($urlRewrite->entity);
                break;
        }

        return $this->finalize();
    }

    public function remove()
    {
        $this->breadcrumbs = [];
    }

    public function add(string $name, ?string $url): void
    {
        $this->breadcrumbs[] = ['name' => $name, 'url' => $url];
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
        $categoryPath = [];
        $current = $category;
        while ($current) {
            array_unshift($categoryPath, [
                'name' => $current->getCustomAttribute('name'),
                'url' => $this->getCategoryUrl($current),
            ]);
            $current = $current->parent;
        }
        $this->breadcrumbs = array_merge($this->breadcrumbs, $categoryPath);
    }

    protected function generateForProduct(Product $product): void
    {
        $refererUrl = $this->request->header('referer');
        $refererRewrite = null;

        if ($refererUrl) {
            $refererPath = ltrim(parse_url($refererUrl, PHP_URL_PATH), '/');
            $refererRewrite = UrlRewrite::query()
                ->with('entity')
                ->where('request_path', $refererPath)
                ->where('entity_type', 'category')
                ->first();
        }

        if ($refererRewrite && $refererRewrite->entity) {
            $this->generateForCategory($refererRewrite->entity);
        } else {
            // Logika fallback...
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