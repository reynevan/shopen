<?php

namespace Shopen\Services;

use Illuminate\Support\Facades\Cache;
use Shopen\Models\Config\Config;

class ConfigService
{
    public function save($path, $value): void
    {
        Config::query()->updateOrCreate(['path' => $path], ['value' => $value]);
        Cache::forget('config.' . $path);
        Cache::rememberForever('config.' . $path, fn() => $value);
    }

    public function remove($path): void
    {
        Config::query()->where(['path' => $path])->delete();
        Cache::forget('config.' . $path);
    }

    public function get($path)
    {
        return Cache::rememberForever('config.' . $path, function() use ($path) {
           return Config::query()->where('path', $path)->first()?->value;
        });
    }
}