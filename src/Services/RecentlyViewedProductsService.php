<?php

namespace Shopen\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Shopen\Models\Product\Product;
use Shopen\Models\Product\UserProductView;
use Shopen\Services\SearchService\SearchService;

class RecentlyViewedProductsService
{
    public function __construct(
        protected SearchService $searchService,
    )
    {}

    const SESSION_KEY = 'recently_viewed_products';
    const MAX_ITEMS = 10;

    public function add(Product $product): void
    {
        if (Auth::check()) {
            UserProductView::query()->updateOrInsert(
                ['user_id' => Auth::id(), 'product_id' => $product->id],
                ['viewed_at' => now()]
            );

            $idsToKeep = UserProductView::query()
                ->where('user_id', Auth::id())
                ->orderByDesc('viewed_at')
                ->limit(self::MAX_ITEMS)
                ->pluck('id');

            UserProductView::query()
                ->where('user_id', Auth::id())
                ->whereNotIn('id', $idsToKeep)
                ->delete();
        } else {
            $productIds = session(self::SESSION_KEY, []);
            array_unshift($productIds, $product->id);
            $productIds = array_slice(array_unique($productIds), 0, self::MAX_ITEMS);
            session([self::SESSION_KEY => $productIds]);
        }
    }


    public function get($except = null): Collection
    {
        $productIds = $this->getProductIds();

        if ($except !== null) {
            $productIds = array_values(array_diff($productIds, (array) $except));
        }

        if (empty($productIds)) {
            return collect();
        }

        return $this->searchService->setIds($productIds)->getProducts()->sortedProducts($productIds);
    }


    protected function getProductIds(): array
    {
        $user = Auth::user();
        if ($user) {
            return UserProductView::query()
                ->where('user_id', $user->id)
                ->orderByDesc('viewed_at')
                ->limit(self::MAX_ITEMS)
                ->pluck('product_id')
                ->toArray();
        }
        return session(self::SESSION_KEY, []);
    }
}