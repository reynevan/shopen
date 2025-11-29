<?php

namespace Shopen\Services;

use Illuminate\Support\Facades\Cache;
use Shopen\Models\Config\Config;

class ConfigService
{
    private const CACHE_TTL = 60 * 60;

    public function save($path, $value)
    {
        Config::query()->updateOrCreate(['path' => $path], ['value' => $value]);
    }

    public function get($path)
    {
        return Cache::remember($path, config('app.debug') ? 0 : self::CACHE_TTL, function() use ($path) {
           return Config::query()->where('path', $path)->first()?->value;
        });
    }
}