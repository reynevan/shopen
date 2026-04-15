<?php

namespace Shopen\Jobs;

use Shopen\Models\Store;
use Shopen\Services\StoreManager;

class MakeSearchable extends \Laravel\Scout\Jobs\MakeSearchable
{
    public function handle()
    {
        if (count($this->models) === 0) {
            return;
        }
        foreach (Store::all() as $store) {
            app(StoreManager::class)->setCurrentStore($store);
            $this->models->first()->makeSearchableUsing($this->models)->first()->searchableUsing()->update($this->models);
        }
    }
}