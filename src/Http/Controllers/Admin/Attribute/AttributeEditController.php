<?php

namespace Shopen\Http\Controllers\Admin\Attribute;

use Inertia\Inertia;
use Inertia\Response;
use Shopen\Http\Resources\Admin\Attribute\AttributeResource;
use Shopen\Models\Attribute\Attribute;
use Shopen\Repositories\Attribute\AttributeRepository;

class AttributeEditController
{

    public function __construct(
        protected readonly AttributeRepository $attributeRepository
    )
    {

    }

    public function edit(Attribute $attribute): Response
    {
        $attribute->load('options');

        return Inertia::render('Admin/Attribute/Edit', [
            'attribute' => AttributeResource::make($attribute),
        ]);
    }

    public function update()
    {

    }
}