<?php

namespace Shopen\Http\Controllers\Admin\Attribute;

use Inertia\Inertia;
use Inertia\Response;
use Shopen\Http\Resources\Admin\Attribute\AttributeResource;
use Shopen\Repositories\Attribute\AttributeRepository;

class AttributeIndexController
{

    public function __construct(
        protected readonly AttributeRepository $attributeRepository
    )
    {

    }

    public function index(): Response
    {
        $attributes = $this->attributeRepository->getPaginated(request('sort', 'id'), request('dir', 'asc'), request('q'));

        return Inertia::render('Admin/Attribute/Index', [
            'attributes' => AttributeResource::collection($attributes),
            'sort' => request('sort', 'id'),
            'dir' => request('dir', 'desc'),
            'q' => request('q')
        ]);
    }
}