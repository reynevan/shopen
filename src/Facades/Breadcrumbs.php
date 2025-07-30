<?php

namespace Shopen\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static void register(string $routeName, \Closure $callback)
 * @method static array generate()
 *
 * @see \Shopen\Services\BreadcrumbsService
 */
class Breadcrumbs extends Facade
{

    protected static function getFacadeAccessor(): string
    {
        return 'breadcrumbs';
    }
}