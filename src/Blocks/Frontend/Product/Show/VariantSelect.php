<?php

namespace Shopen\Blocks\Frontend\Product\Show;

use Shopen\Blocks\Block;
use Shopen\Core\Context;
use Shopen\Http\Resources\Attribute\AttributeOptionResource;
use Shopen\Http\Resources\Attribute\AttributeResource;
use Shopen\Models\Attribute\AttributeOption;
use Shopen\Models\Product\Attribute\Value\ProductAttributeInt;
use Shopen\Repositories\Product\ProductRepository;

class VariantSelect extends Block
{
    public function __construct(private readonly Context $context, private readonly ProductRepository $productRepository)
    {
    }

    public function getVariants()
    {
        $product = $this->context->getCurrentProduct();

        return $this->productRepository->getProductVariants($product);
    }

    public function getConfigurableAttributes()
    {
        $product = $this->context->getCurrentProduct();
        $childProductIds = $product->variants->pluck('id')->toArray();
        $attributes = $this->context->getCurrentProduct()->configurableAttributes;
        $data = [];
        foreach ($attributes as $attribute) {
            $usedOptions = ProductAttributeInt::query()
                ->where('attribute_id', $attribute->id)
                ->whereIn('entity_id', $childProductIds)
                ->select('value');
            $options = AttributeOption::query()
                ->whereIn('id', $usedOptions)
                ->get();
            $data[] = [
                'attribute' => [
                    'id' => $attribute->id,
                    'name' => $attribute->name,
                    'code' => $attribute->code,
                ],
                'options' => AttributeOptionResource::collection($options)
            ];
        }
        return $data;
    }

}