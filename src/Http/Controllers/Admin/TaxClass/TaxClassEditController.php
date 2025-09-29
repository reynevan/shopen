<?php

namespace Shopen\Http\Controllers\Admin\TaxClass;

use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Shopen\Http\Requests\Admin\TaxClass\UpdateTaxClassRequest;
use Shopen\Http\Resources\Admin\TaxClass\TaxClassResource;
use Shopen\Models\Product\TaxClass;
use Shopen\Repositories\Category\CategoryRepository;
use Shopen\Repositories\Product\ProductAttributeRepository;

readonly class TaxClassEditController
{
    public function __construct(
        protected ProductAttributeRepository $productAttributeRepository,
        protected CategoryRepository $categoryRepository,
    )
    {}

    public function edit(TaxClass $taxClass): Response
    {
        return Inertia::render('Admin/TaxClass/Edit', [
            'taxClass' => TaxClassResource::make($taxClass)
        ]);
    }

    public function update(UpdateTaxClassRequest $request, TaxClass $taxClass): RedirectResponse
    {
        $data = $request->validated();

        $taxClass->update($data);

        return redirect()->to(route('admin.promo-codes.index'))->with('success', 'Klasa podatku została zaktualizowana.');
    }

    public function destroy(TaxClass $taxClass): RedirectResponse
    {
        $taxClass->delete();
        return redirect()->to(route('admin.promo-codes.index'))->with('Klasa podatku została usunięta.');
    }
}