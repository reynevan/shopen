<?php

namespace Shopen\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Shopen\Services\StoreManager;

class StoreDetector
{
    public function handle(Request $request, Closure $next)
    {
        $resolved = app(StoreManager::class)->resolveStore();
        $store = $resolved['store'];

        if (!$store) {
            abort(404, 'Store not found');
        }

        if ($resolved['redirect']) {
            return redirect()->to($store->url . '/' . $store->url_code . '/' . $request->path());
        }

        app(StoreManager::class)->setCurrentStore($store);

        return $next($request);
    }
}