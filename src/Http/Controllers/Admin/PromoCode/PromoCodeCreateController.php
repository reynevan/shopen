<?php

namespace Shopen\Http\Controllers\Admin\PromoCode;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;
use Shopen\Http\Requests\Admin\PromoCode\StorePromoCodeRequest;
use Shopen\Http\Resources\Admin\PromoCode\PromoCodeResource;
use Shopen\Http\Resources\Attribute\AttributeResource;
use Shopen\Models\PromoCode\PromoCode;
use Shopen\Repositories\Category\CategoryRepository;
use Shopen\Repositories\Product\ProductAttributeRepository;
use Throwable;

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
            'categories' => fn () => $this->categoryRepository->getArray(),
        ]);
    }

    public function store(StorePromoCodeRequest $request): RedirectResponse
    {
        DB::beginTransaction();
        try {
            $data = $request->validated();

            $data['conditions_serialized'] = json_encode(
                [
                    'attributes' => request()->post('attributes'),
                    'categories' => request()->post('categories'),
                ]
            );

            $promoCode = PromoCode::create($data);

            foreach ($data['codes'] as $code) {
                $promoCode->coupons()->create($code);
            }
            DB::commit();

            return redirect()->to(route('admin.promo-codes.index'));
        } catch (Throwable $e) {
            Log::error($e);
            DB::rollBack();
        }
        return back()->with('error', 'Wystąpił błąd przy zapisie kodu.');
    }
}