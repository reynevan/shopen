<?php

namespace Shopen\Http\Controllers\Admin\Api;

use Shopen\Http\Resources\Attribute\AttributeResource;
use Shopen\Repositories\Attribute\AttributeRepository;
use Shopen\Repositories\Category\CategoryAttributeRepository;
use Shopen\Repositories\Product\ProductAttributeRepository;

class AttributesController
{
    public function __construct(
        private readonly AttributeRepository $attributeRepository,
        private readonly ProductAttributeRepository $productAttributeRepository,
        private readonly CategoryAttributeRepository $categoryAttributeRepository,
    )
    {

    }

    public function index()
    {
        $attributes = $this->attributeRepository->getAll();

        return AttributeResource::collection($attributes);
    }

    public function categories()
    {
        $attributes = $this->categoryAttributeRepository->getAll();

        return AttributeResource::collection($attributes);
    }

    public function products()
    {
        $attributes = $this->productAttributeRepository->getAll();

        return AttributeResource::collection($attributes);
    }
}
