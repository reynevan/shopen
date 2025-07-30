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

    /**
     * Tablica przechowująca zarejestrowane callbacki dla nazwanych tras.
     * Klucz to nazwa trasy, wartość to funkcja (Closure).
     *
     * @var array<string, Closure>
     */
    protected array $registeredRoutes = [];

    public function __construct(protected Request $request)
    {
        // "Strona główna" jest teraz dodawana na początku każdej generacji,
        // co daje większą elastyczność zarejestrowanym callbackom.
    }

    /**
     * Rejestruje generator breadcrumbów dla konkretnej nazwy trasy.
     *
     * @param string $routeName Nazwa trasy (np. 'products.show')
     * @param Closure $callback Funkcja, która generuje breadcrumbs.
     *                          Otrzymuje instancję serwisu i parametry trasy jako argumenty.
     */
    public function register(string $routeName, Closure $callback): void
    {
        $this->registeredRoutes[$routeName] = $callback;
    }

    /**
     * Generuje i zwraca tablicę breadcrumbs.
     *
     * @return array
     */
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

    /**
     * Publiczna metoda 'add', aby można było jej używać w callbackach.
     *
     * @param string $name
     * @param string|null $url
     */
    public function add(string $name, ?string $url): void
    {
        $this->breadcrumbs[] = ['name' => $name, 'url' => $url];
    }

    /**
     * Ustawia URL ostatniego elementu na null, oznaczając go jako aktywny.
     *
     * @return array
     */
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
                'name' => $current->name,
                'url' => $this->getCategoryUrl($current),
            ]);
            $current = $current->parent;
        }
        $this->breadcrumbs = array_merge($this->breadcrumbs, $categoryPath);
    }

    protected function generateForProduct(Product $product): void
    {
        $refererUrl = $this->request->header('referer');
        $categoryFromReferer = null;

        if ($refererUrl) {
            $refererPath = ltrim(parse_url($refererUrl, PHP_URL_PATH), '/');
            $refererRewrite = UrlRewrite::where('request_path', $refererPath)
                ->where('entity_type', 'category')
                ->first();
            if ($refererRewrite) {
                $categoryFromReferer = Category::find($refererRewrite->entity_id);
            }
        }

        if ($categoryFromReferer) {
            $this->generateForCategory($categoryFromReferer);
        } else {
            // Logika fallback...
        }

        $this->add($product->name, $this->getProductUrl($product));
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