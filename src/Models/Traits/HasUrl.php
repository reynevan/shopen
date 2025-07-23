<?php

namespace Shopen\Models\Traits;

use Shopen\Models\UrlRewrite;

trait HasUrl
{
    protected abstract function getEntityType(): string;

    public function urlRewrite()
    {
        return $this->hasOne(UrlRewrite::class, 'entity_id')->where('entity_type', $this->getEntityType());
    }

    public function getUrl()
    {
        $rewrite = $this->urlRewrite;
        if (!$rewrite) {
            return null;
        }
        return config('app.url') . $rewrite->request_path;
    }


}