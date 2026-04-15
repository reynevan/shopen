<?php

namespace Shopen\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Shopen\Services\StoreManager;

class RouteServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        $this->routes(function () {
            $storeManager = app(StoreManager::class);
            $resolved = $storeManager->resolveStore();

            if (!$resolved['store']) {
                abort(404, 'Store not found');
            }

            if ($resolved['redirect']) {
                $store = $resolved['store'];
                redirect()->to($store->url . '/' . $store->url_code . '/' . request()->path())->send();
                return;
            }

            $storeManager->setCurrentStore($resolved['store']);

            // Teraz definiujemy routes z odpowiednim prefixem
            $prefix = $storeManager->getRoutePrefix();

            Route::prefix($prefix)
                ->group(base_path('routes/web.php'));

            Route::prefix($prefix)
                ->group(base_path('routes/api.php'));
        });
    }
}