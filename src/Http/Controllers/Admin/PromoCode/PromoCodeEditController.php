<?php

namespace Shopen\Http\Controllers\Admin\PromoCode;

use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Shopen\Http\Requests\Admin\PromoCode\UpdatePromoCodeRequest;
use Shopen\Http\Resources\Admin\PromoCode\PromoCodeResource;
use Shopen\Http\Resources\Attribute\AttributeResource;
use Shopen\Models\PromoCode\PromoCode;
use Shopen\Models\PromoCode\PromoCodeCoupon;
use Shopen\Repositories\Category\CategoryRepository;
use Shopen\Repositories\Product\ProductAttributeRepository;

readonly class PromoCodeEditController
{
    public function __construct(
        protected ProductAttributeRepository $productAttributeRepository,
        protected CategoryRepository $categoryRepository,
    )
    {}

    public function edit(PromoCode $promoCode): Response
    {
        $promoCode->load('coupons');

        return Inertia::render('Admin/PromoCode/Edit', [
            'promoCode' => fn () => PromoCodeResource::make($promoCode),
            'attributes' => fn () => AttributeResource::collection($this->productAttributeRepository->getAll()),
            'categories' => fn () => $this->categoryRepository->getArray(),
        ]);
    }

    public function update(UpdatePromoCodeRequest $request, PromoCode $promoCode): RedirectResponse
    {
        $data = $request->validated();
        $data['conditions_serialized'] = json_encode(
            [
                'attributes' => request()->post('attributes'),
                'categories' => request()->post('categories'),
            ]
        );
        $promoCode->update($data);

        $codeIds = array_filter(array_map(fn($code) => $code['id'] ?? null, $data['codes']));
        $promoCode->coupons()->whereNotIn('id', $codeIds)->delete();

        foreach ($data['codes'] as $code) {
            if ($code['id'] ?? null) {
                PromoCodeCoupon::query()->where('id', $code['id'])->update(['code' => $code['code']]);
                continue;
            }
            $promoCode->coupons()->create($code);
        }

        return redirect()->to(route('admin.promo-codes.index'));
    }

    public function destroy(PromoCode $promoCode): RedirectResponse
    {
        $promoCode->delete();
        return redirect()->to(route('admin.promo-codes.index'));
    }
}