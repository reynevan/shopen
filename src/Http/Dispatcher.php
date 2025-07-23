<?php

namespace Shopen\Http;

use Illuminate\Container\Container;
use Illuminate\Routing\ResolvesRouteDependencies;
use ReflectionMethod;
use Shopen\Http\Controllers\Frontend\Category\CategoryShowController;
use Shopen\Http\Controllers\Frontend\Product\ProductShowController;
use Shopen\Models\Category\Category;
use Shopen\Models\Product\Product;
use Shopen\Models\UrlRewrite;

class Dispatcher
{
    use ResolvesRouteDependencies;

    protected Container $container;

    public function dispatch() {
        $url = url()->current();
        $url = str_replace(['https://', 'http://'], '', $url);
        $parts = explode('/', $url);
        $domain = array_shift($parts);
        $requestPath = implode('/', $parts);
        $urlRewrite = UrlRewrite::query()->where('request_path', $requestPath)->first();
        if (!$urlRewrite) {
            abort(404);
        }
        $this->container = Container::getInstance();
        if ($urlRewrite->entity_type === 'product') {
            $controller = $this->container->make(ProductShowController::class);
            $product = Product::query()->find($urlRewrite->entity_id);
            $parameters = $this->getParameters([$product], $controller, 'index');
            return $controller->index(...$parameters);
        }
        if ($urlRewrite->entity_type === 'category') {
            $controller = $this->container->make(CategoryShowController::class);
            $category = Category::query()->find($urlRewrite->entity_id);
            $parameters = $this->getParameters([$category], $controller, 'index');
            return $controller->index(...$parameters);
        }

    }

    protected function getParameters(array $parameters, $instance, $method)
    {
        return $this->resolveMethodDependencies(
            $parameters, new ReflectionMethod($instance, $method)
        );
    }
}