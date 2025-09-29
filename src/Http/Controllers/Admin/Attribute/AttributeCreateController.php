<?php

namespace Shopen\Http\Controllers\Admin\Attribute;

use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Shopen\Http\Requests\Admin\Attribute\StoreAttributeRequest;
use Shopen\Models\Attribute\Attribute;
use Shopen\Models\Attribute\AttributeOption;
use Shopen\Repositories\Attribute\AttributeRepository;

class AttributeCreateController
{

    public function __construct(
        protected readonly AttributeRepository $attributeRepository
    )
    {

    }

    public function create(): Response
    {

        return Inertia::render('Admin/Attribute/Create', [
        ]);
    }

    public function store(StoreAttributeRequest $request): RedirectResponse
    {
        $attribute = Attribute::create($request->validated());

        $this->addAttributeOptions($attribute, $request->options);

        return back()->with('success', 'Atrybut został zapisany');
    }

    private function addAttributeOptions(Attribute $attribute, array $options)
    {

        foreach ($options as $optionData) {
            if (empty($optionData['value'])) {
                continue;
            }
            AttributeOption::create([
                'attribute_id' => $attribute->id,
                'value' => $optionData['value'],
                'color' => $optionData['color'] ?? null,
            ]);
        }
    }
}