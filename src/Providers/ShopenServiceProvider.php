<?php

namespace Shopen\Providers;

use Carbon\Carbon;
use Illuminate\Auth\Events\Login;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\Number;
use Illuminate\Support\ServiceProvider;
use Shopen\Console\Commands\CreateAdminUser;
use Shopen\Console\Commands\GenerateSitemap;
use Shopen\Console\Commands\ImportMagento;
use Shopen\Console\Commands\RecalculateProductPrices;
use Shopen\Console\Commands\Reindex;
use Shopen\Console\Commands\SetupAdminUser;
use Shopen\Console\Commands\ShopenInstall;
use Shopen\Console\Commands\SyncInstagramPosts;
use Shopen\Console\Commands\Test;
use Shopen\Core\BlockDirective;
use Shopen\Core\Context;
use Shopen\Core\Payment\PaymentMethodManager;
use Shopen\Core\Shipping\ShippingMethodManager;
use Shopen\Events\Product\Price\ProductPriceRuleUpdated;
use Shopen\Jobs\RecalculateDiscountPrices;
use Shopen\Listeners\MergeGuestShoppingLists;
use Shopen\Models\Address;
use Shopen\Models\Brand\Brand;
use Shopen\Models\Category\Category;
use Shopen\Models\Product\Price\ProductPrice;
use Shopen\Models\Product\Product;
use Shopen\Repositories\Attribute\AttributeRepository;
use Shopen\Repositories\Category\CategoryAttributeRepository;
use Shopen\Repositories\Product\ProductAttributeRepository;
use Shopen\Services\CartService;
use Shopen\Services\CustomAttributesService;
use Shopen\Observers\ProductPriceObserver;
use Shopen\Services\FiltersService;
use Shopen\Services\ShoppingListService;
use Shopen\Services\UrlService;

class ShopenServiceProvider  extends ServiceProvider
{
    public function boot(): void
    {
        if (config('app.log_sql_queries')) {
            DB::listen(function ($query) {
                $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 15);

                $caller = collect($trace)->first(function ($t) {
                    return isset($t['file']) &&
                        !str_contains($t['file'], 'vendor/laravel') &&
                        !str_contains($t['file'], '/Database/');
                });

                $location = sprintf(
                    '%s:%s',
                    basename($caller['file'] ?? 'unknown'),
                    $caller['line'] ?? '?'
                );

                Log::info($query->sql, [
                    'Bindings' => $query->bindings,
                    'Time' => $query->time . 'ms',
                    'Called from' => $location,
                ]);
            });
        }
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');

        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'shopen');

        if ($this->app->runningInConsole()) {
            $this->loadMigrationsFrom(__DIR__ . '/../Database/migrations');
                $this->commands([
                    SetupAdminUser::class,
                    RecalculateProductPrices::class,
                    ShopenInstall::class,
                    Reindex::class,
                    Test::class,
                    GenerateSitemap::class,
                    CreateAdminUser::class,
                    ImportMagento::class,
                    SyncInstagramPosts::class
                ]);
        }
        $months = [
            'styczeń' => 'stycznia',
            'luty' => 'lutego',
            'marzec' => 'marca',
            'kwieceiń' => 'kwietnia',
            'maj' => 'maja',
            'czerwiec' => 'czerwca',
            'lipiec' => 'lipca',
            'sierpień' => 'sierpnia',
            'wrzesień' => 'września',
            'grudzień' => 'grudnia'
        ];

        Carbon::macro('toLocalDateTime', static function () use ($months) {
            $date = self::this()->translatedFormat('j F Y H:i');
            return str_replace(array_keys($months), array_values($months), $date);
        });

        Carbon::macro('toLocalDate', static function () use ($months) {
            $date = self::this()->translatedFormat('j F Y');
            return str_replace(array_keys($months), array_values($months), $date);
        });

        Number::useCurrency(config('app.currency'));
        Number::useLocale(config('app.locale'));

        $this->app->singleton(FiltersService::class, function ($app) {
            return new FiltersService();
        });

        $this->app->singleton(CustomAttributesService::class, function ($app) {
            return new CustomAttributesService(
                $app->make(ProductAttributeRepository::class),
                $app->make(CategoryAttributeRepository::class),
            );
        });

        $this->app->singleton(CartService::class, function ($app) {
            return new CartService(request());
        });

        $this->app->singleton(ShippingMethodManager::class, function ($app) {
            return new ShippingMethodManager();
        });

        $this->app->singleton(PaymentMethodManager::class, function ($app) {
            return new PaymentMethodManager();
        });

        $this->app->singleton(AttributeRepository::class, function ($app) {
            return new AttributeRepository();
        });

        $this->app->singleton(ProductAttributeRepository::class, function ($app) {
            return new ProductAttributeRepository();
        });

        $this->app->singleton(CategoryAttributeRepository::class, function ($app) {
            return new CategoryAttributeRepository();
        });

        $this->app->singleton(ShoppingListService::class, function ($app) {
            return new ShoppingListService();
        });

        $this->app->singleton(UrlService::class, function ($app) {
            return new UrlService();
        });

        Event::listen(ProductPriceRuleUpdated::class, RecalculateDiscountPrices::class);
        Event::listen(Login::class, MergeGuestShoppingLists::class);

        ProductPrice::observe(ProductPriceObserver::class);

        EncryptCookies::except('cart_uuid');

        Paginator::currentPathResolver(function () {
            return '/' . request()->path();
        });

        //Route::model('address', Address::class);

        JsonResource::withoutWrapping();

        Relation::morphMap([
            'product' => Product::class,
            'category' => Category::class,
            'brand' => Brand::class,
        ]);

        Vite::prefetch(concurrency: 6);
    }

}