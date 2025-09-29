<?php

namespace Shopen\Http\Controllers\Admin\TaxClass;

use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Shopen\Http\Requests\Admin\TaxClass\StoreTaxClassRequest;
use Shopen\Models\Product\TaxClass;
use Shopen\Repositories\Category\CategoryRepository;
use Shopen\Repositories\Product\ProductAttributeRepository;

readonly class TaxClassCreateController
{
    public function __construct(
        protected ProductAttributeRepository $productAttributeRepository,
        protected CategoryRepository $categoryRepository,
    )
    {}

    public function create(): Response
    {
        return Inertia::render('Admin/TaxClass/Create', []);
    }

    public function store(StoreTaxClassRequest $request): RedirectResponse
    {
        $data = $request->validated();

        TaxClass::create($data);

        return redirect()->to(route('admin.promo-codes.index'));
    }
}