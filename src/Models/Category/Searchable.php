<?php

namespace Shopen\Models\Category;


use Shopen\Models\Store;
use Shopen\Services\StoreManager;

trait Searchable
{
    use \Elastic\ScoutDriverPlus\Searchable;

    public function shouldBeSearchable(): bool
    {
        return !!$this->is_active;
    }

    public function toSearchableArray()
    {
        $stores = Store::query()->get();
        $data = [
            'id' => $this->id,
            'parent_id' => $this->parent_id,
            'products_count' => $this
                ->products()
                ->where('visible_individually', true)
                ->filterByAttribute('is_active', true)
                ->count(),
        ];
        foreach ($stores as $store) {
            app(StoreManager::class)->setCurrentStore($store);
            $data['name_' . $store->code] = $this->getCustomAttribute('name');
            $data['url_key_' . $store->code] = $this->getUrl(false);
            $this->clearCustomAttributes();
        }

        return $data;
    }
}