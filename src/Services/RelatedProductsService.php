<?php

namespace Shopen\Services;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Shopen\Services\SearchService\SearchService;

class RelatedProductsService
{
    public function __construct(
        protected readonly CartService   $cartService,
        protected readonly SearchService $searchService,
    )
    {
    }

    public function getCrossSellProducts(): Collection
    {
        $cartProducts = $this->cartService->getCart()->items;
        $cartProductsIds = $cartProducts->map(fn($item) => $item->product_id);
        $crossSellProductsIds = DB::table('product_cross_sells')
            ->whereIn('product_id', $cartProductsIds)
            ->get()
            ->pluck('cross_sell_product_id')
            ->unique()
            ->values()
            ->toArray();
        if (!count($crossSellProductsIds)) {
            return new Collection();
        }
        return $this->searchService->setIds($crossSellProductsIds)->getProducts()->products();
    }
}