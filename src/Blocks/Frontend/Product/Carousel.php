<?php

namespace Shopen\Blocks\Frontend\Product;

use Illuminate\Database\Eloquent\Builder;
use Shopen\Blocks\Block;
use Shopen\Http\Resources\Product\ProductResource;
use Shopen\Models\Product\Product;
use Shopen\Repositories\Product\ProductRepository;

class Carousel extends Block
{
    public function __construct(
        private ProductRepository $productRepository,
        private readonly array $data
    )
    {}

    public function getProducts()
    {
        $query = Product::query()->with('price');
        if ($this->data['category_id'] ?? false) {
            $query->whereHas('categories', function (Builder $query) {
                $query->where('category_id', $this->data['category_id']);
            });
        }
        $products = $query->limit($this->data['limit'] ?? 20)->get();

        $this->productRepository->addAttributesUsedInList($products);

        return ProductResource::collection($products);
    }

    public function getTitle()
    {
        return $this->data['title'] ?? null;
    }
}