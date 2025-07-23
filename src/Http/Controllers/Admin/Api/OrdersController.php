<?php

namespace Shopen\Http\Controllers\Admin\Api;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Shopen\Http\Resources\Admin\Order\OrderResource;
use Shopen\Http\Resources\Admin\Product\ProductResource;
use Shopen\Jobs\RecalculateProductPrice;
use Shopen\Models\Order\Order;
use Shopen\Models\Product\Product;
use Shopen\Repositories\Order\OrderRepository;

class OrdersController
{

    public function __construct(
        private readonly OrderRepository $orderRepository,
    )
    {}

    public function index()
    {
        $sortField = request('sort', 'id');
        $sortDir = request('dir', 'desc');
        $orders = $this->orderRepository->all($sortField, $sortDir)->paginate();


        return OrderResource::collection($orders);
    }

    public function show(Product $product)
    {
        $this->customAttributesService->loadAllAttributes($product);
        $product->load('price');
        return ProductResource::make($product);
    }

    public function update(Product $product)
    {
        DB::beginTransaction();
        try {
            $data = request()->post('product');

            $images = request()->post('images');

            $price = $data['price'];

            $product->sku = $data['sku'];
            $product->uses_stock = $data['uses_stock'] ?? false;
            $product->stock_qty = $data['stock_qty'] ?? 0;

            foreach ($data['attributes'] as $key => $value) {
                if ($value) {
                    $product->setCustomAttribute($key, $value);
                }
            }
            $product->save();

            $product->categories()->sync($data['category_ids'] ?? []);

            $product->setPrice($price);

            RecalculateProductPrice::dispatch($product->id);

            $this->updateImages($product, $images);

            DB::commit();
        } catch (\Exception $e) {
            Log::error($e);
            DB::rollBack();
        }

    }
}
