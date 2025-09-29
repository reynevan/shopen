<?php

namespace Shopen\Services\Base;

use Illuminate\Support\Facades\Http;
use Shopen\Models\Product\Product;
use Shopen\Repositories\Product\ProductRepository;

class BaseService
{
    protected $url;
    protected $key;

    public function __construct(
        protected ProductRepository $productRepository
    )
    {
        $this->url = config('services.base.url');
        $this->key = config('services.base.key');
    }

    public function request(string $method, array $params = [])
    {
        $response = Http::asForm()->post($this->url, [
            'token'      => $this->key,
            'method'     => $method,
            'parameters' => json_encode($params),
        ]);

        return $response->json();
    }

    // np. pobieranie zamówień
    public function getOrders()
    {
        return $this->request('getOrders'/*, [
            'date_confirmed_from' => strtotime('-7 days'),
        ]*/);
    }

    // np. pobieranie listy produktów
    public function getInventory()
    {
        return $this->request('getInventoryProductsList', [
            'inventory_id' => 72890, // ID magazynu w Base
        ]);
    }

    public function uploadProducts()
    {
        $products = $this->productRepository->getAll();
        foreach ($products as $product) {
            $this->uploadProduct($product);
        }
    }

    public function uploadProduct(Product $product)
    {
        $data = [
            'inventory_id' => 72890,
            'ean' => $product->ean,
            'sku' => $product->sku,
            'tax_rate' => $product->getTaxRate(),
            'weight' => $product->weight,
            'height' => $product->height,
            'width' => $product->width,
            'length' => $product->length,
        ];
        if ($product->base_product_id) {
            $data['product_id'] = $product->base_product_id;
        }
        if ($product->parent_id && $product->parent?->base_product_id) {
            $data['parent_id'] = $product->parent?->base_product_id;
        }
    }
}