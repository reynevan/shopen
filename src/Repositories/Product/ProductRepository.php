<?php

namespace Shopen\Repositories\Product;

use App\Support\ProductSorting\ProductSortRegistry;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use Shopen\Models\Category\Category;
use Shopen\Models\Product\Price\ProductPriceRule;
use Shopen\Models\Product\Product;
use Shopen\Services\CustomAttributesService;
use Shopen\Services\SearchService\SearchService;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class ProductRepository
{
    public function __construct(
        protected readonly ProductAttributeRepository $attributeRepository,
        protected readonly ProductSortRegistry        $productSortRegistry,
        protected readonly CustomAttributesService    $customAttributesService,
        protected readonly SearchService              $searchService,
    )
    {
    }

    public function getById($id)
    {
        return Product::query()->where('id', $id)->first();
    }

    public function getAll(): Collection
    {
        return Product::query()->get();
    }

    public function getPaginated($sortField, $sortDir, $searchQuery = null, $attributesToLoad = [])
    {
        $products = Product::query()
            ->select('products.*')
            ->when($searchQuery, function (Builder $query) use ($searchQuery) {
                $query
                    ->whereLike('sku', '%' . $searchQuery . '%')
                    ->filterByAttribute('name', '%' . $searchQuery . '%', 'OR');
            })
            ->with(['price', 'urlRewrite', 'media'])
            ->sort($sortField, $sortDir)
            ->paginate()
            ->withQueryString();

        if (count($attributesToLoad)) {
            $this->customAttributesService->loadAttributesToCollection($products, $attributesToLoad);
        } else {
            $this->customAttributesService->loadUsedInListAttributesToCollection($products);
        }
        return $products;
    }

    public function getMatchingForPriceRule(ProductPriceRule $productPriceRule)
    {
        return $this->getQueryMatchingForPriceRule($productPriceRule)->get();
    }

    public function getQueryMatchingForPriceRule(ProductPriceRule $productPriceRule)
    {
        return Product::query()
            ->where(function (Builder $query) use ($productPriceRule) {
                foreach ($productPriceRule->conditions['attributes'] as $attrCondition) {
                    $attribute = $this->attributeRepository->getById($attrCondition['attribute_id']);
                    $query->filterByAttribute($attribute, $attrCondition['value'], 'or');
                }
            })
            ->whereHas('categories', function ($query) use ($productPriceRule) {
                $query->whereIn('categories.id', $productPriceRule->conditions['categories']);
            });
    }

    public function getProductVariants(Product $product)
    {
        if (!$product->parent_id && !$product->isConfigurable()) {
            return [];
        }

        $parent = $product->isConfigurable() ? $product : $product->parent;
        $configurableAttributes = $parent->configurableAttributes;
        $variants = Product::query()
            ->with(['urlRewrite'])
            ->where('parent_id', $parent->id)
            ->get();

        return $configurableAttributes->map(function ($attribute) use ($variants, $product, $configurableAttributes) {
            $filteredVariants = $variants->filter(function ($variant) use ($product, $attribute, $configurableAttributes) {
                foreach ($configurableAttributes as $attr) {
                    if ($attr->id === $attribute->id) {
                        continue;
                    }
                    if ($variant->getAttribute($attr->code) !== $product->getAttribute($attr->code)) {
                        return false;
                    }
                }
                return true;
            })->map(function ($variant) use ($attribute, $product) {
                return [
                    'id' => $variant->id,
                    'url' => $variant->getUrl(),
                    'is_selected' => $variant->id === $product->id,
                    'attribute_value' => $variant->getAttributeTextValue($attribute->code),
                ];
            })->values();

            return [
                'attribute' => $attribute,
                'products' => $filteredVariants,
            ];
        })->toArray();
    }

    public function loadAttributes(Product $product)
    {
        $attributes = $this->attributeRepository->getAll();
        foreach ($attributes as $attribute) {
            $product->loadAttribute($attribute);
        }
    }

    public function addAttributesUsedInList($products)
    {
        $attributes = $this->attributeRepository->getUsedInList();

        foreach ($attributes as $attribute) {
            if ($attribute->backend_type === 'multiselect') {
                $values = $attribute->getValueModel()::query()
                    ->whereIn('entity_id', $products->pluck('id')->merge($products->pluck('parent_id'))->unique()->toArray())
                    ->where('attribute_id', $attribute->id)
                    ->get()
                    ->groupBy('entity_id')
                    ->map(function ($items) {
                        return $items->pluck('value')->all();
                    })
                    ->toArray();
            } else {
                $values = $attribute->getValueModel()::query()
                    ->whereIn('entity_id', $products->pluck('id')->merge($products->pluck('parent_id'))->unique()->toArray())
                    ->where('attribute_id', $attribute->id)
                    ->get()
                    ->pluck('value', 'entity_id')
                    ->toArray();
            }
            foreach ($products as $product) {
                $product->setCustomAttribute($attribute->code, $values[$product->id] ?? $values[$product->parent_id] ?? null);
            }
        }
    }

    public function addThumbnails($products): void
    {
        $thumbnails = [];
        $media = Media::query()
            ->where('model_type', Product::class)
            ->whereIn('model_id', $products->pluck('id')->merge($products->pluck('parent_id'))->unique()->toArray())
            ->orderBy('order_column')
            ->get();
        foreach ($media as $image) {
            if (count($thumbnails[$image->model_id] ?? []) >= config('shopen.product.thumbnail.max_images_count')) {
                continue;
            }
            $thumbnails[$image->model_id][] = [
                'thumbnail' => $image->getUrl('thumbnail'),
                'thumbnail_mobile' => $image->getUrl('thumbnail_mobile')
            ];
        }
        foreach ($products as $product) {
            $product->setThumbnails($thumbnails[$product->id] ?? $thumbnails[$product->parent_id] ?? []);
        }
    }

    public function getRelatedProducts(Product $product)
    {
        $productIds = $product->relatedProducts->pluck('id')->unique()->toArray();
        if (!count($productIds)) {
            return new Collection();
        }
        return $this->searchService->setIds($productIds)->getProducts()->sortedProducts($productIds);
    }
}