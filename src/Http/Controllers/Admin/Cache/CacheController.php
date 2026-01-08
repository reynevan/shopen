<?php

namespace Shopen\Http\Controllers\Admin\Cache;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Cache;

class CacheController
{
    public function clearCache(): RedirectResponse
    {
        Cache::flush();

        return back()->with('success', 'Cache został wyczyszczony');
    }
}