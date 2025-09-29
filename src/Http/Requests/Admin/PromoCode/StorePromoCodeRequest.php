<?php

namespace Shopen\Http\Requests\Admin\PromoCode;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Shopen\Enums\Promocode\ApplyType;
use Shopen\Enums\Promocode\DiscountType;

class StorePromoCodeRequest extends FormRequest
{

    public function authorize(): bool
    {
        return Auth::guard('admin')->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'code' => ['required', 'string'],
            'name' => ['required', 'string'],
            'description' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
            'applies_to_discounted' => ['nullable', 'boolean'],
            'discount_type' => ['required', 'in:' . implode(',', array_keys(DiscountType::options()))],
            'discount_value' => ['required', 'numeric'],
            'max_discount_amount' => ['nullable', 'numeric'],
            'minimum_order_value' => ['nullable', 'numeric'],
            'applies_to' => ['required', 'in:' . implode(',', array_keys(ApplyType::options()))],
            'for_logged_users_only' => ['nullable', 'boolean'],
            'usage_limit' => ['nullable', 'numeric'],
            'valid_from' => ['nullable', 'date'],
            'valid_to' => ['nullable', 'date'],
            'attributes' => ['nullable', 'array'],
            'categories' => ['nullable', 'array'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => (bool) $this->input('is_active'),
            'applies_to_discounted' => (bool) $this->input('applies_to_discounted'),
            'for_logged_users_only' => (bool) $this->input('for_logged_users_only'),
            'minimum_order_value' => (int) $this->input('minimum_order_value'),
        ]);
    }
}
