<?php

namespace Shopen\Services;

use Illuminate\Support\Facades\Cache;
use Shopen\Models\Config\Config;

class ConfigService
{
    public function save($path, $value)
    {
        Config::query()->updateOrCreate(['path' => $path], ['value' => $value]);
    }

    public function get($path)
    {
        return Cache::rememberForever($path, function() use ($path) {
           return Config::query()->where('path', $path)->first()?->value;
        });
    }
}