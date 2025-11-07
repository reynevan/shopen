<?php

namespace Shopen\Services;

use Illuminate\Support\Facades\Cache;
use Shopen\Models\TextSlide\TextSlide;

class TextSlidesService
{
    const CACHE_KEY = 'text-slides';

    public function getAll()
    {
        return Cache::remember(self::CACHE_KEY, config('app.debug') ? 0 : 60 * 24, function () {
           return TextSlide::query()->get();
        });
    }

    public function clearCache()
    {
        Cache::forget(self::CACHE_KEY);
    }
}