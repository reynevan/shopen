<?php

namespace Shopen\Facades;

use Illuminate\Support\Facades\Facade;
use Shopen\Services\StoreManager;

class Store extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return StoreManager::class;
    }
}