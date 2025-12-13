<?php

namespace Shopen\Services;

use Illuminate\Support\Facades\Cache;
use Shopen\Models\TextSlide\TextSlide;

class TextSlidesService
{
    const CACHE_KEY = 'text-slides';

    public function getAll()
    {
        return Cache::rememberForever(self::CACHE_KEY, function () {
           return TextSlide::query()->get();
        });
    }

    public function clearCache()
    {
        Cache::forget(self::CACHE_KEY);
    }
}