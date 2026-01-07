<?php

namespace Shopen\Services;

use Shopen\Models\UrlRewrite;

class UrlService
{
    protected array $rewrites = [];

    public function getRewrite($path)
    {
        if ($this->rewrites[$path] ?? false) {
            return $this->rewrites[$path];
        }

        $urlRewrite = UrlRewrite::query()
            ->with('entity')
            ->where('request_path', $path)
            ->first();

        $this->rewrites[$path] = $urlRewrite;

        return $urlRewrite;
    }
}