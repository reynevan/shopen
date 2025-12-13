<?php

namespace Shopen\Services\Facebook;

use Illuminate\Database\Eloquent\Collection;
use Shopen\Models\Instagram\InstagramPost;

class InstagramPostService
{
    public function getLatest($limit): Collection
    {
        return InstagramPost::query()
            ->with(['media'])
            ->orderBy('timestamp', 'desc')
            ->limit($limit)
            ->get();
    }
}