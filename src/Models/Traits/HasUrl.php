<?php

namespace Shopen\Models\Traits;

use Shopen\Models\UrlRewrite;
use Shopen\Services\StoreManager;

trait HasUrl
{
    protected abstract function getEntityType(): string;

    public function urlRewrite()
    {
        $store = app(StoreManager::class)->getCurrentStore();
        return $this
            ->hasOne(UrlRewrite::class, 'entity_id')
            ->where('store_id', $store->id)
            ->where('entity_type', $this->getEntityType());
    }

    public function getUrl()
    {
        $store = app(StoreManager::class)->getCurrentStore();
        $rewrite = $this->urlRewrite;
        if (!$rewrite) {
            return null;
        }
        return $store->full_url . '/' . $rewrite->request_path;
    }


}