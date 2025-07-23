<?php

namespace Shopen\Http\Controllers\Frontend\Api;

use Shopen\Models\Banner\Banner;

class BannerTrackController
{
    public function __invoke(Banner $banner)
    {
        $banner->timestamps = false;
        $banner->increment('click_count');
        return response()->noContent();
    }
}