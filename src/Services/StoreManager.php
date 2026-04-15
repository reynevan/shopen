<?php

namespace Shopen\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Number;
use Shopen\Models\Store;

class StoreManager
{
    protected ?Store $currentStore = null;

    public function setCurrentStore(Store $store): void
    {
        $this->currentStore = $store;
        app()->setLocale($store->language);
        Number::useCurrency($store->currency);
        Number::useLocale($store->language);
    }

    public function getCurrentStore(): ?Store
    {
        return $this->currentStore;
    }

    public function resolveStore()
    {
        $stores = Cache::remember('stores_all', 3600, fn() => Store::all());
        $fullUrl = Request::getSchemeAndHttpHost();
        $segments = Request::segments();
        $firstSegment = $segments[0] ?? null;

        $useCode = config('shopen.web.use_code_in_url', false);

        // 1. Szukamy po URL i url_code (jeśli włączone)
        if ($useCode && $firstSegment) {
            $store = $stores->where('url', $fullUrl)
                ->where('url_code', $firstSegment)
                ->first();
            if ($store) return ['store' => $store, 'redirect' => false];
        }

        // 2. Szukamy po samym URL (dla sklepów bez url_code, np. us.shopen.localhost)
        $storeByUrl = $stores->where('url', $fullUrl)
            ->whereNull('url_code')
            ->first();

        if ($storeByUrl) return ['store' => $storeByUrl, 'redirect' => false];

        // 3. Jeśli nie znaleziono, a use_code jest true -> szukamy domyślnego dla tego URL i przekierowujemy
        $defaultForUrl = $stores->where('url', $fullUrl)->where('is_default', true)->first()
            ?? $stores->where('is_default', true)->first();

        if ($useCode && $defaultForUrl && $defaultForUrl->url_code) {
            return ['store' => $defaultForUrl, 'redirect' => true];
        }

        return ['store' => $defaultForUrl, 'redirect' => false];
    }

    /**
     * Sprawdza czy aktualny Store używa url_code w ścieżce
     */
    public function usesUrlCode(): bool
    {
        return $this->currentStore && !is_null($this->currentStore->url_code);
    }

    /**
     * Zwraca prefix dla routes (np. 'pl', 'en' lub '')
     */
    public function getRoutePrefix(): string
    {
        return $this->usesUrlCode() ? $this->currentStore->url_code : '';
    }
}