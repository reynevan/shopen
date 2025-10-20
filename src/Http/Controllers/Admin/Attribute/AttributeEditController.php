<?php

namespace Shopen\Http\Controllers\Admin\Attribute;

use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Shopen\Http\Requests\Admin\Attribute\UpdateAttributeRequest;
use Shopen\Http\Resources\Admin\Attribute\AttributeResource;
use Shopen\Models\Attribute\Attribute;
use Shopen\Models\Attribute\AttributeOption;
use Shopen\Repositories\Attribute\AttributeRepository;

class AttributeEditController
{

    public function __construct(
        protected readonly AttributeRepository $attributeRepository
    )
    {}

    public function edit(Attribute $attribute): Response
    {
        $attribute->load('options');

        return Inertia::render('Admin/Attribute/Edit', [
            'attribute' => AttributeResource::make($attribute),
        ]);
    }

    public function update(UpdateAttributeRequest $request, Attribute $attribute)
    {
        $attribute->update($request->validated());

        $this->syncAttributeOptions($attribute, $request->options);

    }

    public function destroy(Attribute $attribute): RedirectResponse
    {
        $attribute->delete();
        return back();
    }

    private function syncAttributeOptions(Attribute $attribute, array $options)
    {
        $existingIds = [];

        foreach ($options as $optionData) {
            if (empty($optionData['value'])) {
                continue;
            }
            if (isset($optionData['id'])) {
                $option = AttributeOption::where('id', $optionData['id'])
                    ->where('attribute_id', $attribute->id)
                    ->first();

                if ($option) {
                    $option->update([
                        'value' => $optionData['value'],
                        'color' => $optionData['color'] ?? null,
                    ]);
                    $existingIds[] = $option->id;
                }
            } else {
                $newOption = AttributeOption::create([
                    'attribute_id' => $attribute->id,
                    'value' => $optionData['value']
                ]);
                $existingIds[] = $newOption->id;
            }
        }

        AttributeOption::where('attribute_id', $attribute->id)
            ->whereNotIn('id', $existingIds)
            ->delete();
    }
}