<?php

namespace Shopen\Http\Controllers\Admin\PromoCode;

use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Shopen\Http\Requests\Admin\PromoCode\StorePromoCodeRequest;
use Shopen\Http\Resources\Admin\Category\CategoryResource;
use Shopen\Http\Resources\Admin\PromoCode\PromoCodeResource;
use Shopen\Http\Resources\Attribute\AttributeResource;
use Shopen\Models\PromoCode;
use Shopen\Repositories\Category\CategoryRepository;
use Shopen\Repositories\Product\ProductAttributeRepository;

readonly class PromoCodeCreateController
{
    public function __construct(
        protected ProductAttributeRepository $productAttributeRepository,
        protected CategoryRepository $categoryRepository,
    )
    {}

    public function create(): Response
    {
        $promoCode = new PromoCode();

        return Inertia::render('Admin/PromoCode/Edit', [
            'promoCode' => PromoCodeResource::make($promoCode),
            'attributes' => fn () => AttributeResource::collection($this->productAttributeRepository->getAll()),
            'categories' => fn () => CategoryResource::collection($this->categoryRepository->getAll(0)),
        ]);
    }

    public function store(StorePromoCodeRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['conditions_serialized'] = json_encode(
            [
                'attributes' => request()->post('attributes'),
                'categories' => request()->post('categories'),
            ]
        );

        PromoCode::create($data);

        return redirect()->to(route('admin.promo-codes.index'));
    }
}